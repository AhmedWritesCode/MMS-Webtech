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
('S98765', 'susan.lee@student.university.edu', '$2y$10$HSAO6n5HAW..DgBBGvq5T.C7DBQqsa84AROC.9LXQOWCiknDa0rgq', 'Susan Lee', 'student'),
('S87654', 'david.kim@student.university.edu', '$2y$10$HSAO6n5HAW..DgBBGvq5T.C7DBQqsa84AROC.9LXQOWCiknDa0rgq', 'David Kim', 'student'),
('L003', 'lecturer3@university.edu', '$2y$10$5LgACe02ICSd2UryTHure.8YFBp4aBAINystxarKV6Fox/ke4qdhK', 'Dr. Lena Schmidt', 'lecturer'),
('L004', 'lecturer4@university.edu', '$2y$10$5LgACe02ICSd2UryTHure.8YFBp4aBAINystxarKV6Fox/ke4qdhK', 'Prof. Omar Hassan', 'lecturer'),
('ADV003', 'advisor3@university.edu', '$2y$10$QMzy9KK6HTO3NJOu5b0Cgu98SBWhvqWk8NPKXDKw8G.ehfk47fvki', 'Dr. Chloe Green', 'advisor'),
('ADV004', 'advisor4@university.edu', '$2y$10$QMzy9KK6HTO3NJOu5b0Cgu98SBWhvqWk8NPKXDKw8G.ehfk47fvki', 'Dr. Ben Carter', 'advisor'),
('admin', 'admin@university.edu', '$2y$10$Z/vBVjkPICN9um8AnW5/a.X0kBv/nqDK70jus44vYXSlouwhqX2Si', 'System Administrator', 'admin');

-- Sample courses (refreshing data)
INSERT INTO courses (course_code, course_name, credits, semester, academic_year) VALUES
('HRM301', 'Human Resource Management', 3, '2025/2026-1', '2025/2026'),
('FIN405', 'Financial Accounting', 3, '2025/2026-1', '2025/2026'),
('MKT202', 'Principles of Marketing', 3, '2025/2026-1', '2025/2026'),
('ECO101', 'Microeconomics', 3, '2025/2026-1', '2025/2026'),
('PSY305', 'Organizational Psychology', 4, '2025/2026-1', '2025/2026'),
('COM210', 'Business Communication', 3, '2025/2026-1', '2025/2026');

-- Link lecturers to courses (refreshing data)
INSERT INTO course_lecturers (lecturer_id, course_id, role) VALUES
(3, 7, 'primary'), -- Dr. Lena teaches Human Resource Management (assuming IDs increment)
(4, 8, 'primary'), -- Prof. Omar teaches Financial Accounting
(3, 9, 'primary'), -- Dr. Lena also teaches Principles of Marketing
(4, 10, 'primary'), -- Prof. Omar teaches Microeconomics
(3, 11, 'primary'), -- Dr. Lena teaches Organizational Psychology
(4, 12, 'primary'); -- Prof. Omar teaches Business Communication

-- Enroll students in courses (refreshing data)
INSERT INTO course_enrollments (student_id, course_id) VALUES
(1, 7), (1, 8), (1, 9), (1, 10), (1, 11), -- Susan takes all new courses
(2, 7), (2, 8), (2, 10), (2, 12); -- David takes HRM, Financial Accounting, Microeconomics, Business Communication

-- Assign advisor to students (refreshing data)
INSERT INTO advisor_assignments (advisor_id, student_id, assigned_date) VALUES
(5, 1, '2025-09-01'), -- Dr. Chloe advises Susan
(6, 2, '2025-09-01'); -- Dr. Ben advises David

-- Sample assessment components for Human Resource Management course
INSERT INTO assessment_components (course_id, component_name, component_type, max_marks, weight_percentage, description, is_published) VALUES
(7, 'Case Study 1', 'assignment', 50.00, 20.00, 'Analyzing HR Challenges', TRUE),
(7, 'Midterm Exam', 'test', 100.00, 30.00, 'Concepts in HRM', TRUE),
(7, 'Presentation', 'other', 40.00, 15.00, 'Group Presentation on HR Trends', TRUE),
(7, 'Participation', 'other', 20.00, 5.00, 'Class Participation and Discussion', TRUE),
(7, 'Final Project', 'assignment', 100.00, 30.00, 'HR Strategy Development', FALSE);

-- Sample assessment components for Financial Accounting course
INSERT INTO assessment_components (course_id, component_name, component_type, max_marks, weight_percentage, description, is_published) VALUES
(8, 'Quiz 1', 'quiz', 25.00, 10.00, 'Accounting Principles', TRUE),
(8, 'Problem Set 1', 'assignment', 75.00, 20.00, 'Journal Entries and Ledgers', TRUE),
(8, 'Midterm Exam', 'test', 100.00, 35.00, 'Financial Statement Analysis', TRUE),
(8, 'Case Study', 'assignment', 60.00, 15.00, 'Company Financial Report Analysis', TRUE),
(8, 'Final Exam', 'final_exam', 100.00, 20.00, 'Comprehensive Final', FALSE);

-- Sample assessment components for Principles of Marketing course
INSERT INTO assessment_components (course_id, component_name, component_type, max_marks, weight_percentage, description, is_published) VALUES
(9, 'Marketing Plan Proposal', 'assignment', 80.00, 25.00, 'Product/Service Marketing Plan', TRUE),
(9, 'Quiz 1', 'quiz', 30.00, 10.00, 'Market Segmentation', TRUE),
(9, 'Mid-Term Test', 'test', 100.00, 30.00, 'Marketing Mix Elements', TRUE),
(9, 'Market Research Report', 'assignment', 70.00, 20.00, 'Consumer Behavior Analysis', TRUE),
(9, 'Final Exam', 'final_exam', 100.00, 15.00, 'Overall Marketing Concepts', FALSE);

-- Sample marks for Susan Lee (S98765) - Human Resource Management
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(1, 19, 42.00, 3, NOW(), TRUE), -- Case Study 1: 42/50
(1, 20, 88.00, 3, NOW(), TRUE), -- Midterm Exam: 88/100
(1, 21, 35.00, 3, NOW(), TRUE), -- Presentation: 35/40
(1, 22, 18.00, 3, NOW(), TRUE); -- Participation: 18/20

-- Sample marks for Susan Lee (S98765) - Financial Accounting
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(1, 24, 23.00, 4, NOW(), TRUE), -- Quiz 1: 23/25
(1, 25, 68.00, 4, NOW(), TRUE), -- Problem Set 1: 68/75
(1, 26, 82.00, 4, NOW(), TRUE), -- Midterm Exam: 82/100
(1, 27, 55.00, 4, NOW(), TRUE); -- Case Study: 55/60

-- Sample marks for Susan Lee (S98765) - Principles of Marketing
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(1, 29, 75.00, 3, NOW(), TRUE), -- Marketing Plan Proposal: 75/80
(1, 30, 28.00, 3, NOW(), TRUE), -- Quiz 1: 28/30
(1, 31, 80.00, 3, NOW(), TRUE), -- Mid-Term Test: 80/100
(1, 32, 65.00, 3, NOW(), TRUE); -- Market Research Report: 65/70

-- Sample marks for David Kim (S87654) - Human Resource Management
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(2, 19, 38.00, 3, NOW(), TRUE), -- Case Study 1: 38/50
(2, 20, 91.00, 3, NOW(), TRUE), -- Midterm Exam: 91/100
(2, 21, 39.00, 3, NOW(), TRUE), -- Presentation: 39/40
(2, 22, 20.00, 3, NOW(), TRUE); -- Participation: 20/20

-- Sample marks for David Kim (S87654) - Financial Accounting
INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, graded_by, graded_at, is_final) VALUES
(2, 24, 20.00, 4, NOW(), TRUE), -- Quiz 1: 20/25
(2, 25, 72.00, 4, NOW(), TRUE), -- Problem Set 1: 72/75
(2, 26, 85.00, 4, NOW(), TRUE), -- Midterm Exam: 85/100
(2, 27, 58.00, 4, NOW(), TRUE); -- Case Study: 58/60
