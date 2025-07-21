-- Course Mark Management System Database Schema

-- Users table - stores all system users (lecturers, students, advisors)
-- Think of this as the master directory of everyone who can access the system
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL, -- matric number for students, staff ID for others
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL, -- never store plain text passwords!
    full_name VARCHAR(200) NOT NULL,
    user_type ENUM('lecturer', 'student', 'advisor', 'admin') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE, -- allows us to deactivate users without deleting data
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Add indexes for frequently searched columns
    INDEX idx_username (username),
    INDEX idx_user_type (user_type),
    INDEX idx_email (email)
);

-- Courses table - stores information about academic courses
-- Each course can have multiple lecturers and many students enrolled
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) UNIQUE NOT NULL, -- e.g., 'SECJ3483'
    course_name VARCHAR(200) NOT NULL, -- e.g., 'Web Technology'
    credits INT NOT NULL DEFAULT 3,
    semester VARCHAR(20) NOT NULL, -- e.g., '2024/2025-1'
    academic_year VARCHAR(10) NOT NULL, -- e.g., '2024/2025'
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_course_code (course_code),
    INDEX idx_semester (semester, academic_year)
);

-- Course enrollments - links students to courses they're taking
-- This is a many-to-many relationship between users and courses
CREATE TABLE course_enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'dropped', 'completed') DEFAULT 'active',
    
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    
    -- Ensure a student can't be enrolled in the same course twice
    UNIQUE KEY unique_enrollment (student_id, course_id),
    INDEX idx_student_courses (student_id),
    INDEX idx_course_students (course_id)
);

-- Course assignments - links lecturers to courses they teach
CREATE TABLE course_lecturers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lecturer_id INT NOT NULL,
    course_id INT NOT NULL,
    role ENUM('primary', 'secondary') DEFAULT 'primary', -- primary lecturer vs assistant
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (lecturer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_assignment (lecturer_id, course_id),
    INDEX idx_lecturer_courses (lecturer_id),
    INDEX idx_course_lecturers (course_id)
);

-- Assessment components - defines the different types of assessments
-- This gives us flexibility to have different assessment structures per course
CREATE TABLE assessment_components (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    component_name VARCHAR(100) NOT NULL, -- e.g., 'Quiz 1', 'Assignment 1', 'Lab Exercise 3'
    component_type ENUM('quiz', 'assignment', 'exercise', 'lab', 'test', 'final_exam', 'other') NOT NULL,
    max_marks DECIMAL(5,2) NOT NULL, -- maximum possible marks (e.g., 100.00)
    weight_percentage DECIMAL(5,2) NOT NULL, -- contribution to final grade (e.g., 15.50 for 15.5%)
    due_date DATE NULL, -- when the assessment is due (optional)
    description TEXT, -- detailed description of the assessment
    is_published BOOLEAN DEFAULT FALSE, -- whether students can see this component yet
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course_components (course_id),
    INDEX idx_component_type (component_type)
);

-- Student marks - stores individual student scores for each assessment component
-- This is where the actual grades live
CREATE TABLE student_marks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    assessment_component_id INT NOT NULL,
    marks_obtained DECIMAL(5,2) NULL, -- actual marks scored (NULL if not yet graded)
    remarks TEXT, -- lecturer's comments or feedback
    graded_by INT NULL, -- which lecturer entered/updated these marks
    graded_at TIMESTAMP NULL, -- when the marks were entered
    is_final BOOLEAN DEFAULT FALSE, -- whether these marks are finalized
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_component_id) REFERENCES assessment_components(id) ON DELETE CASCADE,
    FOREIGN KEY (graded_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Ensure one mark entry per student per component
    UNIQUE KEY unique_student_assessment (student_id, assessment_component_id),
    INDEX idx_student_marks (student_id),
    INDEX idx_assessment_marks (assessment_component_id),
    INDEX idx_graded_by (graded_by)
);

-- Academic advisor assignments - links advisors to their advisees
CREATE TABLE advisor_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    advisor_id INT NOT NULL,
    student_id INT NOT NULL,
    assigned_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT, -- advisor's notes about the student
    
    FOREIGN KEY (advisor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_advisor_students (advisor_id),
    INDEX idx_student_advisor (student_id)
);

-- Remark requests - when students request mark reviews
CREATE TABLE remark_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    assessment_component_id INT NOT NULL,
    original_marks DECIMAL(5,2) NOT NULL, -- marks before the remark request
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    justification TEXT NOT NULL, -- student's reason for requesting remark
    status ENUM('pending', 'approved', 'rejected', 'under_review') DEFAULT 'pending',
    reviewed_by INT NULL, -- lecturer who handled the request
    review_comments TEXT, -- lecturer's response
    new_marks DECIMAL(5,2) NULL, -- updated marks if approved
    reviewed_at TIMESTAMP NULL,
    
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_component_id) REFERENCES assessment_components(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_student_requests (student_id),
    INDEX idx_status (status),
    INDEX idx_component_requests (assessment_component_id)
);

-- System notifications - for notifying users about mark updates, etc.
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('mark_update', 'remark_status', 'system_alert', 'general') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_notifications (user_id),
    INDEX idx_unread (user_id, is_read)
);

-- Audit log - tracks all important changes for security and debugging
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL, -- who made the change
    action VARCHAR(100) NOT NULL, -- what they did (e.g., 'UPDATE_MARKS', 'CREATE_USER')
    table_name VARCHAR(50) NOT NULL, -- which table was affected
    record_id INT NOT NULL, -- which specific record
    old_values JSON NULL, -- what the data looked like before
    new_values JSON NULL, -- what the data looks like after
    ip_address VARCHAR(45) NULL, -- user's IP address
    user_agent TEXT NULL, -- browser information
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_actions (user_id),
    INDEX idx_table_records (table_name, record_id),
    INDEX idx_action_time (action, created_at)
);

-- Insert some sample data to get started

-- Sample users (passwords would be hashed in real application)
-- Note: Using simple password hashes for development. In production, use proper password_hash()
INSERT INTO users (username, email, password_hash, full_name, user_type) VALUES
-- Test users that match frontend credentials
('A12345', 'student1@student.university.edu', '$2y$10$HSAO6n5HAW..DgBBGvq5T.C7DBQqsa84AROC.9LXQOWCiknDa0rgq', 'John Smith', 'student'),
('B23456', 'student2@student.university.edu', '$2y$10$HSAO6n5HAW..DgBBGvq5T.C7DBQqsa84AROC.9LXQOWCiknDa0rgq', 'Emily Davis', 'student'),
('L001', 'lecturer1@university.edu', '$2y$10$5LgACe02ICSd2UryTHure.8YFBp4aBAINystxarKV6Fox/ke4qdhK', 'Dr. Sarah Johnson', 'lecturer'),
('L002', 'lecturer2@university.edu', '$2y$10$5LgACe02ICSd2UryTHure.8YFBp4aBAINystxarKV6Fox/ke4qdhK', 'Prof. Michael Chen', 'lecturer'),
('ADV001', 'advisor1@university.edu', '$2y$10$QMzy9KK6HTO3NJOu5b0Cgu98SBWhvqWk8NPKXDKw8G.ehfk47fvki', 'Dr. Amanda Wilson', 'advisor'),
('ADV002', 'advisor2@university.edu', '$2y$10$QMzy9KK6HTO3NJOu5b0Cgu98SBWhvqWk8NPKXDKw8G.ehfk47fvki', 'Dr. Robert Brown', 'advisor'),
('admin', 'admin@university.edu', '$2y$10$Z/vBVjkPICN9um8AnW5/a.X0kBv/nqDK70jus44vYXSlouwhqX2Si', 'System Administrator', 'admin');

-- Sample courses
INSERT INTO courses (course_code, course_name, credits, semester, academic_year) VALUES
('SECJ3483', 'Web Technology', 3, '2024/2025-1', '2024/2025'),
('SECJ2203', 'Database Systems', 3, '2024/2025-1', '2024/2025'),
('SECJ3103', 'Software Engineering', 3, '2024/2025-1', '2024/2025'),
('SECJ1013', 'Programming Technique I', 3, '2024/2025-1', '2024/2025'),
('SECJ2154', 'Object Oriented Programming', 4, '2024/2025-1', '2024/2025'),
('SECJ2303', 'Computer Networks', 3, '2024/2025-1', '2024/2025');

-- Link lecturers to courses
INSERT INTO course_lecturers (lecturer_id, course_id, role) VALUES
(3, 1, 'primary'), -- Dr. Sarah teaches Web Technology
(4, 2, 'primary'), -- Prof. Michael teaches Database Systems
(3, 3, 'primary'), -- Dr. Sarah also teaches Software Engineering
(4, 4, 'primary'), -- Prof. Michael teaches Programming Technique I
(3, 5, 'primary'), -- Dr. Sarah teaches Object Oriented Programming
(4, 6, 'primary'); -- Prof. Michael teaches Computer Networks

-- Enroll students in courses
INSERT INTO course_enrollments (student_id, course_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), -- John takes all courses
(2, 1), (2, 2), (2, 4), (2, 6); -- Emily takes Web Tech, Database, Programming, Networks

-- Assign advisor to students
INSERT INTO advisor_assignments (advisor_id, student_id, assigned_date) VALUES
(5, 1, '2024-09-01'), -- Dr. Amanda advises John
(6, 2, '2024-09-01'); -- Dr. Robert advises Emily

-- Sample assessment components for Web Technology course
INSERT INTO assessment_components (course_id, component_name, component_type, max_marks, weight_percentage, description, is_published) VALUES
(1, 'Quiz 1', 'quiz', 20.00, 10.00, 'HTML and CSS Fundamentals', TRUE),
(1, 'Assignment 1', 'assignment', 100.00, 15.00, 'Build a responsive website using HTML/CSS', TRUE),
(1, 'Lab Exercise 1', 'lab', 50.00, 5.00, 'JavaScript DOM Manipulation', TRUE),
(1, 'Test 1', 'test', 100.00, 20.00, 'Mid-semester examination covering HTML, CSS, JavaScript', TRUE),
(1, 'Assignment 2', 'assignment', 100.00, 20.00, 'Full-stack web application using PHP and MySQL', TRUE),
(1, 'Final Exam', 'final_exam', 100.00, 30.00, 'Comprehensive final examination', FALSE);

-- Sample assessment components for Database Systems course
INSERT INTO assessment_components (course_id, component_name, component_type, max_marks, weight_percentage, description, is_published) VALUES
(2, 'Quiz 1', 'quiz', 15.00, 10.00, 'Database Fundamentals', TRUE),
(2, 'Assignment 1', 'assignment', 100.00, 20.00, 'ERD Design and Normalization', TRUE),
(2, 'Lab Exercise 1', 'lab', 50.00, 10.00, 'SQL Basics', TRUE),
(2, 'Test 1', 'test', 100.00, 25.00, 'Mid-semester examination', TRUE),
(2, 'Assignment 2', 'assignment', 100.00, 25.00, 'Database Application Development', TRUE),
(2, 'Final Exam', 'final_exam', 100.00, 10.00, 'Final examination', FALSE);

-- Sample assessment components for Software Engineering course
INSERT INTO assessment_components (course_id, component_name, component_type, max_marks, weight_percentage, description, is_published) VALUES
(3, 'Quiz 1', 'quiz', 20.00, 10.00, 'Software Development Life Cycle', TRUE),
(3, 'Assignment 1', 'assignment', 100.00, 20.00, 'Requirements Engineering', TRUE),
(3, 'Lab Exercise 1', 'lab', 50.00, 10.00, 'UML Modeling', TRUE),
(3, 'Test 1', 'test', 100.00, 25.00, 'Mid-semester examination', TRUE),
(3, 'Assignment 2', 'assignment', 100.00, 25.00, 'Software Design and Architecture', TRUE),
(3, 'Final Exam', 'final_exam', 100.00, 10.00, 'Final examination', FALSE);

-- Sample marks for John Smith (A12345) - Web Technology
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(1, 1, 18.00, 3, NOW(), TRUE), -- Quiz 1: 18/20
(1, 2, 85.00, 3, NOW(), TRUE), -- Assignment 1: 85/100
(1, 3, 45.00, 3, NOW(), TRUE), -- Lab 1: 45/50
(1, 4, 78.00, 3, NOW(), TRUE), -- Test 1: 78/100
(1, 5, 92.00, 3, NOW(), TRUE); -- Assignment 2: 92/100

-- Sample marks for John Smith (A12345) - Database Systems
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(1, 7, 14.00, 4, NOW(), TRUE), -- Quiz 1: 14/15
(1, 8, 88.00, 4, NOW(), TRUE), -- Assignment 1: 88/100
(1, 9, 48.00, 4, NOW(), TRUE), -- Lab 1: 48/50
(1, 10, 82.00, 4, NOW(), TRUE), -- Test 1: 82/100
(1, 11, 95.00, 4, NOW(), TRUE); -- Assignment 2: 95/100

-- Sample marks for John Smith (A12345) - Software Engineering
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(1, 13, 17.00, 3, NOW(), TRUE), -- Quiz 1: 17/20
(1, 14, 90.00, 3, NOW(), TRUE), -- Assignment 1: 90/100
(1, 15, 47.00, 3, NOW(), TRUE), -- Lab 1: 47/50
(1, 16, 85.00, 3, NOW(), TRUE), -- Test 1: 85/100
(1, 17, 88.00, 3, NOW(), TRUE); -- Assignment 2: 88/100

-- Sample marks for Emily Davis (B23456) - Web Technology
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(2, 1, 16.00, 3, NOW(), TRUE), -- Quiz 1: 16/20
(2, 2, 92.00, 3, NOW(), TRUE), -- Assignment 1: 92/100
(2, 3, 48.00, 3, NOW(), TRUE), -- Lab 1: 48/50
(2, 4, 85.00, 3, NOW(), TRUE), -- Test 1: 85/100
(2, 5, 89.00, 3, NOW(), TRUE); -- Assignment 2: 89/100

-- Sample marks for Emily Davis (B23456) - Database Systems
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(2, 7, 13.00, 4, NOW(), TRUE), -- Quiz 1: 13/15
(2, 8, 95.00, 4, NOW(), TRUE), -- Assignment 1: 95/100
(2, 9, 50.00, 4, NOW(), TRUE), -- Lab 1: 50/50
(2, 10, 88.00, 4, NOW(), TRUE), -- Test 1: 88/100
(2, 11, 92.00, 4, NOW(), TRUE); -- Assignment 2: 92/100