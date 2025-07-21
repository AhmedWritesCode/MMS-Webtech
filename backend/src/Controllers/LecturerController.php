<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LecturerController extends BaseController
{
    /**
     * Get lecturer's dashboard overview
     * GET /api/lecturer/{lecturerId}/dashboard
     */
    public function getDashboard(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];

            // Verify lecturer access
            $userRole = $this->getUserRole($request);
            $currentUserId = $this->getUserId($request);
            
            if ($userRole === 'lecturer' && $currentUserId !== $lecturerId) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            // Get lecturer's courses
            $coursesSql = "
                SELECT 
                    c.id as course_id,
                    c.course_code,
                    c.course_name,
                    c.credits,
                    c.semester,
                    COUNT(DISTINCT ce.student_id) as enrolled_students,
                    COUNT(DISTINCT ac.id) as total_components,
                    COUNT(DISTINCT CASE WHEN sm.marks_obtained IS NOT NULL THEN sm.id END) as graded_components,
                    AVG(CASE 
                        WHEN ac.component_type != 'final_exam' AND sm.marks_obtained IS NOT NULL
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as avg_continuous_performance,
                    AVG(CASE 
                        WHEN ac.component_type = 'final_exam' AND sm.marks_obtained IS NOT NULL
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as avg_final_exam_performance
                FROM course_lecturers cl
                JOIN courses c ON cl.course_id = c.id
                LEFT JOIN course_enrollments ce ON c.id = ce.course_id AND ce.status = 'active'
                LEFT JOIN assessment_components ac ON c.id = ac.course_id AND ac.is_published = TRUE
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id
                WHERE cl.lecturer_id = ? AND cl.role = 'primary'
                GROUP BY c.id, c.course_code, c.course_name, c.credits, c.semester
                ORDER BY c.semester DESC, c.course_code
            ";

            $stmt = $this->db->prepare($coursesSql);
            $stmt->execute([$lecturerId]);
            $courses = $stmt->fetchAll();

            // Calculate overall statistics
            $totalStudents = 0;
            $totalComponents = 0;
            $totalGradedComponents = 0;
            $overallAvgPerformance = 0;
            $performanceCount = 0;

            foreach ($courses as &$course) {
                $totalStudents += $course['enrolled_students'];
                $totalComponents += $course['total_components'];
                $totalGradedComponents += $course['graded_components'];
                
                if ($course['avg_continuous_performance'] !== null) {
                    $overallAvgPerformance += $course['avg_continuous_performance'];
                    $performanceCount++;
                }

                // Calculate course completion rate
                $course['completion_rate'] = $course['total_components'] > 0 ? 
                    round(($course['graded_components'] / $course['total_components']) * 100, 1) : 0;
            }

            $overallAvgPerformance = $performanceCount > 0 ? 
                round($overallAvgPerformance / $performanceCount, 2) : 0;

            // Get recent activities
            $recentActivitiesSql = "
                SELECT 
                    'mark_entry' as activity_type,
                    sm.graded_at as activity_date,
                    CONCAT('Graded ', ac.component_name, ' for ', c.course_code) as description,
                    u.full_name as student_name,
                    c.course_code
                FROM student_marks sm
                JOIN assessment_components ac ON sm.assessment_component_id = ac.id
                JOIN courses c ON ac.course_id = c.id
                JOIN course_lecturers cl ON c.id = cl.course_id
                JOIN users u ON sm.student_id = u.id
                WHERE cl.lecturer_id = ? AND sm.graded_by = ?
                ORDER BY sm.graded_at DESC
                LIMIT 10
            ";

            $stmt = $this->db->prepare($recentActivitiesSql);
            $stmt->execute([$lecturerId, $lecturerId]);
            $recentActivities = $stmt->fetchAll();

            return $this->successResponse($response, [
                'courses' => $courses,
                'overview' => [
                    'total_courses' => count($courses),
                    'total_students' => $totalStudents,
                    'total_components' => $totalComponents,
                    'total_graded_components' => $totalGradedComponents,
                    'overall_avg_performance' => $overallAvgPerformance,
                    'completion_rate' => $totalComponents > 0 ? 
                        round(($totalGradedComponents / $totalComponents) * 100, 1) : 0
                ],
                'recent_activities' => $recentActivities
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch dashboard: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get lecturer's courses only
     * GET /api/lecturer/{lecturerId}/courses
     */
    public function getCourses(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];

            // Verify lecturer access
            $userRole = $this->getUserRole($request);
            $currentUserId = $this->getUserId($request);
            if ($userRole === 'lecturer' && $currentUserId !== $lecturerId) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $coursesSql = "
                SELECT 
                    c.id as course_id,
                    c.course_code,
                    c.course_name,
                    c.credits,
                    c.semester,
                    COUNT(DISTINCT ce.student_id) as enrolled_students,
                    COUNT(DISTINCT ac.id) as total_components,
                    COUNT(DISTINCT CASE WHEN sm.marks_obtained IS NOT NULL THEN sm.id END) as graded_components,
                    AVG(CASE 
                        WHEN ac.component_type != 'final_exam' AND sm.marks_obtained IS NOT NULL
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as avg_continuous_performance,
                    AVG(CASE 
                        WHEN ac.component_type = 'final_exam' AND sm.marks_obtained IS NOT NULL
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as avg_final_exam_performance
                FROM course_lecturers cl
                JOIN courses c ON cl.course_id = c.id
                LEFT JOIN course_enrollments ce ON c.id = ce.course_id AND ce.status = 'active'
                LEFT JOIN assessment_components ac ON c.id = ac.course_id AND ac.is_published = TRUE
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id
                WHERE cl.lecturer_id = ? AND cl.role = 'primary'
                GROUP BY c.id, c.course_code, c.course_name, c.credits, c.semester
                ORDER BY c.semester DESC, c.course_code
            ";

            $stmt = $this->db->prepare($coursesSql);
            $stmt->execute([$lecturerId]);
            $courses = $stmt->fetchAll();

            foreach ($courses as &$course) {
                $course['completion_rate'] = $course['total_components'] > 0 ? 
                    round(($course['graded_components'] / $course['total_components']) * 100, 1) : 0;
            }

            return $this->successResponse($response, [
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch courses: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get students in a specific course
     * GET /api/lecturer/{lecturerId}/course/{courseId}/students
     */
    public function getCourseStudents(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];
            $courseId = (int)$args['courseId'];

            // Verify lecturer access to this course
            if (!$this->verifyLecturerCourseAccess($lecturerId, $courseId)) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $sql = "
                SELECT 
                    u.id as student_id,
                    u.username as matric_number,
                    u.full_name,
                    u.email,
                    ce.enrollment_date,
                    COUNT(DISTINCT ac.id) as total_components,
                    COUNT(DISTINCT CASE WHEN sm.marks_obtained IS NOT NULL THEN sm.id END) as graded_components,
                    AVG(CASE 
                        WHEN ac.component_type != 'final_exam' AND sm.marks_obtained IS NOT NULL
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as continuous_avg,
                    AVG(CASE 
                        WHEN ac.component_type = 'final_exam' AND sm.marks_obtained IS NOT NULL
                        THEN (sm.marks_obtained / ac.max_marks) * 100 
                        ELSE NULL 
                    END) as final_exam_avg,
                    SUM(CASE WHEN sm.marks_obtained IS NOT NULL THEN sm.marks_obtained ELSE 0 END) as total_marks,
                    SUM(CASE WHEN sm.marks_obtained IS NOT NULL THEN ac.max_marks ELSE 0 END) as max_marks
                FROM course_enrollments ce
                JOIN users u ON ce.student_id = u.id
                LEFT JOIN assessment_components ac ON ce.course_id = ac.course_id AND ac.is_published = TRUE
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = u.id
                WHERE ce.course_id = ? AND ce.status = 'active'
                GROUP BY u.id, u.username, u.full_name, u.email, ce.enrollment_date
                ORDER BY u.full_name
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId]);
            $students = $stmt->fetchAll();

            foreach ($students as &$student) {
                // Calculate overall percentage
                $overallPercentage = null;
                if ($student['continuous_avg'] !== null && $student['final_exam_avg'] !== null) {
                    $overallPercentage = round(($student['continuous_avg'] * 0.7) + ($student['final_exam_avg'] * 0.3), 2);
                } elseif ($student['continuous_avg'] !== null) {
                    $overallPercentage = round($student['continuous_avg'] * 0.7, 2);
                }

                $student['overall_percentage'] = $overallPercentage;
                $student['grade_letter'] = $overallPercentage ? $this->getGradeLetter($overallPercentage) : null;
                $student['grade_point'] = $overallPercentage ? $this->getGradePoint($overallPercentage) : null;
                $student['completion_rate'] = $student['total_components'] > 0 ? 
                    round(($student['graded_components'] / $student['total_components']) * 100, 1) : 0;
            }

            return $this->successResponse($response, [
                'students' => $students
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch students: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get assessment components for a course
     * GET /api/lecturer/{lecturerId}/course/{courseId}/components
     */
    public function getCourseComponents(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];
            $courseId = (int)$args['courseId'];

            // Verify lecturer access to this course
            if (!$this->verifyLecturerCourseAccess($lecturerId, $courseId)) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $sql = "
                SELECT 
                    ac.id,
                    ac.component_name,
                    ac.component_type,
                    ac.max_marks,
                    ac.weight_percentage,
                    ac.description,
                    ac.is_published,
                    ac.created_at,
                    COUNT(sm.id) as submissions_count,
                    AVG(sm.marks_obtained) as avg_marks,
                    MIN(sm.marks_obtained) as min_marks,
                    MAX(sm.marks_obtained) as max_marks
                FROM assessment_components ac
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id
                WHERE ac.course_id = ?
                GROUP BY ac.id, ac.component_name, ac.component_type, ac.max_marks, ac.weight_percentage, ac.description, ac.is_published, ac.created_at
                ORDER BY ac.component_type, ac.created_at
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId]);
            $components = $stmt->fetchAll();

            foreach ($components as &$component) {
                $component['avg_percentage'] = $component['avg_marks'] ? 
                    round(($component['avg_marks'] / $component['max_marks']) * 100, 2) : null;
                $component['min_percentage'] = $component['min_marks'] ? 
                    round(($component['min_marks'] / $component['max_marks']) * 100, 2) : null;
                $component['max_percentage'] = $component['max_marks'] ? 
                    round(($component['max_marks'] / $component['max_marks']) * 100, 2) : null;
            }

            return $this->successResponse($response, [
                'components' => $components
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch components: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create or update assessment component
     * POST /api/lecturer/{lecturerId}/course/{courseId}/components
     */
    public function createUpdateComponent(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];
            $courseId = (int)$args['courseId'];
            $data = json_decode($request->getBody()->getContents(), true);

            // Verify lecturer access to this course
            if (!$this->verifyLecturerCourseAccess($lecturerId, $courseId)) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $required = ['component_name', 'component_type', 'max_marks', 'weight_percentage'];
            $missing = $this->validateRequired($data, $required);
            if (!empty($missing)) {
                return $this->errorResponse($response, 'Missing required fields: ' . implode(', ', $missing));
            }

            // Check if component exists (for update)
            $componentId = $data['id'] ?? null;
            
            if ($componentId) {
                // Update existing component
                $sql = "
                    UPDATE assessment_components 
                    SET component_name = ?, component_type = ?, max_marks = ?, 
                        weight_percentage = ?, description = ?, is_published = ?, updated_at = NOW()
                    WHERE id = ? AND course_id = ?
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $data['component_name'],
                    $data['component_type'],
                    $data['max_marks'],
                    $data['weight_percentage'],
                    $data['description'] ?? null,
                    $data['is_published'] ?? true,
                    $componentId,
                    $courseId
                ]);
            } else {
                // Create new component
                $sql = "
                    INSERT INTO assessment_components 
                    (course_id, component_name, component_type, max_marks, weight_percentage, description, is_published, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $courseId,
                    $data['component_name'],
                    $data['component_type'],
                    $data['max_marks'],
                    $data['weight_percentage'],
                    $data['description'] ?? null,
                    $data['is_published'] ?? true
                ]);
                $componentId = $this->db->lastInsertId();
            }

            return $this->successResponse($response, [
                'component_id' => $componentId,
                'message' => isset($data['id']) ? 'Component updated successfully' : 'Component created successfully'
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to save component: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update student marks for a specific component
     * PUT /api/lecturer/{lecturerId}/course/{courseId}/student/{studentId}/marks
     */
    public function updateStudentMarks(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];
            $courseId = (int)$args['courseId'];
            $studentId = (int)$args['studentId'];
            $data = json_decode($request->getBody()->getContents(), true);

            // Verify lecturer access to this course
            if (!$this->verifyLecturerCourseAccess($lecturerId, $courseId)) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $required = ['component_id', 'marks_obtained'];
            $missing = $this->validateRequired($data, $required);
            if (!empty($missing)) {
                return $this->errorResponse($response, 'Missing required fields: ' . implode(', ', $missing));
            }

            // Verify component belongs to this course
            $componentSql = "SELECT id, max_marks FROM assessment_components WHERE id = ? AND course_id = ?";
            $stmt = $this->db->prepare($componentSql);
            $stmt->execute([$data['component_id'], $courseId]);
            $component = $stmt->fetch();

            if (!$component) {
                return $this->errorResponse($response, 'Invalid component', 400);
            }

            // Validate marks
            if ($data['marks_obtained'] > $component['max_marks']) {
                return $this->errorResponse($response, 'Marks cannot exceed maximum marks', 400);
            }

            // Insert or update marks
            $sql = "
                INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, remarks, graded_by, graded_at, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                marks_obtained = VALUES(marks_obtained),
                remarks = VALUES(remarks),
                graded_by = VALUES(graded_by),
                graded_at = NOW(),
                updated_at = NOW()
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $studentId,
                $data['component_id'],
                $data['marks_obtained'],
                $data['remarks'] ?? null,
                $lecturerId
            ]);

            return $this->successResponse($response, [
                'message' => 'Marks updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to update marks: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Bulk upload marks via CSV
     * POST /api/lecturer/{lecturerId}/course/{courseId}/marks/bulk-upload
     */
    public function bulkUploadMarks(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];
            $courseId = (int)$args['courseId'];

            // Verify lecturer access to this course
            if (!$this->verifyLecturerCourseAccess($lecturerId, $courseId)) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $uploadedFiles = $request->getUploadedFiles();
            $csvFile = $uploadedFiles['csv_file'] ?? null;

            if (!$csvFile || $csvFile->getError() !== UPLOAD_ERR_OK) {
                return $this->errorResponse($response, 'No CSV file uploaded or upload error', 400);
            }

            $csvContent = $csvFile->getStream()->getContents();
            $lines = explode("\n", $csvContent);
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Skip header row
            array_shift($lines);

            foreach ($lines as $lineNumber => $line) {
                if (empty(trim($line))) continue;

                $columns = str_getcsv($line);
                if (count($columns) < 3) {
                    $errors[] = "Line " . ($lineNumber + 2) . ": Invalid format";
                    $errorCount++;
                    continue;
                }

                $matricNumber = trim($columns[0]);
                $componentName = trim($columns[1]);
                $marks = trim($columns[2]);
                $remarks = isset($columns[3]) ? trim($columns[3]) : null;

                try {
                    // Get student ID
                    $studentSql = "SELECT id FROM users WHERE username = ? AND user_type = 'student'";
                    $stmt = $this->db->prepare($studentSql);
                    $stmt->execute([$matricNumber]);
                    $student = $stmt->fetch();

                    if (!$student) {
                        $errors[] = "Line " . ($lineNumber + 2) . ": Student not found: " . $matricNumber;
                        $errorCount++;
                        continue;
                    }

                    // Get component ID
                    $componentSql = "SELECT id, max_marks FROM assessment_components WHERE component_name = ? AND course_id = ?";
                    $stmt = $this->db->prepare($componentSql);
                    $stmt->execute([$componentName, $courseId]);
                    $component = $stmt->fetch();

                    if (!$component) {
                        $errors[] = "Line " . ($lineNumber + 2) . ": Component not found: " . $componentName;
                        $errorCount++;
                        continue;
                    }

                    // Validate marks
                    if (!is_numeric($marks) || $marks < 0 || $marks > $component['max_marks']) {
                        $errors[] = "Line " . ($lineNumber + 2) . ": Invalid marks for " . $componentName;
                        $errorCount++;
                        continue;
                    }

                    // Insert or update marks
                    $marksSql = "
                        INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, remarks, graded_by, graded_at, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, NOW(), NOW(), NOW())
                        ON DUPLICATE KEY UPDATE 
                        marks_obtained = VALUES(marks_obtained),
                        remarks = VALUES(remarks),
                        graded_by = VALUES(graded_by),
                        graded_at = NOW(),
                        updated_at = NOW()
                    ";

                    $stmt = $this->db->prepare($marksSql);
                    $stmt->execute([
                        $student['id'],
                        $component['id'],
                        $marks,
                        $remarks,
                        $lecturerId
                    ]);

                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Line " . ($lineNumber + 2) . ": " . $e->getMessage();
                    $errorCount++;
                }
            }

            return $this->successResponse($response, [
                'message' => 'Bulk upload completed',
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to process CSV: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Export course marks as CSV
     * GET /api/lecturer/{lecturerId}/course/{courseId}/marks/export
     */
    public function exportMarks(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];
            $courseId = (int)$args['courseId'];

            // Verify lecturer access to this course
            if (!$this->verifyLecturerCourseAccess($lecturerId, $courseId)) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $sql = "
                SELECT 
                    u.username as matric_number,
                    u.full_name,
                    ac.component_name,
                    ac.component_type,
                    ac.max_marks,
                    ac.weight_percentage,
                    sm.marks_obtained,
                    sm.remarks,
                    sm.graded_at,
                    c.course_code,
                    c.course_name
                FROM course_enrollments ce
                JOIN users u ON ce.student_id = u.id
                JOIN courses c ON ce.course_id = c.id
                LEFT JOIN assessment_components ac ON c.id = ac.course_id AND ac.is_published = TRUE
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = u.id
                WHERE ce.course_id = ? AND ce.status = 'active'
                ORDER BY u.full_name, ac.component_type, ac.created_at
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId]);
            $results = $stmt->fetchAll();

            // Generate CSV content
            $csvContent = "Matric Number,Student Name,Component Name,Component Type,Max Marks,Weight %,Marks Obtained,Percentage,Remarks,Graded At\n";

            foreach ($results as $row) {
                $percentage = $row['marks_obtained'] ? 
                    round(($row['marks_obtained'] / $row['max_marks']) * 100, 2) : '';
                
                $csvContent .= sprintf(
                    '"%s","%s","%s","%s",%s,%s,%s,%s,"%s","%s"' . "\n",
                    $row['matric_number'],
                    $row['full_name'],
                    $row['component_name'],
                    $row['component_type'],
                    $row['max_marks'],
                    $row['weight_percentage'],
                    $row['marks_obtained'] ?? '',
                    $percentage,
                    $row['remarks'] ?? '',
                    $row['graded_at'] ?? ''
                );
            }

            $courseCode = $results[0]['course_code'] ?? 'course';
            $filename = $courseCode . '_marks_' . date('Y-m-d') . '.csv';

            $response = $response
                ->withHeader('Content-Type', 'text/csv')
                ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->withHeader('Content-Length', strlen($csvContent));

            $response->getBody()->write($csvContent);
            return $response;

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to export marks: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get course analytics
     * GET /api/lecturer/{lecturerId}/course/{courseId}/analytics
     */
    public function getCourseAnalytics(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];
            $courseId = (int)$args['courseId'];

            // Verify lecturer access to this course
            if (!$this->verifyLecturerCourseAccess($lecturerId, $courseId)) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            // Get component-wise statistics
            $componentStatsSql = "
                SELECT 
                    ac.component_name,
                    ac.component_type,
                    ac.max_marks,
                    ac.weight_percentage,
                    COUNT(sm.id) as submissions,
                    AVG(sm.marks_obtained) as avg_marks,
                    MIN(sm.marks_obtained) as min_marks,
                    MAX(sm.marks_obtained) as max_marks,
                    STDDEV(sm.marks_obtained) as std_deviation
                FROM assessment_components ac
                LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id
                WHERE ac.course_id = ? AND ac.is_published = TRUE
                GROUP BY ac.id, ac.component_name, ac.component_type, ac.max_marks, ac.weight_percentage
                ORDER BY ac.component_type, ac.created_at
            ";

            $stmt = $this->db->prepare($componentStatsSql);
            $stmt->execute([$courseId]);
            $componentStats = $stmt->fetchAll();

            foreach ($componentStats as &$stat) {
                $stat['avg_percentage'] = $stat['avg_marks'] ? 
                    round(($stat['avg_marks'] / $stat['max_marks']) * 100, 2) : null;
                $stat['min_percentage'] = $stat['min_marks'] ? 
                    round(($stat['min_marks'] / $stat['max_marks']) * 100, 2) : null;
                $stat['max_percentage'] = $stat['max_marks'] ? 
                    round(($stat['max_marks'] / $stat['max_marks']) * 100, 2) : null;
            }

            // Get grade distribution
            $gradeDistributionSql = "
                SELECT 
                    CASE 
                        WHEN overall_percentage >= 80 THEN 'A'
                        WHEN overall_percentage >= 70 THEN 'B'
                        WHEN overall_percentage >= 60 THEN 'C'
                        WHEN overall_percentage >= 50 THEN 'D'
                        ELSE 'F'
                    END as grade,
                    COUNT(*) as count
                FROM (
                    SELECT 
                        u.id,
                        AVG(CASE 
                            WHEN ac.component_type != 'final_exam' AND sm.marks_obtained IS NOT NULL
                            THEN (sm.marks_obtained / ac.max_marks) * 100 
                            ELSE NULL 
                        END) * 0.7 + 
                        AVG(CASE 
                            WHEN ac.component_type = 'final_exam' AND sm.marks_obtained IS NOT NULL
                            THEN (sm.marks_obtained / ac.max_marks) * 100 
                            ELSE NULL 
                        END) * 0.3 as overall_percentage
                    FROM course_enrollments ce
                    JOIN users u ON ce.student_id = u.id
                    LEFT JOIN assessment_components ac ON ce.course_id = ac.course_id AND ac.is_published = TRUE
                    LEFT JOIN student_marks sm ON ac.id = sm.assessment_component_id AND sm.student_id = u.id
                    WHERE ce.course_id = ? AND ce.status = 'active'
                    GROUP BY u.id
                    HAVING overall_percentage IS NOT NULL
                ) as student_grades
                GROUP BY grade
                ORDER BY grade
            ";

            $stmt = $this->db->prepare($gradeDistributionSql);
            $stmt->execute([$courseId]);
            $gradeDistribution = $stmt->fetchAll();

            return $this->successResponse($response, [
                'component_statistics' => $componentStats,
                'grade_distribution' => $gradeDistribution
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to fetch analytics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send notification to students
     * POST /api/lecturer/{lecturerId}/course/{courseId}/notifications
     */
    public function sendNotification(Request $request, Response $response, array $args): Response
    {
        try {
            $lecturerId = (int)$args['lecturerId'];
            $courseId = (int)$args['courseId'];
            $data = json_decode($request->getBody()->getContents(), true);

            // Verify lecturer access to this course
            if (!$this->verifyLecturerCourseAccess($lecturerId, $courseId)) {
                return $this->errorResponse($response, 'Access denied', 403);
            }

            $required = ['title', 'message'];
            $missing = $this->validateRequired($data, $required);
            if (!empty($missing)) {
                return $this->errorResponse($response, 'Missing required fields: ' . implode(', ', $missing));
            }

            // Get all students enrolled in this course
            $studentsSql = "
                SELECT u.id, u.username, u.full_name, u.email
                FROM course_enrollments ce
                JOIN users u ON ce.student_id = u.id
                WHERE ce.course_id = ? AND ce.status = 'active'
            ";

            $stmt = $this->db->prepare($studentsSql);
            $stmt->execute([$courseId]);
            $students = $stmt->fetchAll();

            $notificationCount = 0;

            foreach ($students as $student) {
                // Insert notification for each student
                $notificationSql = "
                    INSERT INTO notifications (user_id, title, message, type, sender_id, created_at)
                    VALUES (?, ?, ?, 'course_notification', ?, NOW())
                ";

                $stmt = $this->db->prepare($notificationSql);
                $stmt->execute([
                    $student['id'],
                    $data['title'],
                    $data['message'],
                    $lecturerId
                ]);

                $notificationCount++;
            }

            return $this->successResponse($response, [
                'message' => 'Notification sent successfully',
                'recipients_count' => $notificationCount
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Failed to send notification: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify lecturer has access to a specific course
     */
    private function verifyLecturerCourseAccess(int $lecturerId, int $courseId): bool
    {
        $sql = "SELECT COUNT(*) FROM course_lecturers WHERE lecturer_id = ? AND course_id = ? AND role = 'primary'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$lecturerId, $courseId]);
        return $stmt->fetchColumn() > 0;
    }
} 