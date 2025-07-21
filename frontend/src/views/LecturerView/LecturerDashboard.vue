<template>
  <div class="lecturer-dashboard">
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
        <div class="section-actions">
          <button class="filter-btn" @click="toggleFilterMenu">
            <span class="btn-icon">🔍</span>
            Filter
          </button>
        </div>
      </div>

      <div class="courses-grid">
        <div
          v-for="course in filteredCourses"
          :key="course.course_id"
          class="course-card"
          @click="viewCourse(course.course_id)"
        >
          <div class="course-header">
            <div class="course-info">
              <h3 class="course-code">{{ course.course_code }}</h3>
              <h4 class="course-name">{{ course.course_name }}</h4>
              <p class="course-semester">{{ course.semester }}</p>
            </div>
            <div class="course-status">
              <span class="status-badge active">Active</span>
            </div>
          </div>

          <div class="course-stats">
            <div class="stat-item">
              <span class="stat-label">Students</span>
              <span class="stat-value">{{ course.enrolled_students }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Components</span>
              <span class="stat-value">{{ course.total_components }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Graded</span>
              <span class="stat-value">{{ course.graded_components }}</span>
            </div>
          </div>

          <div class="course-progress">
            <div class="progress-header">
              <span>Grading Progress</span>
              <span>{{ course.completion_rate }}%</span>
            </div>
            <div class="progress-bar">
              <div
                class="progress-fill"
                :style="{ width: course.completion_rate + '%' }"
                :class="getProgressClass(course.completion_rate)"
              ></div>
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
      <label for="course-select">Select Course:</label>
      <select id="course-select" v-model="selectedCourseId">
        <option v-for="course in courses" :key="course.course_id" :value="course.course_id">
          {{ course.course_code }} - {{ course.course_name }}
        </option>
      </select>
      <AssessmentComponentPanel v-if="selectedCourseId" :course-id="selectedCourseId" :lecturer-id="lecturerId" />
    </div>

    <!-- Recent Activities -->
    <div class="recent-activities">
      <div class="section-header">
        <h2>Recent Activities</h2>
        <router-link to="/lecturer/activities" class="view-all-link">
          View All
        </router-link>
      </div>

      <div class="activities-list">
        <div
          v-for="activity in recentActivities"
          :key="activity.id"
          class="activity-item"
        >
          <div class="activity-icon">📝</div>
          <div class="activity-content">
            <p class="activity-description">{{ activity.description }}</p>
            <div class="activity-meta">
              <span class="activity-course">{{ activity.course_code }}</span>
              <span class="activity-time">{{
                formatDate(activity.activity_date)
              }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty Activities -->
      <div v-if="recentActivities.length === 0" class="empty-activities">
        <p>No recent activities</p>
      </div>
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
</template>

<script>
import { lecturerAPI, utilityAPI } from "@/services/api";
import AssessmentComponentPanel from "@/views/LecturerView/AssessmentComponentPanel.vue";

export default {
  name: "LecturerDashboard",
  components: { AssessmentComponentPanel },
  data() {
    return {
      loading: false,
      error: null,
      lecturerName: "Lecturer",
      lecturerId: null,
      courses: [],
      selectedCourseId: null,
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
  },
};
</script>

<style scoped>
.lecturer-dashboard {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
}

/* Dashboard Header */
.dashboard-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 32px;
  color: white;
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
  background: rgba(255, 255, 255, 0.3);
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
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-icon {
  font-size: 2.5rem;
  margin-right: 20px;
}

.stat-content h3 {
  margin: 0 0 4px 0;
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
}

.stat-content p {
  margin: 0;
  color: #64748b;
  font-weight: 500;
}

/* Courses Section */
.courses-section {
  background: white;
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 32px;
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
  font-weight: 700;
  color: #1e293b;
}

.filter-btn {
  display: flex;
  align-items: center;
  padding: 8px 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-btn:hover {
  background: #e2e8f0;
}

.courses-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.course-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  padding: 1.5rem;
  width: 320px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.course-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}

.course-code {
  margin: 0 0 4px 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.course-name {
  margin: 0 0 4px 0;
  font-size: 1rem;
  color: #374151;
}

.course-semester {
  margin: 0;
  font-size: 0.9rem;
  color: #6b7280;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.active {
  background: #dcfce7;
  color: #166534;
}

.course-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}

.stat-item {
  text-align: center;
}

.stat-label {
  display: block;
  font-size: 0.8rem;
  color: #6b7280;
  margin-bottom: 4px;
}

.stat-value {
  display: block;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

.course-progress {
  margin-bottom: 20px;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 0.9rem;
  color: #374151;
}

.progress-bar {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  transition: width 0.3s;
}

.progress-excellent {
  background: #10b981;
}

.progress-good {
  background: #3b82f6;
}

.progress-warning {
  background: #f59e0b;
}

.progress-poor {
  background: #ef4444;
}

.course-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
  justify-content: flex-end;
}

.action-btn.small {
  font-size: 0.95rem;
  padding: 0.4rem 0.9rem;
  border-radius: 5px;
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  color: #333;
  transition: background 0.2s;
}
.action-btn.small:hover {
  background: #e0e7ff;
  color: #1d4ed8;
}

/* Recent Activities */
.recent-activities {
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.view-all-link {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
}

.view-all-link:hover {
  text-decoration: underline;
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
  border: 1px solid #f3f4f6;
  border-radius: 8px;
  transition: background 0.2s;
}

.activity-item:hover {
  background: #f9fafb;
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
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  font-size: 1.1rem;
  color: #3b82f6;
  font-weight: 600;
}

.error-message {
  background: white;
  border: 1px solid #ef4444;
  border-radius: 12px;
  padding: 24px;
  margin: 20px;
  text-align: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.error-message p {
  color: #ef4444;
  margin: 0 0 16px 0;
  font-weight: 600;
}

.error-message button {
  background: #ef4444;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.error-message button:hover {
  background: #dc2626;
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
