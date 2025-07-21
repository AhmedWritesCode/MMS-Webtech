# 🎓 Marks Management System (MMS)

A full-featured web application for managing and monitoring student course marks. Built using **Vue.js** (frontend), **PHP Slim Framework** (backend), and **MySQL** (database), the system supports multiple user roles including lecturers, students, and academic advisors.

---

## 📁 Project Structure

```
marks-management-system/
├── frontend/   # Vue.js frontend app
└── backend/    # PHP Slim backend API
```

---

## 🚀 Technologies Used

| Layer           | Technology                        |
|----------------|-----------------------------------|
| Frontend        | Vue.js (Vue CLI), Axios, Chart.js |
| Backend         | PHP Slim Framework, Composer      |
| Database        | MySQL                             |
| API Comm        | RESTful API (JSON)                |
| Version Control | Git + GitHub                      |

---

## 👥 User Roles & Features

### 👨‍🏫 Lecturer
- Secure login
- Manage student records
- Add/edit continuous assessment marks (70%)
- Enter final exam marks (30%)
- Auto-calculate total marks
- Visual analytics (progress charts)
- Export as CSV
- Notify students of mark updates

### 👩‍🎓 Student
- Login with matric number and PIN
- View marks and breakdown
- Progress bar visualization
- Anonymous comparison with classmates
- View rank and percentile
- Simulate marks ("What-if" tool)
- Request remark with justification

### 👨‍💼 Academic Advisor
- Secure login
- Access advisee's mark breakdown
- Highlight at-risk students (GPA < 2.0)
- Add notes & consultation history
- Export consultation reports

### 🛠 Admin (Optional)
- Manage users and roles
- Assign lecturers to courses
- View logs and activity
- Reset passwords

---

## 🧪 Project Setup

### 1. Clone the Repository

```bash
git clone https://github.com/YOUR-ORG-NAME/marks-management-system.git
cd marks-management-system
```

### 2. Setup Frontend

```bash
cd frontend
npm install
npm run serve
```

### 3. Setup Backend

```bash
cd backend
composer install
php -S localhost:8080 -t public
```

> ⚠️ Configure database connection in the `.env` file inside the `backend/` folder.

---

## 📦 Database Setup

### 1. Create the Database

```sql
CREATE DATABASE course_marks_db;
```

### 2. Import the Schema

```bash
mysql -u your_username -p course_marks_db < database_schema.sql
```

### 3. Test the Connection

1. Make sure `db_test.php` exists in `backend/`
2. Run:

```bash
php -S localhost:8080
```

3. Visit: [http://localhost:8080/db_test.php](http://localhost:8080/db_test.php)

---

## 📌 Notes

- Ensure CORS is enabled in the backend.
- Use `.env` files to manage API URLs and DB credentials.
- Use Git branching (`frontend-dev`, `backend-dev`, `main`) for team collaboration.

---

## 👥 Team Members

- **Ahmed Zaki Al-Gabaly**
- **Waleed Othman Algidi**

---

## 🧩 Team Responsibilities

### 🔐 Ahmed Zaki Al-Gabaly – Authentication & Assessment Management

**Authentication Module**
- Secure login for all roles
- Role-based access control (RBAC)
- Admin password reset

**Assessment Module**
- Continuous Assessment (70%) – create, edit, input marks
- Final Exam (30%) – input marks
- Bulk upload marks via CSV
- Export student marks as CSV
- Lecturer feedback/remarks per student

---

### 🧑‍💼 Waleed Othman Algidi – Student Performance & Advisor Module

**Student Performance Module**
- View marks and breakdown
- Visual progress bar, assessment analytics
- Anonymized class comparison
- Class rank and percentile view
- "What-If" grade simulator

**Advisor Module**
- View advisees’ marks and history
- Highlight at-risk students (GPA < 2.0)
- Add consultation notes
- Export advisor reports

---

## 📃 License

This project is developed for academic purposes as part of the Web Technology course (SECJ3483). Not for commercial use.
