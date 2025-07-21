<template>
  <div class="lecturer-students-view">
    <div class="page-header">
      <h1>Course Students</h1>
      <p>Course ID: {{ $route.params.courseId }}</p>
      <p>Manage and view student performance in this course</p>
    </div>

    <div class="content-section">
      <div class="section-header">
        <h2>Student List</h2>
        <div class="header-actions">
          <button class="action-btn" @click="exportStudentData">
            <span class="btn-icon">📊</span>
            Export Data
          </button>
          <button class="action-btn" @click="bulkUploadMarks">
            <span class="btn-icon">📝</span>
            Bulk Upload
          </button>
        </div>
      </div>

      <div class="students-table">
        <div v-if="loading" class="loading">Loading students...</div>

        <div v-else-if="students.length === 0" class="empty-state">
          <div class="empty-icon">👥</div>
          <h3>No students found</h3>
          <p>No students are currently enrolled in this course.</p>
        </div>

        <div v-else class="table-container">
          <table class="students-table">
            <thead>
              <tr>
                <th>Student</th>
                <th>Matric Number</th>
                <th>Email</th>
                <th>Enrollment Date</th>
                <th>Overall %</th>
                <th>Grade</th>
                <th>Completion</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="student in students" :key="student.student_id">
                <td class="student-info">
                  <div class="student-name">{{ student.full_name }}</div>
                </td>
                <td>{{ student.matric_number }}</td>
                <td>{{ student.email }}</td>
                <td>{{ formatDate(student.enrollment_date) }}</td>
                <td class="performance">
                  <span
                    v-if="student.overall_percentage !== null"
                    class="percentage"
                  >
                    {{ student.overall_percentage }}%
                  </span>
                  <span v-else class="no-data">-</span>
                </td>
                <td class="grade">
                  <span
                    v-if="student.grade_letter"
                    :class="[
                      'grade-badge',
                      getGradeClass(student.grade_letter),
                    ]"
                  >
                    {{ student.grade_letter }}
                  </span>
                  <span v-else class="no-data">-</span>
                </td>
                <td class="completion">
                  <div class="completion-bar">
                    <div
                      class="completion-fill"
                      :style="{ width: student.completion_rate + '%' }"
                      :class="getCompletionClass(student.completion_rate)"
                    ></div>
                  </div>
                  <span class="completion-text"
                    >{{ student.completion_rate }}%</span
                  >
                </td>
                <td class="actions">
                  <button
                    @click="viewStudentDetails(student.student_id)"
                    class="action-btn small"
                  >
                    View
                  </button>
                  <button
                    @click="editMarks(student.student_id)"
                    class="action-btn small"
                  >
                    Edit Marks
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Student Details Modal -->
    <div
      v-if="showStudentModal"
      class="modal-overlay"
      @click="closeStudentModal"
    >
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Student Details</h3>
          <button @click="closeStudentModal" class="close-btn">×</button>
        </div>
        <div class="modal-body">
          <div v-if="selectedStudent" class="student-details">
            <div class="detail-row">
              <strong>Name:</strong> {{ selectedStudent.full_name }}
            </div>
            <div class="detail-row">
              <strong>Matric Number:</strong>
              {{ selectedStudent.matric_number }}
            </div>
            <div class="detail-row">
              <strong>Email:</strong> {{ selectedStudent.email }}
            </div>
            <div class="detail-row">
              <strong>Enrollment Date:</strong>
              {{ formatDate(selectedStudent.enrollment_date) }}
            </div>
            <div class="detail-row">
              <strong>Overall Performance:</strong>
              <span v-if="selectedStudent.overall_percentage !== null">
                {{ selectedStudent.overall_percentage }}% ({{
                  selectedStudent.grade_letter
                }})
              </span>
              <span v-else>Not available</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { lecturerAPI } from "@/services/api";

export default {
  name: "LecturerStudentsView",
  data() {
    return {
      loading: false,
      students: [],
      showStudentModal: false,
      selectedStudent: null,
    };
  },
  async mounted() {
    await this.loadStudents();
  },
  methods: {
    async loadStudents() {
      this.loading = true;
      try {
        const courseId = this.$route.params.courseId;
        const response = await lecturerAPI.getCourseStudents(courseId);
        if (response.success) {
          this.students = response.data.students || [];
        } else {
          console.error("Failed to load students:", response.message);
        }
      } catch (error) {
        console.error("Error loading students:", error);
      } finally {
        this.loading = false;
      }
    },

    viewStudentDetails(studentId) {
      this.selectedStudent = this.students.find(
        (s) => s.student_id === studentId
      );
      this.showStudentModal = true;
    },

    closeStudentModal() {
      this.showStudentModal = false;
      this.selectedStudent = null;
    },

    editMarks(studentId) {
      // Navigate to marks editing page
      this.$router.push(
        `/lecturer/course/${this.$route.params.courseId}/student/${studentId}/marks`
      );
    },

    async exportStudentData() {
      try {
        const courseId = this.$route.params.courseId;
        const response = await lecturerAPI.exportMarks(courseId);
        if (response.success) {
          // Create download link
          const blob = new Blob([response.data], { type: "text/csv" });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = response.filename || "student_data.csv";
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        }
      } catch (error) {
        console.error("Error exporting data:", error);
        alert("Failed to export student data");
      }
    },

    bulkUploadMarks() {
      // Navigate to bulk upload page
      this.$router.push(
        `/lecturer/course/${this.$route.params.courseId}/bulk-upload`
      );
    },

    getGradeClass(grade) {
      const gradeClasses = {
        A: "grade-a",
        B: "grade-b",
        C: "grade-c",
        D: "grade-d",
        F: "grade-f",
      };
      return gradeClasses[grade] || "grade-unknown";
    },

    getCompletionClass(percentage) {
      if (percentage >= 80) return "completion-excellent";
      if (percentage >= 60) return "completion-good";
      if (percentage >= 40) return "completion-warning";
      return "completion-poor";
    },

    formatDate(dateString) {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },
  },
};
</script>

<style scoped>
.lecturer-students-view {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 32px;
}

.page-header h1 {
  margin: 0 0 8px 0;
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
}

.page-header p {
  margin: 4px 0;
  color: #64748b;
}

.content-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.section-header h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.action-btn {
  display: flex;
  align-items: center;
  padding: 8px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 500;
}

.action-btn:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.action-btn.small {
  padding: 4px 8px;
  font-size: 0.8rem;
}

.btn-icon {
  margin-right: 6px;
  font-size: 1rem;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #6b7280;
}

.empty-state {
  text-align: center;
  padding: 48px 24px;
  color: #6b7280;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 16px;
}

.empty-state h3 {
  margin: 0 0 8px 0;
  font-size: 1.2rem;
}

.table-container {
  overflow-x: auto;
}

.students-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}

.students-table th {
  background: #f8fafc;
  padding: 12px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 2px solid #e2e8f0;
}

.students-table td {
  padding: 12px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.students-table tr:hover {
  background: #f8fafc;
}

.student-name {
  font-weight: 600;
  color: #1e293b;
}

.percentage {
  font-weight: 600;
  color: #1e293b;
}

.no-data {
  color: #9ca3af;
  font-style: italic;
}

.grade-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  text-align: center;
  min-width: 24px;
  display: inline-block;
}

.grade-a {
  background: #dcfce7;
  color: #166534;
}

.grade-b {
  background: #dbeafe;
  color: #1e40af;
}

.grade-c {
  background: #fef3c7;
  color: #92400e;
}

.grade-d {
  background: #fed7aa;
  color: #c2410c;
}

.grade-f {
  background: #fee2e2;
  color: #991b1b;
}

.grade-unknown {
  background: #f3f4f6;
  color: #6b7280;
}

.completion {
  display: flex;
  align-items: center;
  gap: 8px;
}

.completion-bar {
  width: 60px;
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
}

.completion-fill {
  height: 100%;
  transition: width 0.3s;
}

.completion-excellent {
  background: #10b981;
}

.completion-good {
  background: #3b82f6;
}

.completion-warning {
  background: #f59e0b;
}

.completion-poor {
  background: #ef4444;
}

.completion-text {
  font-size: 0.8rem;
  color: #6b7280;
  min-width: 30px;
}

.actions {
  display: flex;
  gap: 4px;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 600;
  color: #1e293b;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  padding: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  color: #374151;
}

.modal-body {
  padding: 24px;
}

.student-details {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row strong {
  color: #374151;
  font-weight: 600;
}

@media (max-width: 768px) {
  .lecturer-students-view {
    padding: 16px;
  }

  .section-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }

  .header-actions {
    width: 100%;
    justify-content: flex-start;
  }

  .students-table {
    font-size: 0.8rem;
  }

  .students-table th,
  .students-table td {
    padding: 8px 6px;
  }

  .actions {
    flex-direction: column;
    gap: 2px;
  }

  .action-btn.small {
    padding: 2px 6px;
    font-size: 0.7rem;
  }
}
</style>
