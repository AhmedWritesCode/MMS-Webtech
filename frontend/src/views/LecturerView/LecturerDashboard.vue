<template>
  <div class="lecturer-dashboard-container">
    <LecturerNavbar @sidebar-toggle="handleSidebarToggle" />
    <div class="lecturer-dashboard" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
      <div class="header-content">
        <div class="welcome-section">
          <h1>Welcome back, {{ lecturerName }}!</h1>
          <p class="subtitle">Here's an overview of your teaching activities</p>
        </div>
        <div class="header-actions">
          <button class="action-btn primary" @click="createNewComponent">
            <span class="btn-icon">➕</span>
            Add Assessment
          </button>
        </div>
      </div>
    </div>

    <!-- Overview Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-content">
          <h3>{{ overview.total_courses }}</h3>
          <p>Active Courses</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <h3>{{ overview.total_students }}</h3>
          <p>Total Students</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-content">
          <h3>{{ overview.total_components }}</h3>
          <p>Assessment Components</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <h3>{{ overview.completion_rate }}%</h3>
          <p>Grading Completion</p>
        </div>
      </div>
    </div>

    <!-- Courses Section -->
    <div class="courses-section">
      <div class="section-header">
        <h2>My Courses</h2>
    
      </div>

      <div class="courses-grid">
        <div
          v-for="course in filteredCourses"
          :key="course.course_id"
          class="course-card"
          @click="viewCourse(course.course_id)"
        >
          <div class="course-header">
            <h3 class="course-code">{{ course.course_code }}</h3>
          </div>
          
          <div class="course-details">
            <h4 class="course-name">{{ course.course_name }}</h4>
            <p class="course-semester">{{ course.semester }}</p>
          </div>
          
          <div class="course-stats">
            <div class="stat-item">
              <div class="stat-value">{{ course.enrolled_students }}</div>
              <div class="stat-label">Students</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">{{ course.total_components }}</div>
              <div class="stat-label">Components</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">{{ course.graded_components }}</div>
              <div class="stat-label">Graded</div>
            </div>
          </div>
          
          
          
          <div class="course-actions">
            <button
              class="action-btn small"
              @click.stop="viewStudents(course.course_id)"
            >
              View Students
            </button>
            <button
              class="action-btn small"
              @click.stop="viewAnalytics(course.course_id)"
            >
              Analytics
            </button>
            <button
              class="action-btn small"
              @click.stop="exportCourseMarks(course.course_id)"
            >
              Export
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredCourses.length === 0" class="empty-state">
        <div class="empty-icon">📚</div>
        <h3>No courses found</h3>
        <p>You haven't been assigned to any courses yet.</p>
      </div>
    </div>

    <!-- Assessment Components Section -->
    <div class="assessment-section">
      <h2>Manage Assessment Components</h2>
      <div class="course-selector">
        <label for="course-select">Select Course:</label>
        <select id="course-select" v-model="selectedCourseId">
          <option v-for="course in courses" :key="course.course_id" :value="course.course_id">
            {{ course.course_code }} - {{ course.course_name }}
          </option>
        </select>
      </div>
      <AssessmentComponentPanel v-if="selectedCourseId" :course-id="selectedCourseId" :lecturer-id="lecturerId" />
    </div>

   
    <!-- Loading Overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner">Loading dashboard...</div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="loadDashboardData">Retry</button>
    </div>
    </div>
  </div>
</template>

<script>
import { lecturerAPI, utilityAPI } from "@/services/api";
import AssessmentComponentPanel from "@/views/LecturerView/AssessmentComponentPanel.vue";
import LecturerNavbar from "@/components/navigation/LecturerNavbar.vue";

export default {
  name: "LecturerDashboard",
  components: {
    AssessmentComponentPanel,
    LecturerNavbar
  },
  data() {
    return {
      loading: false,
      error: null,
      lecturerName: "Lecturer",
      lecturerId: null,
      courses: [],
      selectedCourseId: null,
      sidebarCollapsed: false,
      overview: {
        total_courses: 0,
        total_students: 0,
        total_components: 0,
        total_graded_components: 0,
        overall_avg_performance: 0,
        completion_rate: 0,
      },
      recentActivities: [],
      filterMenuOpen: false,
    };
  },
  computed: {
    filteredCourses() {
      return this.courses;
    },
  },
  async mounted() {
    await this.loadDashboardData();
    if (this.courses.length > 0) {
      this.selectedCourseId = this.courses[0].course_id;
    }
  },
  methods: {
    async loadDashboardData() {
      this.loading = true;
      this.error = null;

      try {
        // Get user profile
        const userResponse = await utilityAPI.getUserProfile();
        console.log('User profile response:', userResponse);
        if (userResponse.success) {
          this.lecturerName = userResponse.data.full_name;
          this.lecturerId = userResponse.data.id;
          console.log('Lecturer ID set to:', this.lecturerId);
        } else {
          throw new Error("Failed to load user profile");
        }

        // Only fetch courses if lecturerId is set
        if (this.lecturerId) {
          console.log("Fetching courses for lecturerId:", this.lecturerId);
          const coursesResponse = await lecturerAPI.getCourses(this.lecturerId);
          if (coursesResponse.success) {
            this.courses = coursesResponse.data.courses || coursesResponse.data || [];
          } else {
            this.courses = [];
          }
        } else {
          this.error = "Lecturer ID not found.";
          console.error("Lecturer ID is null or undefined.");
        }
      } catch (error) {
        console.error("Error loading dashboard:", error);
        this.error = "Failed to load dashboard data";
      } finally {
        this.loading = false;
      }
    },

    viewCourse(courseId) {
      this.$router.push(`/lecturer/course/${courseId}`);
    },

    viewStudents(courseId) {
      this.$router.push(`/lecturer/course/${courseId}/students`);
    },

    viewAnalytics(courseId) {
      this.$router.push(`/lecturer/course/${courseId}/analytics`);
    },

    async exportCourseMarks(courseId) {
      try {
        const response = await lecturerAPI.exportMarks(this.lecturerId, courseId);
        if (response.success) {
          // Create download link
          const blob = new Blob([response.data], { type: "text/csv" });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = response.filename || "course_marks.csv";
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        }
      } catch (error) {
        console.error("Error exporting marks:", error);
        alert("Failed to export marks");
      }
    },

    createNewComponent() {
      // Navigate to component creation page
      this.$router.push("/lecturer/components/new");
    },

    toggleFilterMenu() {
      this.filterMenuOpen = !this.filterMenuOpen;
    },

    getProgressClass(percentage) {
      if (percentage >= 80) return "progress-excellent";
      if (percentage >= 60) return "progress-good";
      if (percentage >= 40) return "progress-warning";
      return "progress-poor";
    },

    formatDate(dateString) {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    
    handleSidebarToggle(collapsed) {
      this.sidebarCollapsed = collapsed;
    },
  },
};
</script>

<style scoped>
.lecturer-dashboard-container {
  display: flex;
  min-height: 100vh;
  background: linear-gradient(135deg, #7C9885 0%, #B5B682 100%);
}

.lecturer-dashboard {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
  min-height: 100vh;
  margin-left: 220px; /* Match the width of the sidebar */
  width: calc(100% - 220px);
  transition: margin-left 0.2s, width 0.2s;
}

.lecturer-dashboard.sidebar-collapsed {
  margin-left: 64px;
  width: calc(100% - 64px);
}

@media (max-width: 900px) {
  .lecturer-dashboard {
    margin-left: 64px;
    width: calc(100% - 64px);
  }
}

/* Dashboard Header */
.dashboard-header {
  background: linear-gradient(135deg, #7C9885 0%, #B5B682 100%);
  border-radius: 1.5rem;
  padding: 32px;
  margin-bottom: 32px;
  color: white;
  border: 2px solid #7C9885;
  box-shadow: 0 6px 24px 0 rgba(124, 152, 133, 0.18);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.welcome-section h1 {
  margin: 0 0 8px 0;
  font-size: 2rem;
  font-weight: 700;
}

.subtitle {
  margin: 0;
  opacity: 0.9;
  font-size: 1.1rem;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.action-btn {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.action-btn.primary {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.action-btn.primary:hover {
  background: #B5B682;
  color: #23272f;
}

.btn-icon {
  margin-right: 8px;
  font-size: 1.1rem;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
  background: #fff;
  border-radius: 1.5rem;
  padding: 24px;
  box-shadow: 0 6px 24px 0 rgba(124, 152, 133, 0.18);
  border: 2px solid #7C9885;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(124, 152, 133, 0.1);
  display: flex;
  align-items: center;
  transition: transform 0.2s;
  border: 1px solid #B5B682;
}

.stat-card:hover {
  transform: translateY(-2px);
  border-color: #7C9885;
}

.stat-icon {
  font-size: 2.5rem;
  margin-right: 20px;
}

.stat-content h3 {
  margin: 0 0 4px 0;
  font-size: 2rem;
  font-weight: 700;
  color: #7C9885;
}

.stat-content p {
  margin: 0;
  color: #64748b;
  font-weight: 500;
}

/* Courses Section */
.courses-section {
  background: white;
  border-radius: 1.5rem;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 6px 24px 0 rgba(124, 152, 133, 0.18);
  border: 2px solid #7C9885;
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
  font-weight: 700;
  color: #1e293b;
}

.filter-btn {
  display: flex;
  align-items: center;
  padding: 8px 16px;
  background: #f8fafc;
  border: 1px solid #B5B682;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-btn:hover {
  background: #B5B682;
  color: #23272f;
}

.courses-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.course-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(124, 152, 133, 0.1);
  padding: 1.2rem;
  width: 280px;
  display: flex;
  flex-direction: column;
  border: 1px solid #B5B682;
  transition: all 0.2s;
  cursor: pointer;
}

.course-card:hover {
  border-color: #7C9885;
  box-shadow: 0 4px 12px rgba(124, 152, 133, 0.15);
  transform: translateY(-2px);
}

.course-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.course-code {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #7C9885;
}

.course-details {
  margin-bottom: 12px;
}

.course-name {
  margin: 0 0 4px 0;
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
}

.course-semester {
  margin: 0;
  font-size: 0.85rem;
  color: #6b7280;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  background: #B5B682;
  color: #23272f;
}

.course-stats {
  display: flex;
  justify-content: space-between;
  margin-bottom: 16px;
  padding: 8px 0;
  border-top: 1px solid rgba(181, 182, 130, 0.3);
  border-bottom: 1px solid rgba(181, 182, 130, 0.3);
}

.stat-item {
  text-align: center;
  flex: 1;
}

.stat-label {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 2px;
}

.stat-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: #7C9885;
}

.course-progress {
  margin-bottom: 16px;
}

.progress-label {
  margin-bottom: 6px;
  font-size: 0.85rem;
  color: #374151;
}

.progress-bar {
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  transition: width 0.3s;
}

.progress-excellent {
  background: #7C9885;
}

.progress-good {
  background: #B5B682;
}

.progress-warning {
  background: #B5B682;
  opacity: 0.8;
}

.progress-poor {
  background: #7C9885;
  opacity: 0.6;
}

.course-actions {
  display: flex;
  gap: 0.4rem;
  justify-content: space-between;
}

.action-btn.small {
  flex: 1;
  font-size: 0.8rem;
  padding: 0.4rem 0.5rem;
  border-radius: 6px;
  background: #f5f5f5;
  border: 1px solid #B5B682;
  color: #333;
  transition: all 0.2s;
  text-align: center;
}

.action-btn.small:hover {
  background: #B5B682;
  color: #23272f;
}

/* Assessment Components Section */
.assessment-section {
  background: white;
  border-radius: 1.5rem;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 6px 24px 0 rgba(124, 152, 133, 0.18);
  border: 2px solid #7C9885;
}

.course-selector {
  margin-bottom: 20px;
}

.course-selector label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #7C9885;
}

.course-selector select {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #B5B682;
  background-color: #fff;
  font-size: 1rem;
}

.course-selector select:focus {
  outline: none;
  border-color: #7C9885;
  box-shadow: 0 0 0 2px rgba(124, 152, 133, 0.2);
}

/* Recent Activities */
.recent-activities {
  background: white;
  border-radius: 1.5rem;
  padding: 32px;
  box-shadow: 0 6px 24px 0 rgba(124, 152, 133, 0.18);
  border: 2px solid #7C9885;
}

.view-all-link {
  color: #7C9885;
  text-decoration: none;
  font-weight: 600;
}

.view-all-link:hover {
  text-decoration: underline;
  color: #B5B682;
}

.activities-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.activity-item {
  display: flex;
  align-items: flex-start;
  padding: 16px;
  border: 1px solid #B5B682;
  border-radius: 8px;
  transition: all 0.2s;
  margin-bottom: 8px;
}

.activity-item:hover {
  background: #f9fafb;
  border-color: #7C9885;
}

.activity-icon {
  font-size: 1.2rem;
  margin-right: 16px;
  margin-top: 2px;
}

.activity-content {
  flex: 1;
}

.activity-description {
  margin: 0 0 8px 0;
  color: #374151;
  font-weight: 500;
}

.activity-course {
  color: #7C9885;
  font-weight: 600;
}

.activity-meta {
  display: flex;
  gap: 16px;
  font-size: 0.8rem;
  color: #6b7280;
}

/* Empty States */
.empty-state,
.empty-activities {
  text-align: center;
  padding: 48px 24px;
  color: #6b7280;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 16px;
}

.empty-state h3,
.empty-activities p {
  margin: 0 0 8px 0;
  font-size: 1.1rem;
}

/* Loading and Error States */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(181, 182, 130, 0.92);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  background: #B5B682;
  color: #23272f;
  padding: 20px 40px;
  border-radius: 16px;
  font-weight: 600;
  font-size: 1.2rem;
  letter-spacing: 1px;
  box-shadow: 0 2px 16px rgba(124, 152, 133, 0.18);
  border: 1.5px solid #7C9885;
}

.error-message {
  position: fixed;
  top: 24px;
  right: 24px;
  background: #7C9885;
  border: 1.5px solid #B5B682;
  color: #e74c3c;
  padding: 18px;
  border-radius: 14px;
  max-width: 340px;
  z-index: 1000;
  box-shadow: 0 2px 12px rgba(124, 152, 133, 0.15);
}

.error-message p {
  color: #fff;
  margin: 0 0 16px 0;
  font-weight: 600;
}

.error-message button {
  background: #B5B682;
  color: #23272f;
  border: none;
  padding: 7px 16px;
  border-radius: 7px;
  margin-top: 12px;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.2s;
}

.error-message button:hover {
  background: #7C9885;
  color: #fff;
}

/* Responsive Design */
@media (max-width: 768px) {
  .lecturer-dashboard {
    padding: 16px;
  }

  .header-content {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }

  .header-actions {
    width: 100%;
    justify-content: center;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .courses-grid {
    grid-template-columns: 1fr;
  }

  .course-stats {
    grid-template-columns: repeat(2, 1fr);
  }

  .course-actions {
    flex-direction: column;
  }
}
</style>
