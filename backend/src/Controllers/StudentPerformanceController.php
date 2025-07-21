<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class StudentPerformanceController extends BaseController
{
    public function testMethod(Request $request, Response $response, array $args): Response
{
    $response->getBody()->write("It works!");
    return $response;
}
    /**
     * Get student's component-wise marks and total for a specific course
     * GET /api/student/{studentId}/course/{courseId}/marks
     */
    public function getStudentCourseMarks(Request $request, Response $response, array $args): Response
    {
        try {
            $studentId = (int)$args['studentId'];
            $courseId = (int)$args['courseId'];

            // Verify student access (students can only see their own marks)
            $userRole = $this->getUserRole($request);
            $currentUserId = $this->getUserId($request);
            
            if ($userRole === 'student' && $currentUserId !== $studentId) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $sql = "
                SELECT 
                    ac.id as component_id,
                    ac.component_name,
                    ac.component_type,
                    ac.max_marks,
                    ac.weight_percentage,
                    sm.marks_obtained,
                    sm.remarks,
                    sm.graded_at,
                    c.course_code,
                    c.course_name
                FROM assessment_components ac
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = ?
                JOIN courses c ON ac.course_id = c.id
                WHERE ac.course_id = ? AND ac.is_published = TRUE
                ORDER BY ac.component_type, ac.created_at
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$studentId, $courseId]);
            $components = $stmt->fetchAll();

            // Calculate totals
            $continuousTotal = 0;
            $continuousMax = 0;
            $finalExamMarks = null;
            $finalExamMax = 0;

            foreach ($components as &$component) {
                if ($component['component_type'] === 'final_exam') {
                    $finalExamMarks = $component['marks_obtained'];
                    $finalExamMax = $component['max_marks'];
                } else {
                    if ($component['marks_obtained'] !== null) {
                        $continuousTotal += ($component['marks_obtained'] / $component['max_marks']) * $component['weight_percentage'];
                    }
                    $continuousMax += $component['weight_percentage'];
                }

                // Add percentage calculation
                $component['percentage'] = $component['marks_obtained'] ? 
                    round(($component['marks_obtained'] / $component['max_marks']) * 100, 2) : null;
            }

            // Calculate overall percentage
            $overallPercentage = null;
            if ($continuousTotal > 0 && $finalExamMarks !== null) {
                $finalExamPercentage = ($finalExamMarks / $finalExamMax) * 30; // 30% weight
                $overallPercentage = round($continuousTotal + $finalExamPercentage, 2);
            }

            return $this->successResponse($response, [
                'course' => [
                    'course_code' => $components[0]['course_code'] ?? '',
                    'course_name' => $components[0]['course_name'] ?? ''
                ],
                'components' => $components,
                'summary' => [
                    'continuous_assessment' => [
                        'total_percentage' => round($continuousTotal, 2),
                        'max_percentage' => $continuousMax
                    ],
                    'final_exam' => [
                        'marks' => $finalExamMarks,
                        'max_marks' => $finalExamMax,
                        'percentage' => $finalExamMarks ? round(($finalExamMarks / $finalExamMax) * 100, 2) : null
                    ],
                    'overall_percentage' => $overallPercentage,
                    'grade_letter' => $overallPercentage ? $this->getGradeLetter($overallPercentage) : null,
                    'grade_point' => $overallPercentage ? $this->getGradePoint($overallPercentage) : null
                ]
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch marks: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Get student's full mark breakdown across all courses
     * GET /api/student/{studentId}/marks/breakdown
     */
    public function getStudentFullBreakdown(Request $request, Response $response, array $args): Response
    {
        try {
            $studentId = (int)$args['studentId'];

            // Verify access
            $userRole = $this->getUserRole($request);
            $currentUserId = $this->getUserId($request);
            
            if ($userRole === 'student' && $currentUserId !== $studentId) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            // First, get student information
            $studentSql = "
                SELECT 
                    u.id,
                    u.username as matric_number,
                    u.full_name as name,
                    u.email,
                    u.user_type,
                    'Computer Science' as department,
                    'Bachelor of Computer Science' as program,
                    '2024/2025-1' as current_semester
                FROM users u
                WHERE u.id = ? AND u.user_type = 'student'
            ";
            
            $studentStmt = $this->db->prepare($studentSql);
            $studentStmt->execute([$studentId]);
            $studentInfo = $studentStmt->fetch();

            if (!$studentInfo) {
                return $this->errorResponse($response, 'Student not found', 404);
            }

            // Get courses with detailed information
            $sql = "
                SELECT 
                    c.id as course_id,
                    c.course_code,
                    c.course_name,
                    c.credits as credit_hours,
                    c.semester,
                    ce.status as enrollment_status,
                    COUNT(ac.id) as total_components,
                    COUNT(sm.id) as graded_components,
                    AVG(CASE 
                        WHEN ac.component_type != 'final_exam' AND sm.marks_obtained IS NOT NULL 
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as continuous_avg,
                    AVG(CASE 
                        WHEN ac.component_type = 'final_exam' AND sm.marks_obtained IS NOT NULL 
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as final_exam_percentage,
                    SUM(CASE WHEN sm.marks_obtained IS NOT NULL THEN sm.marks_obtained ELSE 0 END) as total_marks,
                    SUM(CASE WHEN sm.marks_obtained IS NOT NULL THEN ac.max_marks ELSE 0 END) as max_marks,
                    u.full_name as lecturer_name
                FROM course_enrollments ce
                JOIN courses c ON ce.course_id = c.id
                LEFT JOIN assessment_components ac ON c.id = ac.course_id AND ac.is_published = TRUE
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = ce.student_id
                LEFT JOIN course_lecturers cl ON c.id = cl.course_id AND cl.role = 'primary'
                LEFT JOIN users u ON cl.lecturer_id = u.id
                WHERE ce.student_id = ? AND ce.status = 'active'
                GROUP BY c.id, c.course_code, c.course_name, c.credits, c.semester, ce.status, u.full_name
                ORDER BY c.semester DESC, c.course_code
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$studentId]);
            $courses = $stmt->fetchAll();

            $totalCredits = 0;
            $totalGradePoints = 0;
            $coursesWithGrades = [];
            $enrolledCoursesCount = 0;

            foreach ($courses as &$course) {
                $enrolledCoursesCount++;
                
                // Calculate overall course percentage
                $overallPercentage = null;
                if ($course['continuous_avg'] !== null && $course['final_exam_percentage'] !== null) {
                    $overallPercentage = round(($course['continuous_avg'] * 0.7) + ($course['final_exam_percentage'] * 0.3), 2);
                } elseif ($course['continuous_avg'] !== null) {
                    $overallPercentage = round($course['continuous_avg'] * 0.7, 2);
                }

                $course['overall_percentage'] = $overallPercentage;
                $course['grade_letter'] = $overallPercentage ? $this->getGradeLetter($overallPercentage) : null;
                $course['grade_point'] = $overallPercentage ? $this->getGradePoint($overallPercentage) : null;
                $course['progress'] = $course['total_components'] > 0 ? 
                    round(($course['graded_components'] / $course['total_components']) * 100, 2) : 0;
                $course['percentage'] = $overallPercentage;
                $course['status'] = $course['enrollment_status'];
                $course['lecturer_name'] = $course['lecturer_name'] ?: 'TBA';

                // Calculate GPA
                if ($course['grade_point'] !== null) {
                    $totalCredits += $course['credit_hours'];
                    $totalGradePoints += $course['grade_point'] * $course['credit_hours'];
                    $coursesWithGrades[] = $course;
                }
            }

            $currentGPA = $totalCredits > 0 ? round($totalGradePoints / $totalCredits, 2) : 0.0;

            // Calculate class rank (simplified - in real app this would be more complex)
            $classRank = 1; // Placeholder
            $totalStudents = 50; // Placeholder
            $percentile = 95; // Placeholder

            return $this->successResponse($response, [
                'student_info' => $studentInfo,
                'courses' => $courses,
                'academic_summary' => [
                    'overall_gpa' => $currentGPA,
                    'gpa_change' => 0.0, // Placeholder for GPA change
                    'enrolled_courses_count' => $enrolledCoursesCount,
                    'class_rank' => $classRank,
                    'total_students' => $totalStudents,
                    'percentile' => $percentile,
                    'current_gpa' => $currentGPA,
                    'total_credits' => $totalCredits,
                    'completed_courses' => count($coursesWithGrades),
                    'total_enrolled' => count($courses),
                    'academic_standing' => $this->getAcademicStanding($currentGPA),
                    'credit_hours' => $totalCredits
                ]
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch breakdown: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Compare marks anonymously with coursemates
     * GET /api/course/{courseId}/marks/comparison
     */
    public function getCourseComparison(Request $request, Response $response, array $args): Response
    {
        try {
            $courseId = (int)$args['courseId'];
            $currentUserId = $this->getUserId($request);

            // First, check if there are any assessment components for this course
            $componentCheckSql = "SELECT COUNT(*) as component_count FROM assessment_components WHERE course_id = ? AND is_published = TRUE";
            $stmt = $this->db->prepare($componentCheckSql);
            $stmt->execute([$courseId]);
            $componentCount = $stmt->fetch()['component_count'];

            if ($componentCount == 0) {
                // No assessment components for this course
                return $this->successResponse($response, [
                    'comparisons' => [],
                    'message' => 'No assessment components available for this course'
                ]);
            }

            $sql = "
                SELECT 
                    ac.component_name,
                    ac.component_type,
                    ac.max_marks,
                    AVG(sm.marks_obtained) as class_average,
                    MIN(sm.marks_obtained) as min_marks,
                    MAX(sm.marks_obtained) as max_marks,
                    COUNT(sm.marks_obtained) as submissions,
                    SUM(CASE WHEN sm.student_id = ? THEN sm.marks_obtained ELSE NULL END) as my_marks
                FROM assessment_components ac
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id
                WHERE ac.course_id = ? AND ac.is_published = TRUE
                GROUP BY ac.id, ac.component_name, ac.component_type, ac.max_marks
                ORDER BY ac.component_type, ac.created_at
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$currentUserId, $courseId]);
            $comparisons = $stmt->fetchAll();

            foreach ($comparisons as &$comp) {
                $comp['class_average_percentage'] = $comp['class_average'] ? 
                    round(($comp['class_average'] / $comp['max_marks']) * 100, 2) : null;
                $comp['my_percentage'] = $comp['my_marks'] ? 
                    round(($comp['my_marks'] / $comp['max_marks']) * 100, 2) : null;
                $comp['percentile'] = $comp['my_marks'] ? $this->calculatePercentile($courseId, $comp['my_marks']) : null;
            }

            return $this->successResponse($response, [
                'comparisons' => $comparisons
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch comparison: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get class rank and percentile for a student
     * GET /api/student/{studentId}/course/{courseId}/rank
     */
    public function getStudentRank(Request $request, Response $response, array $args): Response
    {
        try {
            $studentId = (int)$args['studentId'];
            $courseId = (int)$args['courseId'];

            // Verify access
            $userRole = $this->getUserRole($request);
            $currentUserId = $this->getUserId($request);
            
            if ($userRole === 'student' && $currentUserId !== $studentId) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            // First, check if there are any assessment components for this course
            $componentCheckSql = "SELECT COUNT(*) as component_count FROM assessment_components WHERE course_id = ? AND is_published = TRUE";
            $stmt = $this->db->prepare($componentCheckSql);
            $stmt->execute([$courseId]);
            $componentCount = $stmt->fetch()['component_count'];

            if ($componentCount == 0) {
                // No assessment components for this course
                return $this->successResponse($response, [
                    'rank' => null,
                    'total_students' => 0,
                    'percentile' => null,
                    'percentage' => null,
                    'class_average' => null,
                    'message' => 'No assessment components available for this course'
                ]);
            }

            // Calculate overall course performance for all students
            $sql = "
                SELECT 
                    sm.student_id,
                    SUM(CASE 
                        WHEN ac.component_type != 'final_exam' 
                        THEN (sm.marks_obtained / ac.max_marks) * ac.weight_percentage 
                        ELSE (sm.marks_obtained / ac.max_marks) * 30 
                    END) as total_percentage
                FROM student_marks sm
                JOIN assessment_components ac ON sm.assessment_component_id = ac.id
                WHERE ac.course_id = ? AND sm.marks_obtained IS NOT NULL AND ac.is_published = TRUE
                GROUP BY sm.student_id
                HAVING COUNT(sm.marks_obtained) > 0
                ORDER BY total_percentage DESC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId]);
            $rankings = $stmt->fetchAll();

            $studentRank = null;
            $totalStudents = count($rankings);
            $studentPercentage = null;

            // If no students have marks yet, return appropriate response
            if ($totalStudents == 0) {
                return $this->successResponse($response, [
                    'rank' => null,
                    'total_students' => 0,
                    'percentile' => null,
                    'percentage' => null,
                    'class_average' => null,
                    'message' => 'No students have marks for this course yet'
                ]);
            }

            foreach ($rankings as $index => $ranking) {
                if ($ranking['student_id'] == $studentId) {
                    $studentRank = $index + 1;
                    $studentPercentage = $ranking['total_percentage'];
                    break;
                }
            }

            $percentile = $studentRank ? round((($totalStudents - $studentRank + 1) / $totalStudents) * 100, 1) : null;

            return $this->successResponse($response, [
                'rank' => $studentRank,
                'total_students' => $totalStudents,
                'percentile' => $percentile,
                'percentage' => round($studentPercentage, 2),
                'class_average' => round(array_sum(array_column($rankings, 'total_percentage')) / $totalStudents, 2)
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch rank: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get performance trends for a student
     * GET /api/student/{studentId}/performance/trends
     */
    public function getPerformanceTrends(Request $request, Response $response, array $args): Response
    {
        try {
            $studentId = (int)$args['studentId'];
            $period = $request->getQueryParams()['period'] ?? 'semester';
            $courseId = $request->getQueryParams()['course_id'] ?? null;

            // Verify access
            $userRole = $this->getUserRole($request);
            $currentUserId = $this->getUserId($request);
            
            if ($userRole === 'student' && $currentUserId !== $studentId) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            // Build WHERE clause for course filtering
            $courseFilter = '';
            $params = [$studentId];
            
            if ($courseId && $courseId !== 'all') {
                $courseFilter = ' AND ac.course_id = ?';
                $params[] = (int)$courseId;
            }

            // Get recent assessment updates for trends
            $recentUpdatesSql = "
                SELECT 
                    sm.id,
                    sm.marks_obtained,
                    sm.graded_at as updated_at,
                    ac.component_name,
                    ac.component_type,
                    ac.max_marks,
                    c.course_code,
                    c.course_name,
                    (sm.marks_obtained / ac.max_marks) * 100 as percentage
                FROM student_marks sm
                JOIN assessment_components ac ON sm.assessment_component_id = ac.id
                JOIN courses c ON ac.course_id = c.id
                WHERE sm.student_id = ? AND sm.marks_obtained IS NOT NULL" . $courseFilter . "
                ORDER BY sm.graded_at DESC
                LIMIT 20
            ";

            $stmt = $this->db->prepare($recentUpdatesSql);
            $stmt->execute($params);
            $recentUpdates = $stmt->fetchAll();

            // Get semester trends
            $semesterTrendsSql = "
                SELECT 
                    c.semester,
                    c.course_code,
                    c.course_name,
                    AVG(CASE 
                        WHEN ac.component_type != 'final_exam' 
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as continuous_avg,
                    AVG(CASE 
                        WHEN ac.component_type = 'final_exam' 
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as final_avg,
                    COUNT(sm.id) as completed_assessments
                FROM student_marks sm
                JOIN assessment_components ac ON sm.assessment_component_id = ac.id
                JOIN courses c ON ac.course_id = c.id
                WHERE sm.student_id = ? AND sm.marks_obtained IS NOT NULL" . $courseFilter . "
                GROUP BY c.id, c.semester, c.course_code, c.course_name
                ORDER BY c.semester, c.course_code
            ";

            $stmt = $this->db->prepare($semesterTrendsSql);
            $stmt->execute($params);
            $trends = $stmt->fetchAll();

            // Group by semester if requested
            if ($period === 'semester') {
                $semesterTrends = [];
                foreach ($trends as $trend) {
                    $semester = $trend['semester'];
                    if (!isset($semesterTrends[$semester])) {
                        $semesterTrends[$semester] = [
                            'semester' => $semester,
                            'courses' => [],
                            'average_continuous' => 0,
                            'average_final' => 0,
                            'course_count' => 0
                        ];
                    }
                    $semesterTrends[$semester]['courses'][] = $trend;
                    $semesterTrends[$semester]['course_count']++;
                }

                // Calculate semester averages
                foreach ($semesterTrends as &$semesterData) {
                    $contSum = 0; $finalSum = 0; $contCount = 0; $finalCount = 0;
                    foreach ($semesterData['courses'] as $course) {
                        if ($course['continuous_avg']) { $contSum += $course['continuous_avg']; $contCount++; }
                        if ($course['final_avg']) { $finalSum += $course['final_avg']; $finalCount++; }
                    }
                    $semesterData['average_continuous'] = $contCount > 0 ? round($contSum / $contCount, 2) : 0;
                    $semesterData['average_final'] = $finalCount > 0 ? round($finalSum / $finalCount, 2) : 0;
                }

                return $this->successResponse($response, [
                    'trends' => array_values($semesterTrends),
                    'recent_updates' => $recentUpdates,
                    'period' => $period,
                    'course_id' => $courseId
                ]);
            }

            return $this->successResponse($response, [
                'trends' => $trends,
                'recent_updates' => $recentUpdates,
                'period' => $period,
                'course_id' => $courseId
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch trends: ' . $e->getMessage(), 500);
        }
    }

    /**
     * What-if simulator for grade prediction
     * POST /api/student/{studentId}/what-if-simulation
     */
    public function whatIfSimulation(Request $request, Response $response, array $args): Response
    {
        try {
            $studentId = (int)$args['studentId'];
            $data = json_decode($request->getBody()->getContents(), true);

            // Verify access
            $userRole = $this->getUserRole($request);
            $currentUserId = $this->getUserId($request);
            
            if ($userRole === 'student' && $currentUserId !== $studentId) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $required = ['course_id', 'simulations'];
            $missing = $this->validateRequired($data, $required);
            if (!empty($missing)) {
                return $this->errorResponse($response, 'Missing required fields: ' . implode(', ', $missing));
            }

            $courseId = $data['course_id'];
            $simulations = $data['simulations']; // array of component_id => projected_marks

            // Get current marks and component structure
            $sql = "
                SELECT 
                    ac.id as component_id,
                    ac.component_name,
                    ac.component_type,
                    ac.max_marks,
                    ac.weight_percentage,
                    sm.marks_obtained as current_marks
                FROM assessment_components ac
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = ?
                WHERE ac.course_id = ? AND ac.is_published = TRUE
                ORDER BY ac.component_type, ac.created_at
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$studentId, $courseId]);
            $components = $stmt->fetchAll();

            $projectedTotal = 0;
            $maxPossibleTotal = 0;
            $scenarioBreakdown = [];

            foreach ($components as $component) {
                $componentId = $component['component_id'];
                $marks = isset($simulations[$componentId]) ? 
                    $simulations[$componentId] : $component['current_marks'];

                if ($marks !== null) {
                    $percentage = ($marks / $component['max_marks']) * 100;
                    $weightedScore = ($marks / $component['max_marks']) * $component['weight_percentage'];
                    $projectedTotal += $weightedScore;

                    $scenarioBreakdown[] = [
                        'component_name' => $component['component_name'],
                        'component_type' => $component['component_type'],
                        'current_marks' => $component['current_marks'],
                        'projected_marks' => $marks,
                        'max_marks' => $component['max_marks'],
                        'percentage' => round($percentage, 2),
                        'weight_contribution' => round($weightedScore, 2),
                        'is_simulated' => isset($simulations[$componentId])
                    ];
                }

                $maxPossibleTotal += $component['weight_percentage'];
            }

            $projectedPercentage = round($projectedTotal, 2);
            $projectedGrade = $this->getGradeLetter($projectedPercentage);
            $projectedGPA = $this->getGradePoint($projectedPercentage);

            return $this->successResponse($response, [
                'projected_percentage' => $projectedPercentage,
                'projected_grade' => $projectedGrade,
                'projected_gpa' => $projectedGPA,
                'max_possible_total' => $maxPossibleTotal,
                'scenario_breakdown' => $scenarioBreakdown,
                'improvement_needed' => $this->calculateImprovementNeeded($scenarioBreakdown, $projectedPercentage)
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to simulate grades: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get class averages per component
     * GET /api/course/{courseId}/averages
     */
    public function getClassAverages(Request $request, Response $response, array $args): Response
    {
        try {
            $courseId = (int)$args['courseId'];

            // First, check if there are any assessment components for this course
            $componentCheckSql = "SELECT COUNT(*) as component_count FROM assessment_components WHERE course_id = ? AND is_published = TRUE";
            $stmt = $this->db->prepare($componentCheckSql);
            $stmt->execute([$courseId]);
            $componentCount = $stmt->fetch()['component_count'];

            if ($componentCount == 0) {
                // No assessment components for this course
                return $this->successResponse($response, [
                    'class_averages' => [],
                    'message' => 'No assessment components available for this course'
                ]);
            }

            $sql = "
                SELECT 
                    ac.component_name,
                    ac.component_type,
                    ac.max_marks,
                    ac.weight_percentage,
                    COUNT(sm.marks_obtained) as submissions,
                    AVG(sm.marks_obtained) as average_marks,
                    MIN(sm.marks_obtained) as min_marks,
                    MAX(sm.marks_obtained) as max_marks,
                    STDDEV(sm.marks_obtained) as std_deviation
                FROM assessment_components ac
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id
                WHERE ac.course_id = ? AND ac.is_published = TRUE
                GROUP BY ac.id, ac.component_name, ac.component_type, ac.max_marks, ac.weight_percentage
                ORDER BY ac.component_type, ac.created_at
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId]);
            $averages = $stmt->fetchAll();

            foreach ($averages as &$avg) {
                $avg['average_percentage'] = $avg['average_marks'] ? 
                    round(($avg['average_marks'] / $avg['max_marks']) * 100, 2) : null;
                $avg['min_percentage'] = $avg['min_marks'] ? 
                    round(($avg['min_marks'] / $avg['max_marks']) * 100, 2) : null;
                $avg['max_percentage'] = $avg['max_marks'] ? 
                    round(($avg['max_marks'] / $avg['max_marks']) * 100, 2) : null;
            }

            return $this->successResponse($response, [
                'class_averages' => $averages
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch averages: ' . $e->getMessage(), 500);
        }
    }

    // Helper methods

    private function calculatePercentile($courseId, $marks): float
    {
        $sql = "
            SELECT COUNT(*) as lower_count
            FROM student_marks sm
            JOIN assessment_components ac ON sm.assessment_component_id = ac.id
            WHERE ac.course_id = ? AND sm.marks_obtained < ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId, $marks]);
        $lowerCount = $stmt->fetchColumn();

        $sql = "
            SELECT COUNT(*) as total_count
            FROM student_marks sm
            JOIN assessment_components ac ON sm.assessment_component_id = ac.id
            WHERE ac.course_id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        $totalCount = $stmt->fetchColumn();

        return $totalCount > 0 ? round(($lowerCount / $totalCount) * 100, 1) : 0;
    }

    private function getAcademicStanding(float $gpa): string
    {
        if ($gpa >= 3.5) return 'Dean\'s List';
        if ($gpa >= 3.0) return 'Good Standing';
        if ($gpa >= 2.0) return 'Satisfactory';
        if ($gpa >= 1.5) return 'Academic Warning';
        return 'Academic Probation';
    }

    private function calculateImprovementNeeded(array $breakdown, float $currentPercentage): array
    {
        $improvements = [];
        $targetGrades = [50, 60, 70, 80]; // Target percentages

        foreach ($targetGrades as $target) {
            if ($currentPercentage < $target) {
                $needed = $target - $currentPercentage;
                $improvements[] = [
                    'target_percentage' => $target,
                    'target_grade' => $this->getGradeLetter($target),
                    'improvement_needed' => round($needed, 2),
                    'achievable' => $needed <= 20 // Realistic improvement threshold
                ];
            }
        }

        return $improvements;
    }
}