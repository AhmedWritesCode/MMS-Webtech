<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CourseController extends BaseController
{
    public function getAll(Request $request, Response $response): Response
    {
        try {
            error_log('Fetching all courses');
            $stmt = $this->db->query("SELECT * FROM courses ORDER BY course_code ASC");
            $courses = $stmt->fetchAll();
            
            error_log('Found ' . count($courses) . ' courses');
            return $this->successResponse($response, $courses);
        } catch (\PDOException $e) {
            error_log('Database error while fetching courses: ' . $e->getMessage());
            return $this->errorResponse($response, "Failed to fetch courses: " . $e->getMessage(), 500);
        }
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            error_log('Creating new course: ' . json_encode($data));

            // Validate required fields
            $required = ['course_code', 'course_name', 'credits', 'semester', 'academic_year'];
            $missing = $this->validateRequired($data, $required);
            if (!empty($missing)) {
                error_log('Missing required fields: ' . implode(', ', $missing));
                return $this->errorResponse($response, "Missing required fields: " . implode(', ', $missing), 400);
            }

            // Check if course code already exists
            $existingCourse = $this->db->query("SELECT id FROM courses WHERE course_code = " . 
                $this->db->quote($data['course_code']))->fetch();
            if ($existingCourse) {
                error_log('Course code already exists: ' . $data['course_code']);
                return $this->errorResponse($response, "Course code already exists", 409);
            }

            $sql = "INSERT INTO courses (course_code, course_name, credits, semester, academic_year, is_active) 
                   VALUES (:course_code, :course_name, :credits, :semester, :academic_year, :is_active)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'course_code' => $data['course_code'],
                'course_name' => $data['course_name'],
                'credits' => $data['credits'],
                'semester' => $data['semester'],
                'academic_year' => $data['academic_year'],
                'is_active' => $data['is_active'] ?? true
            ]);

            $id = $this->db->lastInsertId();
            error_log('Course created successfully with ID: ' . $id);
            return $this->successResponse($response, ['id' => $id], 'Course created successfully');
        } catch (\PDOException $e) {
            error_log('Database error while creating course: ' . $e->getMessage());
            return $this->errorResponse($response, "Failed to create course: " . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();
            $id = $args['id'];
            error_log('Updating course ID ' . $id . ': ' . json_encode($data));

            // Validate required fields
            $required = ['course_code', 'course_name', 'credits', 'semester', 'academic_year'];
            $missing = $this->validateRequired($data, $required);
            if (!empty($missing)) {
                error_log('Missing required fields: ' . implode(', ', $missing));
                return $this->errorResponse($response, "Missing required fields: " . implode(', ', $missing), 400);
            }

            // Check if course exists
            $existingCourse = $this->db->query("SELECT id FROM courses WHERE id = " . 
                $this->db->quote($id))->fetch();
            if (!$existingCourse) {
                error_log('Course not found with ID: ' . $id);
                return $this->errorResponse($response, "Course not found", 404);
            }

            // Check if course code is unique (excluding current course)
            $duplicateCode = $this->db->query("SELECT id FROM courses WHERE course_code = " . 
                $this->db->quote($data['course_code']) . " AND id != " . $this->db->quote($id))->fetch();
            if ($duplicateCode) {
                error_log('Course code already exists: ' . $data['course_code']);
                return $this->errorResponse($response, "Course code already exists", 409);
            }

            $sql = "UPDATE courses SET 
                   course_code = :course_code,
                   course_name = :course_name,
                   credits = :credits,
                   semester = :semester,
                   academic_year = :academic_year,
                   is_active = :is_active
                   WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                'id' => $id,
                'course_code' => $data['course_code'],
                'course_name' => $data['course_name'],
                'credits' => $data['credits'],
                'semester' => $data['semester'],
                'academic_year' => $data['academic_year'],
                'is_active' => $data['is_active'] ?? true
            ]);

            error_log('Course updated successfully: ' . $id);
            return $this->successResponse($response, null, 'Course updated successfully');
        } catch (\PDOException $e) {
            error_log('Database error while updating course: ' . $e->getMessage());
            return $this->errorResponse($response, "Failed to update course: " . $e->getMessage(), 500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'];
            error_log('Deleting course ID: ' . $id);

            // Check if course exists
            $existingCourse = $this->db->query("SELECT id FROM courses WHERE id = " . 
                $this->db->quote($id))->fetch();
            if (!$existingCourse) {
                error_log('Course not found with ID: ' . $id);
                return $this->errorResponse($response, "Course not found", 404);
            }

            // Check if course has any dependencies
            $hasEnrollments = $this->db->query("SELECT COUNT(*) FROM course_enrollments WHERE course_id = " . 
                $this->db->quote($id))->fetchColumn();
            if ($hasEnrollments > 0) {
                error_log('Cannot delete course with existing enrollments: ' . $id);
                return $this->errorResponse($response, "Cannot delete course with existing enrollments", 409);
            }

            $sql = "DELETE FROM courses WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);

            error_log('Course deleted successfully: ' . $id);
            return $this->successResponse($response, null, 'Course deleted successfully');
        } catch (\PDOException $e) {
            error_log('Database error while deleting course: ' . $e->getMessage());
            return $this->errorResponse($response, "Failed to delete course: " . $e->getMessage(), 500);
        }
    }
}
