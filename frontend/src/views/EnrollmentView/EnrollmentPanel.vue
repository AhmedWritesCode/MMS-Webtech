<template>
  <div class="enrollment-panel">
    <h2>📚 Enrollment Panel</h2>

    <!-- Alerts -->
    <div v-if="message" :class="['alert-message', messageType]">
      {{ message }}
    </div>

    <!-- Course Dropdown -->
    <div class="form-group">
      <label for="course-select">Course Code</label>
      <select
        id="course-select"
        v-model="selectedCourseId"
        @change="fetchEnrollments"
      >
        <option value="">-- Select Course --</option>
        <option v-for="course in courses" :key="course.id" :value="course.id">
          {{ course.course_code }} - {{ course.course_name }}
        </option>
      </select>
    </div>

    <!-- Loading State -->
    <p v-if="loading">Loading...</p>

    <!-- Student List with Enroll/Unenroll -->
    <div v-if="!loading && students.length > 0" class="student-box">
      <h3>👥 Students</h3>
      <ul class="student-list">
        <li v-for="student in students" :key="student.id">
          <span
            ><strong>{{ student.name || "N/A" }}</strong></span
          >
          <div>
            <button
              v-if="!isEnrolled(student.id)"
              class="btn small primary"
              @click="openEnrollModal(student)"
            >
              Enroll
            </button>
            <button
              v-if="isEnrolled(student.id)"
              class="btn small delete"
              @click="confirmUnenroll(student)"
            >
              🗑️ Unenroll
            </button>
          </div>
        </li>
      </ul>
    </div>
    <p v-if="!loading && !error && students.length === 0">
      No students available.
    </p>

    <!-- Enroll Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <h3>Enroll Student</h3>
        <form @submit.prevent="enrollStudent">
          <div class="form-group">
            <label for="course-select-modal">Course</label>
            <select id="course-select-modal" v-model="form.course_id" required>
              <option value="">-- Select Course --</option>
              <option
                v-for="course in courses"
                :key="course.id"
                :value="course.id"
              >
                {{ course.course_code }} - {{ course.course_name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="status-select">Status</label>
            <select id="status-select" v-model="form.status" required>
              <option value="">-- Select Status --</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="modal-actions">
            <button type="submit" class="btn primary">Enroll</button>
            <button type="button" @click="closeModal" class="btn">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Unenroll Confirmation Modal -->
    <div v-if="showUnenrollModal" class="modal-overlay">
      <div class="modal">
        <p>
          Unenroll <strong>{{ selectedStudent?.name }}</strong> from
          <strong>{{ selectedCourse?.course_code }}</strong
          >?
        </p>
        <div class="modal-actions">
          <button @click="unenrollStudent" class="btn delete">Yes</button>
          <button @click="showUnenrollModal = false" class="btn">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/services/api";

export default {
  name: "EnrollmentPanel",
  data() {
    return {
      courses: [],
      students: [],
      enrollments: [],
      selectedCourseId: "",
      loading: false,
      error: null,
      message: "",
      messageType: "",
      showModal: false,
      showUnenrollModal: false,
      selectedStudent: null,
      selectedCourse: null,
      form: {
        student_id: "",
        course_id: "",
        status: "active",
      },
    };
  },
  mounted() {
    this.fetchStudents();
    this.fetchEnrollments();
  },
  methods: {
    async fetchStudents() {
      try {
        this.loading = true;
        const response = await api.get("/users?type=student");
        console.log("Students Response:", response.data);
        this.students = response.data
          .filter((user) => user.user_type === "student")
          .map((user) => ({
            id: user.id,
            name: user.full_name || user.username || "Unnamed",
          }));
        if (this.students.length === 0) {
          this.showMessage("⚠️ No students found.", "error");
        }
      } catch (error) {
        console.error("Error fetching students:", {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status,
        });
        this.showMessage(
          "❌ Failed to fetch students. Check console for details.",
          "error"
        );
      } finally {
        this.loading = false;
      }
    },
    async fetchEnrollments() {
      try {
        this.loading = true;
        const url = this.selectedCourseId
          ? `/course_enrollments/course/${this.selectedCourseId}`
          : "/course_enrollments";
        const response = await api.get(url);
        console.log("Enrollments Response:", response.data);
        this.enrollments = Array.isArray(response.data) ? response.data : [];

        // Derive unique courses from enrollments
        const courseMap = new Map();
        this.enrollments.forEach((enrollment) => {
          if (
            enrollment.course_id &&
            enrollment.course_code &&
            enrollment.course_name
          ) {
            courseMap.set(enrollment.course_id, {
              id: enrollment.course_id,
              course_code: enrollment.course_code,
              course_name: enrollment.course_name,
            });
          }
        });
        this.courses = Array.from(courseMap.values());
        console.log("Derived Courses:", this.courses);

        if (this.courses.length === 0) {
          this.showMessage("⚠️ No courses found in enrollments.", "error");
        }
      } catch (error) {
        console.error("Error fetching enrollments:", {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status,
        });
        this.showMessage(
          "❌ Failed to load enrollments. Check console for details.",
          "error"
        );
        this.error = "Failed to load enrollments.";
      } finally {
        this.loading = false;
      }
    },
    isEnrolled(studentId) {
      if (!this.selectedCourseId) return false;
      return this.enrollments.some(
        (enrollment) =>
          enrollment.student_id === studentId &&
          enrollment.course_id === parseInt(this.selectedCourseId)
      );
    },
    openEnrollModal(student) {
      this.selectedStudent = student;
      this.form = {
        student_id: student.id,
        course_id: this.selectedCourseId || "",
        status: "active",
      };
      this.showModal = true;
    },
    confirmUnenroll(student) {
      this.selectedStudent = student;
      this.selectedCourse = this.courses.find(
        (course) => course.id === parseInt(this.selectedCourseId)
      );
      this.showUnenrollModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.form = { student_id: "", course_id: "", status: "active" };
      this.selectedStudent = null;
    },
    async enrollStudent() {
      try {
        await api.post("/course_enrollments", {
          ...this.form,
          enrollment_date: new Date()
            .toISOString()
            .slice(0, 19)
            .replace("T", " "),
        });
        this.showMessage("✅ Student enrolled successfully!", "success");
        this.fetchEnrollments();
        this.closeModal();
      } catch (error) {
        console.error("Error enrolling student:", {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status,
        });
        this.showMessage(
          "❌ Failed to enroll student. Check console for details.",
          "error"
        );
      }
    },
    async unenrollStudent() {
      try {
        const enrollment = this.enrollments.find(
          (e) =>
            e.student_id === this.selectedStudent.id &&
            e.course_id === parseInt(this.selectedCourseId)
        );
        if (!enrollment) {
          this.showMessage("❌ Enrollment not found.", "error");
          return;
        }
        await api.delete(`/course_enrollments/${enrollment.id}`);
        this.showMessage("✅ Student unenrolled successfully!", "success");
        this.fetchEnrollments();
        this.showUnenrollModal = false;
        this.selectedStudent = null;
        this.selectedCourse = null;
      } catch (error) {
        console.error("Error unenrolling student:", {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status,
        });
        this.showMessage(
          "❌ Failed to unenroll student. Check console for details.",
          "error"
        );
      }
    },
    showMessage(msg, type) {
      this.message = msg;
      this.messageType = type;
      setTimeout(() => {
        this.message = "";
        this.messageType = "";
      }, 3000);
    },
  },
};
</script>

<style scoped>
.enrollment-panel {
  max-width: 700px;
  margin: auto;
  padding: 2rem;
  background: #f9f9f9;
  color: #333;
  border-radius: 12px;
  font-family: "Segoe UI", sans-serif;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

h2,
h3 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #2c3e50;
}

.alert-message.success {
  background: #e0f7e9;
  color: #2e7d32;
  padding: 0.75rem;
  margin-bottom: 1rem;
  border-radius: 6px;
}
.alert-message.error {
  background: #fdecea;
  color: #c62828;
  padding: 0.75rem;
  margin-bottom: 1rem;
  border-radius: 6px;
}

.form-group {
  margin-bottom: 1.2rem;
}
label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
}
select {
  width: 100%;
  padding: 0.55rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  background: #fff;
  color: #333;
  font-size: 0.95rem;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  margin-right: 0.5rem;
  border-radius: 6px;
  cursor: pointer;
  background: #ddd;
  color: #000;
  transition: background 0.3s ease;
}
.btn.primary {
  background: #007bff;
  color: #fff;
}
.btn.primary:hover {
  background: #0056b3;
}
.btn.delete {
  background: #e53935;
  color: #fff;
}
.btn.delete:hover {
  background: #b71c1c;
}
.btn.small {
  font-size: 0.8rem;
  padding: 0.3rem 0.6rem;
}

.student-box {
  margin-top: 2rem;
}
.student-list {
  list-style: none;
  padding: 0;
}
.student-list li {
  background: #f1f1f1;
  padding: 0.75rem;
  margin-bottom: 0.5rem;
  border-radius: 6px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #333;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}
.modal {
  background: #fff;
  padding: 2rem;
  border-radius: 10px;
  color: #000;
  width: 90%;
  max-width: 400px;
  text-align: center;
}
.modal-actions {
  margin-top: 1rem;
  display: flex;
  justify-content: space-evenly;
}
</style>
