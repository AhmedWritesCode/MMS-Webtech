<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Mpdf\Mpdf;

class AdvisorController extends BaseController
{
    /**
     * Get list of advisees/students under supervision
     * GET /api/advisor/{advisorId}/advisees
     */
    public function getAdvisees(Request $request, Response $response, array $args): Response
    {
        try {
            $advisorId = (int)$args['advisorId'];

            // Verify access - only the advisor can see their advisees
            $userRole = $this->getUserRole($request);
            $currentUserId = $this->getUserId($request);
            
            if ($userRole !== 'advisor' || $currentUserId !== $advisorId) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $sql = "
                SELECT 
                    u.id,
                    u.username as matric_number,
                    u.full_name,
                    u.email,
                    aa.assigned_date,
                    COUNT(DISTINCT ce.course_id) as enrolled_courses,
                    AVG(CASE 
                        WHEN sm.marks_obtained IS NOT NULL AND ac.component_type != 'final_exam'
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as avg_continuous_performance,
                    COUNT(DISTINCT CASE WHEN sm.marks_obtained IS NOT NULL THEN sm.id END) as completed_assessments,
                    COUNT(DISTINCT ac.id) as total_assessments
                FROM advisor_assignments aa
                JOIN users u ON aa.student_id = u.id
                LEFT JOIN course_enrollments ce ON u.id = ce.student_id AND ce.status = 'active'
                LEFT JOIN courses c ON ce.course_id = c.id
                LEFT JOIN assessment_components ac ON c.id = ac.course_id AND ac.is_published = TRUE
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = u.id
                WHERE aa.advisor_id = ? AND aa.is_active = 1
                GROUP BY u.id, u.username, u.full_name, u.email, aa.assigned_date
                ORDER BY u.full_name
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$advisorId]);
            $advisees = $stmt->fetchAll();

            // Calculate additional metrics for each advisee
            foreach ($advisees as &$advisee) {
                // Calculate current GPA
                $gpaData = $this->calculateStudentGPA($advisee['id']);
                $advisee['current_gpa'] = $gpaData['gpa'];
                $advisee['total_credit_hours'] = $gpaData['total_credits'];
                
                // Calculate completion rate
                $advisee['completion_rate'] = $advisee['total_assessments'] > 0 ? 
                    round(($advisee['completed_assessments'] / $advisee['total_assessments']) * 100, 1) : 0;

                // Determine risk level
                $advisee['risk_level'] = $this->calculateRiskLevel($advisee['current_gpa'], $advisee['completion_rate']);
                $advisee['academic_standing'] = $this->getAcademicStanding($advisee['current_gpa']);

                // Get latest notes count
                $advisee['notes_count'] = $this->getNotesCount($advisee['id']);
                
                // Recent activity
                $advisee['last_meeting_date'] = $this->getLastMeetingDate($advisee['id']);
            }

            // Separate at-risk students
            $atRiskStudents = array_filter($advisees, function($advisee) {
                return $advisee['risk_level'] === 'High' || $advisee['risk_level'] === 'Critical';
            });

            return $this->successResponse($response, [
                'advisees' => $advisees,
                'summary' => [
                    'total_advisees' => count($advisees),
                    'at_risk_count' => count($atRiskStudents),
                    'average_gpa' => count($advisees) > 0 ? round(array_sum(array_column($advisees, 'current_gpa')) / count($advisees), 2) : 0
                ]
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch advisees: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get advisee's full mark breakdown across all enrolled courses
     * GET /api/advisor/{advisorId}/advisee/{studentId}/breakdown
     */
    public function getAdviseeBreakdown(Request $request, Response $response, array $args): Response
    {
        try {
            $advisorId = (int)$args['advisorId'];
            $studentId = (int)$args['studentId'];

            // Verify access and relationship
            if (!$this->verifyAdvisorStudentRelationship($advisorId, $studentId)) {
                return $this->errorResponse($response, 'Access denied or invalid advisor-student relationship', 403);
            }

            $sql = "
                SELECT 
                    c.id as course_id,
                    c.course_code,
                    c.course_name,
                    c.credits,
                    c.semester,
                    u_lecturer.full_name as lecturer_name,
                    ac.id as component_id,
                    ac.component_name,
                    ac.component_type,
                    ac.max_marks,
                    ac.weight_percentage,
                    sm.marks_obtained,
                    sm.remarks,
                    sm.graded_at
                FROM course_enrollments ce
                JOIN courses c ON ce.course_id = c.id
                LEFT JOIN course_lecturers cl ON c.id = cl.course_id AND cl.role = 'primary'
                LEFT JOIN users u_lecturer ON cl.lecturer_id = u_lecturer.id
                LEFT JOIN assessment_components ac ON c.id = ac.course_id AND ac.is_published = TRUE
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = ce.student_id
                WHERE ce.student_id = ? AND ce.status = 'active'
                ORDER BY c.semester DESC, c.course_code, ac.component_type, ac.created_at
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$studentId]);
            $results = $stmt->fetchAll();

            // Group by courses
            $courses = [];
            foreach ($results as $row) {
                $courseId = $row['course_id'];
                
                if (!isset($courses[$courseId])) {
                    $courses[$courseId] = [
                        'course_id' => $courseId,
                        'course_code' => $row['course_code'],
                        'course_name' => $row['course_name'],
                        'credits' => $row['credits'],
                        'semester' => $row['semester'],
                        'lecturer_name' => $row['lecturer_name'],
                        'components' => [],
                        'continuous_total' => 0,
                        'final_exam_percentage' => null,
                        'overall_percentage' => null,
                        'grade_letter' => null,
                        'grade_point' => null,
                        'completion_rate' => 0
                    ];
                }

                if ($row['component_id']) {
                    $percentage = $row['marks_obtained'] ? 
                        round(($row['marks_obtained'] / $row['max_marks']) * 100, 2) : null;

                    $component = [
                        'component_id' => $row['component_id'],
                        'component_name' => $row['component_name'],
                        'component_type' => $row['component_type'],
                        'max_marks' => $row['max_marks'],
                        'weight_percentage' => $row['weight_percentage'],
                        'marks_obtained' => $row['marks_obtained'],
                        'percentage' => $percentage,
                        'remarks' => $row['remarks'],
                        'graded_at' => $row['graded_at']
                    ];

                    $courses[$courseId]['components'][] = $component;

                    // Calculate course totals
                    if ($row['marks_obtained'] !== null) {
                        if ($row['component_type'] === 'final_exam') {
                            $courses[$courseId]['final_exam_percentage'] = $percentage;
                        } else {
                            $courses[$courseId]['continuous_total'] += ($row['marks_obtained'] / $row['max_marks']) * $row['weight_percentage'];
                        }
                    }
                }
            }

            // Calculate final course grades and completion rates
            foreach ($courses as &$course) {
                $totalComponents = count($course['components']);
                $completedComponents = count(array_filter($course['components'], function($comp) {
                    return $comp['marks_obtained'] !== null;
                }));

                $course['completion_rate'] = $totalComponents > 0 ? 
                    round(($completedComponents / $totalComponents) * 100, 1) : 0;

                // Calculate overall percentage
                if ($course['continuous_total'] > 0 && $course['final_exam_percentage'] !== null) {
                    $course['overall_percentage'] = round($course['continuous_total'] + ($course['final_exam_percentage'] * 0.3), 2);
                } elseif ($course['continuous_total'] > 0) {
                    $course['overall_percentage'] = round($course['continuous_total'], 2);
                }

                if ($course['overall_percentage']) {
                    $course['grade_letter'] = $this->getGradeLetter($course['overall_percentage']);
                    $course['grade_point'] = $this->getGradePoint($course['overall_percentage']);
                }
            }

            // Get student info
            $studentInfo = $this->getStudentInfo($studentId);

            return $this->successResponse($response, [
                'student_info' => $studentInfo,
                'courses' => array_values($courses),
                'academic_summary' => $this->calculateAcademicSummary(array_values($courses), $studentId)
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch advisee breakdown: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Highlight at-risk students
     * GET /api/advisor/{advisorId}/at-risk-students
     */
    public function getAtRiskStudents(Request $request, Response $response, array $args): Response
    {
        try {
            $advisorId = (int)$args['advisorId'];
            $threshold = $request->getQueryParams()['gpa_threshold'] ?? 2.0;

            // Verify access
            $userRole = $this->getUserRole($request);
            $currentUserId = $this->getUserId($request);
            
            if ($userRole !== 'advisor' || $currentUserId !== $advisorId) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $sql = "
                SELECT 
                    u.id,
                    u.username as matric_number,
                    u.full_name,
                    u.email,
                    COUNT(DISTINCT ce.course_id) as enrolled_courses,
                    COUNT(DISTINCT CASE WHEN sm.marks_obtained IS NOT NULL THEN sm.id END) as completed_assessments,
                    COUNT(DISTINCT ac.id) as total_assessments,
                    AVG(CASE 
                        WHEN sm.marks_obtained IS NOT NULL 
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as avg_performance
                FROM advisor_assignments aa
                JOIN users u ON aa.student_id = u.id
                JOIN course_enrollments ce ON u.id = ce.student_id AND ce.status = 'active'
                JOIN courses c ON ce.course_id = c.id
                LEFT JOIN assessment_components ac ON c.id = ac.course_id AND ac.is_published = TRUE
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = u.id
                WHERE aa.advisor_id = ? AND aa.is_active = 1
                GROUP BY u.id, u.username, u.full_name, u.email
                ORDER BY avg_performance ASC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$advisorId]);
            $students = $stmt->fetchAll();

            $atRiskStudents = [];
            foreach ($students as $student) {
                $gpaData = $this->calculateStudentGPA($student['id']);
                $currentGPA = $gpaData['gpa'];
                
                $completionRate = $student['total_assessments'] > 0 ? 
                    ($student['completed_assessments'] / $student['total_assessments']) * 100 : 0;

                $riskFactors = [];
                $riskLevel = 'Low';

                // GPA risk assessment
                if ($currentGPA < 1.5) {
                    $riskFactors[] = 'Critical GPA (< 1.5)';
                    $riskLevel = 'Critical';
                } elseif ($currentGPA < $threshold) {
                    $riskFactors[] = 'Low GPA (< ' . $threshold . ')';
                    $riskLevel = 'High';
                }

                // Completion rate risk
                if ($completionRate < 50) {
                    $riskFactors[] = 'Low assessment completion (' . round($completionRate, 1) . '%)';
                    if ($riskLevel !== 'Critical') $riskLevel = 'High';
                } elseif ($completionRate < 70) {
                    $riskFactors[] = 'Moderate assessment completion (' . round($completionRate, 1) . '%)';
                    if ($riskLevel === 'Low') $riskLevel = 'Medium';
                }

                // Performance trend risk
                if ($student['avg_performance'] && $student['avg_performance'] < 50) {
                    $riskFactors[] = 'Poor average performance (' . round($student['avg_performance'], 1) . '%)';
                    if ($riskLevel !== 'Critical') $riskLevel = 'High';
                }

                if (!empty($riskFactors)) {
                    $student['current_gpa'] = $currentGPA;
                    $student['completion_rate'] = round($completionRate, 1);
                    $student['risk_level'] = $riskLevel;
                    $student['risk_factors'] = $riskFactors;
                    $student['recommendations'] = $this->generateRecommendations($riskFactors, $currentGPA);
                    $student['last_contact'] = $this->getLastMeetingDate($student['id']);
                    
                    $atRiskStudents[] = $student;
                }
            }

            // Sort by risk level (Critical > High > Medium)
            usort($atRiskStudents, function($a, $b) {
                $riskOrder = ['Critical' => 1, 'High' => 2, 'Medium' => 3];
                return $riskOrder[$a['risk_level']] <=> $riskOrder[$b['risk_level']];
            });

            return $this->successResponse($response, [
                'at_risk_students' => $atRiskStudents,
                'summary' => [
                    'total_at_risk' => count($atRiskStudents),
                    'critical_risk' => count(array_filter($atRiskStudents, fn($s) => $s['risk_level'] === 'Critical')),
                    'high_risk' => count(array_filter($atRiskStudents, fn($s) => $s['risk_level'] === 'High')),
                    'medium_risk' => count(array_filter($atRiskStudents, fn($s) => $s['risk_level'] === 'Medium'))
                ]
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch at-risk students: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Add private notes/meeting records per advisee
     * POST /api/advisor/{advisorId}/advisee/{studentId}/notes
     */
    public function addAdviseNote(Request $request, Response $response, array $args): Response
    {
        try {
            $advisorId = (int)$args['advisorId'];
            $studentId = (int)$args['studentId'];
            $data = json_decode($request->getBody()->getContents(), true);

            // Verify access and relationship
            if (!$this->verifyAdvisorStudentRelationship($advisorId, $studentId)) {
                return $this->errorResponse($response, 'Access denied or invalid advisor-student relationship', 403);
            }

            $required = ['note_type', 'content'];
            $missing = $this->validateRequired($data, $required);
            if (!empty($missing)) {
                return $this->errorResponse($response, 'Missing required fields: ' . implode(', ', $missing));
            }

            $sql = "
                INSERT INTO advisor_notes (advisor_id, student_id, note_type, title, content, meeting_date, follow_up_required)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $advisorId,
                $studentId,
                $data['note_type'],
                $data['title'] ?? null,
                $data['content'],
                $data['meeting_date'] ?? date('Y-m-d'),
                $data['follow_up_required'] ?? false
            ]);

            $noteId = $this->db->lastInsertId();

            // Log the activity
            $this->logActivity($request, 'ADD_ADVISOR_NOTE', 'advisor_notes', $noteId, null, $data);

            return $this->successResponse($response, [
                'note_id' => $noteId,
                'message' => 'Note added successfully'
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to add note: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get advisor notes for a specific advisee
     * GET /api/advisor/{advisorId}/advisee/{studentId}/notes
     */
    public function getAdviseNotes(Request $request, Response $response, array $args): Response
    {
        try {
            $advisorId = (int)$args['advisorId'];
            $studentId = (int)$args['studentId'];

            // Verify access and relationship
            if (!$this->verifyAdvisorStudentRelationship($advisorId, $studentId)) {
                return $this->errorResponse($response, 'Access denied or invalid advisor-student relationship', 403);
            }

            $sql = "
                SELECT 
                    id,
                    note_type,
                    title,
                    content,
                    meeting_date,
                    follow_up_required,
                    follow_up_completed,
                    created_at
                FROM advisor_notes
                WHERE advisor_id = ? AND student_id = ?
                ORDER BY meeting_date DESC, created_at DESC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$advisorId, $studentId]);
            $notes = $stmt->fetchAll();

            return $this->successResponse($response, [
                'notes' => $notes
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch notes: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete advisor note for a specific advisee
     * DELETE /api/advisor/{advisorId}/advisee/{studentId}/notes/{noteId}
     */
    public function deleteAdviseNote(Request $request, Response $response, array $args): Response
    {
        try {
            $advisorId = (int)$args['advisorId'];
            $studentId = (int)$args['studentId'];
            $noteId = (int)$args['noteId'];

            // Verify access and relationship
            if (!$this->verifyAdvisorStudentRelationship($advisorId, $studentId)) {
                return $this->errorResponse($response, 'Access denied or invalid advisor-student relationship', 403);
            }

            // Verify the note belongs to this advisor and student
            $sql = "SELECT id FROM advisor_notes WHERE id = ? AND advisor_id = ? AND student_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$noteId, $advisorId, $studentId]);
            
            if (!$stmt->fetch()) {
                return $this->errorResponse($response, 'Note not found or access denied', 404);
            }

            // Delete the note
            $deleteSql = "DELETE FROM advisor_notes WHERE id = ? AND advisor_id = ? AND student_id = ?";
            $deleteStmt = $this->db->prepare($deleteSql);
            $deleteStmt->execute([$noteId, $advisorId, $studentId]);

            // Log the activity
            $this->logActivity($request, 'DELETE_ADVISOR_NOTE', 'advisor_notes', $noteId, null, ['deleted' => true]);

            return $this->successResponse($response, [
                'message' => 'Note deleted successfully'
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to delete note: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Export student consultation reports
     * GET /api/advisor/{advisorId}/advisee/{studentId}/report
     */
    public function exportConsultationReport(Request $request, Response $response, array $args): Response
    {
        try {
            $advisorId = (int)$args['advisorId'];
            $studentId = (int)$args['studentId'];
            $queryParams = $request->getQueryParams();
            
            $format = $queryParams['format'] ?? 'json';
            $reportType = $queryParams['type'] ?? 'comprehensive';
            $dateRange = $queryParams['date_range'] ?? 'all';
            $includeRecommendations = filter_var($queryParams['include_recommendations'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
            $includeCharts = filter_var($queryParams['include_charts'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
            $title = $queryParams['title'] ?? 'Consultation Report';
            $startDate = $queryParams['start_date'] ?? null;
            $endDate = $queryParams['end_date'] ?? null;

            // Verify access and relationship
            if (!$this->verifyAdvisorStudentRelationship($advisorId, $studentId)) {
                return $this->errorResponse($response, 'Access denied or invalid advisor-student relationship', 403);
            }

            // Get student information
            $studentInfo = $this->getStudentInfo($studentId);
            
            // Get academic performance details (list of courses)
            $coursesData = $this->getAdviseeAcademicSummary($studentId);

            // Calculate the summary from the course data
            $academicSummary = $this->calculateAcademicSummary($coursesData, $studentId);
            
            // Get consultation history with date filtering
            $consultationHistory = $this->getConsultationHistory($advisorId, $studentId, $dateRange, $startDate, $endDate);
            
            // Get advisor information
            $advisorInfo = $this->getAdvisorInfo($advisorId);

            // Build report data based on type
            $reportData = [
                'report_generated' => date('Y-m-d H:i:s'),
                'report_title' => $title,
                'advisor' => $advisorInfo,
                'student' => $studentInfo,
            ];

            // Add sections based on report type
            switch ($reportType) {
                case 'academic':
                    $reportData['academic_summary'] = $academicSummary;
                    $reportData['academic_details'] = $coursesData;
                    break;
                case 'consultation':
                    $reportData['consultation_history'] = $consultationHistory;
                    break;
                case 'summary':
                    $reportData['academic_summary'] = $academicSummary;
                    $reportData['summary_info'] = [
                        'total_meetings' => count($consultationHistory),
                        'last_meeting' => $this->getLastMeetingDate($studentId),
                    ];
                    break;
                case 'comprehensive':
                default:
                    $reportData['academic_summary'] = $academicSummary;
                    $reportData['academic_details'] = $coursesData;
                    $reportData['consultation_history'] = $consultationHistory;
                    break;
            }

            // Add recommendations if requested
            if ($includeRecommendations) {
                $reportData['recommendations'] = $this->generateStudentRecommendations($academicSummary, $consultationHistory);
            }

            // Generate filename
            $filename = $this->generateFilename($studentInfo['full_name'], $format, $reportType);

            if ($format === 'csv') {
                return $this->exportAsCSV($response, $reportData, $filename);
            } elseif ($format === 'pdf') {
                return $this->exportAsPDF($response, $reportData, $filename, $includeCharts);
            } else {
                // JSON format
                return $this->successResponse($response, $reportData);
            }

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to generate report: ' . $e->getMessage(), 500);
        }
    }

    // Helper Methods

    private function calculateStudentGPA(int $studentId): array
    {
        // Use the same logic as StudentPerformanceController.php getStudentFullBreakdown
        $sql = "
            SELECT 
                c.credits as credit_hours,
                AVG(CASE 
                    WHEN ac.component_type != 'final_exam' AND sm.marks_obtained IS NOT NULL 
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                    ELSE NULL 
                END) as continuous_avg,
                AVG(CASE 
                    WHEN ac.component_type = 'final_exam' AND sm.marks_obtained IS NOT NULL 
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                    ELSE NULL 
                END) as final_exam_percentage
            FROM course_enrollments ce
            JOIN courses c ON ce.course_id = c.id
            LEFT JOIN assessment_components ac ON c.id = ac.course_id AND ac.is_published = TRUE
            LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = ce.student_id
            WHERE ce.student_id = ? AND ce.status = 'active'
            GROUP BY c.id, c.credits
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        $courses = $stmt->fetchAll();

        $totalCredits = 0;
        $totalGradePoints = 0;

        foreach ($courses as $course) {
            // Calculate overall course percentage
            $overallPercentage = null;
            if ($course['continuous_avg'] !== null && $course['final_exam_percentage'] !== null) {
                $overallPercentage = round(($course['continuous_avg'] * 0.7) + ($course['final_exam_percentage'] * 0.3), 2);
            } elseif ($course['continuous_avg'] !== null) {
                $overallPercentage = round($course['continuous_avg'] * 0.7, 2);
            }

            $gradePoint = $overallPercentage !== null ? $this->getGradePoint($overallPercentage) : null;
            if ($gradePoint !== null) {
                $totalCredits += $course['credit_hours'];
                $totalGradePoints += $gradePoint * $course['credit_hours'];
            }
        }

        return [
            'gpa' => $totalCredits > 0 ? round($totalGradePoints / $totalCredits, 2) : 0.0,
            'total_credits' => $totalCredits
        ];
    }

    private function calculateRiskLevel(float $gpa, float $completionRate): string
    {
        if ($gpa < 1.5 || $completionRate < 50) return 'Critical';
        if ($gpa < 2.0 || $completionRate < 70) return 'High';
        if ($gpa < 2.5 || $completionRate < 85) return 'Medium';
        return 'Low';
    }

    private function getAcademicStanding(float $gpa): string
    {
        if ($gpa >= 3.5) return 'Dean\'s List';
        if ($gpa >= 3.0) return 'Good Standing';
        if ($gpa >= 2.0) return 'Satisfactory';
        if ($gpa >= 1.5) return 'Academic Warning';
        return 'Academic Probation';
    }

    private function verifyAdvisorStudentRelationship(int $advisorId, int $studentId): bool
    {
        $sql = "SELECT COUNT(*) FROM advisor_assignments WHERE advisor_id = ? AND student_id = ? AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$advisorId, $studentId]);
        return $stmt->fetchColumn() > 0;
    }

    private function getNotesCount(int $studentId): int
    {
        $sql = "SELECT COUNT(*) FROM advisor_notes WHERE student_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchColumn();
    }

    private function getLastMeetingDate(int $studentId): ?string
    {
        $sql = "SELECT MAX(meeting_date) FROM advisor_notes WHERE student_id = ? AND note_type = 'meeting'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchColumn();
    }

    private function getStudentInfo(int $studentId): array
    {
        $sql = "SELECT id, username as matric_number, full_name, email FROM users WHERE id = ? AND user_type = 'student'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetch();
    }

    private function getAdvisorInfo(int $advisorId): array
    {
        $sql = "SELECT id, username, full_name, email FROM users WHERE id = ? AND user_type = 'advisor'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$advisorId]);
        return $stmt->fetch();
    }

    private function calculateAcademicSummary(array $courses, int $studentId): array
    {
        // Use the same GPA calculation as the student dashboard for consistency
        $gpaData = $this->calculateStudentGPA($studentId);
        
        $totalCredits = $gpaData['total_credits'];
        $currentGPA = $gpaData['gpa'];
        $completedCourses = 0;
        $averageCompletion = 0;

        foreach ($courses as $course) {
            if ($course['grade_point']) {
                $completedCourses++;
            }
            $averageCompletion += $course['completion_rate'];
        }

        $averageCompletion = count($courses) > 0 ? round($averageCompletion / count($courses), 1) : 0;

        return [
            'current_gpa' => $currentGPA,
            'total_credits' => $totalCredits,
            'completed_courses' => $completedCourses,
            'total_enrolled' => count($courses),
            'average_completion_rate' => $averageCompletion,
            'academic_standing' => $this->getAcademicStanding($currentGPA)
        ];
    }

    private function generateRecommendations(array $riskFactors, float $gpa): array
    {
        $recommendations = [];

        foreach ($riskFactors as $factor) {
            if (strpos($factor, 'GPA') !== false) {
                if ($gpa < 1.5) {
                    $recommendations[] = 'Immediate academic intervention required - consider course load reduction';
                    $recommendations[] = 'Schedule weekly meetings to monitor progress';
                } else {
                    $recommendations[] = 'Focus on study skills and time management';
                    $recommendations[] = 'Consider tutoring or supplemental instruction';
                }
            }

            if (strpos($factor, 'completion') !== false) {
                $recommendations[] = 'Improve assignment submission consistency';
                $recommendations[] = 'Create structured study schedule';
                $recommendations[] = 'Address potential attendance issues';
            }

            if (strpos($factor, 'performance') !== false) {
                $recommendations[] = 'Review learning strategies and study methods';
                $recommendations[] = 'Connect with course instructors for additional support';
            }
        }

        // Add general recommendations
        $recommendations[] = 'Regular check-ins with academic advisor';
        $recommendations[] = 'Utilize campus academic support resources';

        return array_unique($recommendations);
    }

    private function getConsultationHistory(int $advisorId, int $studentId, string $dateRange = 'all', ?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "
            SELECT 
                id,
                note_type,
                title,
                content,
                meeting_date,
                follow_up_required,
                follow_up_completed,
                created_at
            FROM advisor_notes
            WHERE advisor_id = ? AND student_id = ?
        ";

        $params = [$advisorId, $studentId];
        
        // Add date filtering based on range
        switch ($dateRange) {
            case 'current_semester':
                $sql .= " AND meeting_date >= ? AND meeting_date <= ?";
                $params[] = date('Y-01-01'); // Start of current year
                $params[] = date('Y-12-31'); // End of current year
                break;
            case 'last_6_months':
                $sql .= " AND meeting_date >= ? AND meeting_date <= ?";
                $params[] = date('Y-m-d', strtotime('-6 months'));
                $params[] = date('Y-m-d');
                break;
            case 'last_year':
                $sql .= " AND meeting_date >= ? AND meeting_date <= ?";
                $params[] = date('Y-m-d', strtotime('-1 year'));
                $params[] = date('Y-m-d');
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $sql .= " AND meeting_date >= ? AND meeting_date <= ?";
                    $params[] = $startDate;
                    $params[] = $endDate;
                }
                break;
            case 'all':
            default:
                // No additional filtering needed
                break;
        }

        $sql .= " ORDER BY meeting_date DESC, created_at DESC LIMIT 50";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getAdviseeAcademicSummary(int $studentId): array
    {
        $sql = "
            SELECT 
                c.course_code,
                c.course_name,
                c.credits,
                c.semester,
                AVG(CASE 
                    WHEN ac.component_type != 'final_exam' 
                    THEN (sm.marks_obtained / ac.max_marks) * ac.weight_percentage 
                    ELSE (sm.marks_obtained / ac.max_marks) * 30 
                END) as course_percentage,
                COUNT(sm.id) as completed_assessments,
                COUNT(ac.id) as total_assessments
            FROM course_enrollments ce
            JOIN courses c ON ce.course_id = c.id
            LEFT JOIN assessment_components ac ON c.id = ac.course_id AND ac.is_published = TRUE
            LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = ce.student_id
            WHERE ce.student_id = ? AND ce.status = 'active'
            GROUP BY c.id, c.course_code, c.course_name, c.credits, c.semester
            ORDER BY c.semester DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        $courses = $stmt->fetchAll();

        foreach ($courses as &$course) {
            $course['grade_letter'] = $course['course_percentage'] ? 
                $this->getGradeLetter($course['course_percentage']) : null;
            $course['grade_point'] = $course['course_percentage'] ? 
                $this->getGradePoint($course['course_percentage']) : null;
            $course['completion_rate'] = $course['total_assessments'] > 0 ? 
                round(($course['completed_assessments'] / $course['total_assessments']) * 100, 1) : 0;
        }

        return $courses;
    }

    private function generateStudentRecommendations(array $academicData, array $consultationHistory): array
    {
        $recommendations = [];
        $gpas = array_filter(array_column($academicData, 'grade_point'));
        $averageGPA = count($gpas) > 0 ? array_sum($gpas) / count($gpas) : 0;

        if ($averageGPA < 2.0) {
            $recommendations[] = 'Academic probation - immediate intervention required';
            $recommendations[] = 'Reduce course load for next semester';
            $recommendations[] = 'Mandatory academic skills workshop enrollment';
        } elseif ($averageGPA < 2.5) {
            $recommendations[] = 'Academic warning - enhanced support needed';
            $recommendations[] = 'Weekly progress monitoring';
            $recommendations[] = 'Study skills assessment and training';
        } elseif ($averageGPA < 3.0) {
            $recommendations[] = 'Good progress - maintain current strategies';
            $recommendations[] = 'Consider advanced course opportunities';
        } else {
            $recommendations[] = 'Excellent performance - consider honor programs';
            $recommendations[] = 'Explore research or internship opportunities';
        }

        // Check consultation frequency
        $recentMeetings = array_filter($consultationHistory, function($note) {
            return strtotime($note['meeting_date']) > strtotime('-30 days');
        });

        if (count($recentMeetings) < 2) {
            $recommendations[] = 'Increase frequency of advisor meetings';
        }

        return $recommendations;
    }

    private function exportAsCSV(Response $response, array $data, string $filename): Response
    {
        $csv = "Student Consultation Report\n";
        $csv .= "Generated: " . $data['report_generated'] . "\n";
        $csv .= "Student: " . $data['student']['full_name'] . "\n";
        $csv .= "Matric Number: " . $data['student']['matric_number'] . "\n";
        $csv .= "Advisor: " . $data['advisor']['full_name'] . "\n\n";

        if (isset($data['academic_details'])) {
            $csv .= "Academic Performance\n";
            $csv .= "Course Code,Course Name,Credits,Grade,GPA,Completion Rate\n";
            
            foreach ($data['academic_details'] as $course) {
                $csv .= sprintf(
                    "%s,%s,%d,%s,%.2f,%.1f%%\n",
                    $course['course_code'],
                    $course['course_name'],
                    $course['credits'],
                    $course['grade_letter'] ?? 'N/A',
                    $course['grade_point'] ?? 0,
                    $course['completion_rate']
                );
            }
        }

        if (isset($data['consultation_history'])) {
            $csv .= "\nConsultation History\n";
            $csv .= "Date,Type,Title,Content\n";
            
            foreach ($data['consultation_history'] as $note) {
                $csv .= sprintf(
                    "%s,%s,%s,%s\n",
                    $note['meeting_date'],
                    $note['note_type'],
                    $note['title'] ?? '',
                    str_replace(["\n", "\r", ","], [" ", " ", ";"], $note['content'])
                );
            }
        }

        if (isset($data['recommendations'])) {
            $csv .= "\nRecommendations\n";
            foreach ($data['recommendations'] as $rec) {
                $csv .= "- " . $rec . "\n";
            }
        }

        $response->getBody()->write($csv);
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportAsPDF(Response $response, array $data, string $filename, bool $includeCharts): Response
    {
        // Generate HTML content for the report
        $html = $this->generateReportHTML($data, $includeCharts);

        // Use mPDF to generate real PDF
        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_header' => 10,
                'margin_footer' => 10,
            ]);

            // Set document properties
            $mpdf->SetTitle($data['report_title'] ?? 'Consultation Report');
            $mpdf->SetAuthor($data['advisor']['full_name'] ?? 'Academic Advisor');
            $mpdf->SetCreator('Student Marks System');

            // Write HTML content to PDF
            $mpdf->WriteHTML($html);

            // Output PDF as string
            $pdfContent = $mpdf->Output('', 'S');

            $response->getBody()->write($pdfContent);

            return $response
                ->withHeader('Content-Type', 'application/pdf')
                ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->withHeader('Pragma', 'no-cache')
                ->withHeader('Expires', '0');

        } catch (\Exception $e) {
            // Fallback to error response if PDF generation fails
            return $this->errorResponse($response, 'Failed to generate PDF: ' . $e->getMessage(), 500);
        }
    }

    private function generateReportHTML(array $data, bool $includeCharts): string
    {
        $student = $data['student'];
        $advisor = $data['advisor'];
        $reportTitle = $data['report_title'] ?? 'Consultation Report';
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($reportTitle) . '</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            font-size: 12px;
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #333; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #1e293b;
        }
        .header p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }
        .section { 
            margin-bottom: 25px; 
            page-break-inside: avoid;
        }
        .section h2 { 
            color: #2563eb; 
            border-bottom: 1px solid #e5e7eb; 
            padding-bottom: 8px; 
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .info-grid { 
            display: table; 
            width: 100%; 
            margin: 15px 0; 
            border-collapse: collapse;
        }
        .info-item { 
            display: table-cell;
            background: #f8fafc; 
            padding: 12px; 
            border: 1px solid #e2e8f0;
            vertical-align: top;
            width: 50%;
        }
        .info-label { 
            font-weight: bold; 
            color: #374151; 
            font-size: 11px;
            margin-bottom: 5px;
        }
        .info-value { 
            color: #1f2937; 
            font-size: 12px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0; 
            font-size: 11px;
        }
        th, td { 
            border: 1px solid #d1d5db; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background: #f3f4f6; 
            font-weight: bold; 
            font-size: 11px;
        }
        .recommendations { 
            background: #fef3c7; 
            padding: 12px; 
            border-left: 4px solid #f59e0b; 
            margin: 15px 0;
        }
        .recommendations ul {
            margin: 0;
            padding-left: 20px;
        }
        .recommendations li {
            margin-bottom: 5px;
        }
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            color: #6b7280; 
            font-size: 10px; 
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>';

        // Header
        $html .= '<div class="header">
            <h1>' . htmlspecialchars($reportTitle) . '</h1>
            <p>Generated on: ' . htmlspecialchars($data['report_generated']) . '</p>
        </div>';

        // Student Information
        $html .= '<div class="section">
            <h2>Student Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name:</div>
                    <div class="info-value">' . htmlspecialchars($student['full_name']) . '</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Matric Number:</div>
                    <div class="info-value">' . htmlspecialchars($student['matric_number']) . '</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div class="info-value">' . htmlspecialchars($student['email']) . '</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Advisor:</div>
                    <div class="info-value">' . htmlspecialchars($advisor['full_name']) . '</div>
                </div>
            </div>
        </div>';

        // Academic Performance Summary
        if (isset($data['academic_summary'])) {
            $academic = $data['academic_summary'];
            $html .= '<div class="section">
                <h2>Academic Performance Summary</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Current GPA:</div>
                        <div class="info-value">' . number_format($academic['current_gpa'] ?? 0, 2) . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Academic Standing:</div>
                        <div class="info-value">' . htmlspecialchars($academic['academic_standing'] ?? 'N/A') . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Credits:</div>
                        <div class="info-value">' . ($academic['total_credits'] ?? 0) . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Completed Courses:</div>
                        <div class="info-value">' . ($academic['completed_courses'] ?? 0) . ' / ' . ($academic['total_enrolled'] ?? 0) . '</div>
                    </div>
                </div>
            </div>';
        }

        // Course Details Table
        if (isset($data['academic_details']) && !empty($data['academic_details'])) {
            $html .= '<div class="section">
                <h2>Course Details</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                            <th>Grade</th>
                            <th>GPA</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($data['academic_details'] as $course) {
                $html .= '<tr>
                    <td>' . htmlspecialchars($course['course_code'] ?? '') . '</td>
                    <td>' . htmlspecialchars($course['course_name'] ?? '') . '</td>
                    <td>' . htmlspecialchars($course['credits'] ?? '') . '</td>
                    <td>' . htmlspecialchars($course['grade_letter'] ?? 'N/A') . '</td>
                    <td>' . number_format($course['grade_point'] ?? 0, 2) . '</td>
                    <td>' . number_format($course['completion_rate'] ?? 0, 1) . '%</td>
                </tr>';
            }
            
            $html .= '</tbody></table></div>';
        }

        // Consultation History
        if (isset($data['consultation_history']) && !empty($data['consultation_history'])) {
            $html .= '<div class="section">
                <h2>Consultation History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Follow-up Required</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($data['consultation_history'] as $consultation) {
                $html .= '<tr>
                    <td>' . htmlspecialchars($consultation['meeting_date']) . '</td>
                    <td>' . htmlspecialchars(ucfirst($consultation['note_type'])) . '</td>
                    <td>' . htmlspecialchars($consultation['title']) . '</td>
                    <td>' . ($consultation['follow_up_required'] ? 'Yes' : 'No') . '</td>
                </tr>';
            }
            
            $html .= '</tbody></table></div>';
        }

        // Recommendations
        if (isset($data['recommendations']) && !empty($data['recommendations'])) {
            $html .= '<div class="section">
                <h2>Recommendations</h2>
                <div class="recommendations">
                    <ul>';
            
            foreach ($data['recommendations'] as $recommendation) {
                $html .= '<li>' . htmlspecialchars($recommendation) . '</li>';
            }
            
            $html .= '</ul></div></div>';
        }

        // Footer
        $html .= '<div class="footer">
            <p>This report was generated by the Student Marks System</p>
            <p>Advisor: ' . htmlspecialchars($advisor['full_name']) . ' | Student: ' . htmlspecialchars($student['full_name']) . '</p>
        </div>';

        $html .= '</body></html>';
        
        return $html;
    }

    private function generateFilename(string $studentName, string $format, string $reportType): string
    {
        $cleanName = preg_replace('/[^a-zA-Z0-9\s]/', '', $studentName);
        $cleanName = str_replace(' ', '_', trim($cleanName));
        $timestamp = date('Y-m-d_H-i-s');
        
        $filename = "consultation_report_{$cleanName}_{$reportType}_{$timestamp}.{$format}";
        
        return $filename;
    }

    private function logActivity(Request $request, string $action, string $tableName, int $recordId, ?array $oldValues, array $newValues): void
    {
        try {
            $sql = "
                INSERT INTO audit_log (user_id, action, table_name, record_id, old_values, new_values, ip_address, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $this->getUserId($request),
                $action,
                $tableName,
                $recordId,
                $oldValues ? json_encode($oldValues) : null,
                json_encode($newValues),
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}