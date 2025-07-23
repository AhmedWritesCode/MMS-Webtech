<template>
  <div class="dashboard-outer">
    <div class="dashboard-panel">
      <!-- Dashboard Header -->
      <DashboardHeader
        :student-name="studentInfo.name"
        :matric-number="studentInfo.matricNumber"
        :current-semester="studentInfo.currentSemester"
      />

      <!-- Stats Grid -->
      <StatsGrid
        :overall-g-p-a="academicStats.overallGPA"
        :gpa-change="academicStats.gpaChange"
        :enrolled-courses-count="academicStats.enrolledCoursesCount"
        :class-rank="academicStats.classRank"
        :total-students="academicStats.totalStudents"
        :percentile="academicStats.percentile"
      />

      <!-- Course Performance Section -->
      <CoursePerformanceSection
        :courses="courses"
        :loading="loadingCourses"
        @view-all-courses="handleViewAllCourses"
        @course-details="handleCourseDetails"
      />

      <!-- Quick Actions Section -->
      <QuickActionsSection @action-clicked="handleActionClick" />

      <!-- Recent Activity Section -->
      <RecentActivitySection
        v-if="recentActivities.length > 0"
        :activities="recentActivities"
        :loading="loadingActivities"
        @activity-action="handleActivityAction"
      />

      <!-- Loading Overlay -->
      <div v-if="loading" class="loading-overlay">
        <div class="loading-spinner">Loading...</div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="error-message">
        <p>{{ error }}</p>
        <button @click="retryLoadData">Retry</button>
      </div>
    </div>
  </div>
</template>

<script>
// Import dashboard components
import DashboardHeader from "@/components/StudentComponents/DashboardComponents/DashboardHeader.vue";
import StatsGrid from "@/components/StudentComponents/DashboardComponents/StatsGrid.vue";
import CoursePerformanceSection from "@/components/StudentComponents/DashboardComponents/CoursePerformanceSection.vue";
import QuickActionsSection from "@/components/StudentComponents/DashboardComponents/QuickActionsSection.vue";
import RecentActivitySection from "@/components/StudentComponents/DashboardComponents/RecentActivitySection.vue";

// Import API services
import { studentPerformanceAPI, utilityAPI } from "@/services/api";
import authService from "@/services/auth";

export default {
  name: "StudentDashboard",
  components: {
    DashboardHeader,
    StatsGrid,
    CoursePerformanceSection,
    QuickActionsSection,
    RecentActivitySection,
  },
  data() {
    return {
      loading: false,
      loadingCourses: false,
      loadingActivities: false,
      error: null,

      studentInfo: {
        id: null,
        name: "Loading...",
        matricNumber: "",
        currentSemester: "",
        email: "",
        department: "",
        program: "",
      },

      academicStats: {
        overallGPA: 0.0,
        gpaChange: 0.0,
        enrolledCoursesCount: 0,
        classRank: 0,
        totalStudents: 0,
        percentile: 0,
        creditHours: 0,
        completedCourses: 0,
      },

      courses: [],
      recentActivities: [],
    };
  },

  async mounted() {
    await this.loadDashboardData();
  },

  methods: {
    async loadDashboardData() {
      this.loading = true;
      this.error = null;

      try {
        // Load student info first, then load dependent data
        await this.loadStudentInfo();

        // Now load the rest of the data that depends on studentInfo.id
        await Promise.all([
          this.loadAcademicStats(),
          this.loadRecentActivities(),
        ]);
      } catch (error) {
        console.error("Error loading dashboard data:", error);
        this.error = "Failed to load dashboard data. Please try again.";
      } finally {
        this.loading = false;
      }
    },

    async loadStudentInfo() {
      try {
        console.log("Starting loadStudentInfo...");
        console.log("Current token:", authService.getToken());

        const response = await utilityAPI.getUserProfile();
        console.log("User profile response:", response);

        if (response.success) {
          const userData = response.data;
          console.log("User data:", userData);

          const isStudent = userData.role === "student" || userData.user_type === "student";
          if (isStudent) {
            console.log("Fetching student breakdown for user ID:", userData.id);
            const studentResponse = await studentPerformanceAPI.getStudentFullBreakdown(userData.id);
            console.log("Student breakdown response:", studentResponse);

            if (studentResponse.success) {
              const studentData = studentResponse.data.student_info;
              console.log("Student data from breakdown:", studentData);
              this.studentInfo = {
                id: userData.id,
                name: studentData.name || userData.full_name || "Student Name",
                matricNumber: studentData.matric_number || userData.username || "Loading...",
                currentSemester: studentData.current_semester || "2024/2025-1",
                email: studentData.email || userData.email || "",
                department: studentData.department || "Computer Science",
                program: studentData.program || "Bachelor of Computer Science",
              };
              console.log("Updated studentInfo:", this.studentInfo);
              await this.loadCourses();
            } else {
              console.log("Student breakdown failed:", studentResponse);
              // Fallback to user profile data
              this.studentInfo = {
                id: userData.id,
                name: userData.full_name || "Student Name",
                matricNumber: userData.username || "Loading...",
                currentSemester: "2024/2025-1",
                email: userData.email || "",
                department: "Computer Science",
                program: "Bachelor of Computer Science",
              };
              await this.loadCourses();
            }
          }
        } else {
          console.log("User profile failed:", response);
        }
      } catch (error) {
        console.error("Error loading student info:", error);
        // Keep default loading state if API fails
      }
    },

    async loadAcademicStats() {
      try {
        if (!this.studentInfo.id) return;

        const response = await studentPerformanceAPI.getStudentFullBreakdown(
          this.studentInfo.id
        );

        if (response.success) {
          const data = response.data;

          this.academicStats = {
            overallGPA: data.academic_summary?.overall_gpa || 0.0,
            gpaChange:
              data.academic_summary?.gpa_change !== undefined
                ? String(data.academic_summary.gpa_change)
                : "+0.00",
            enrolledCoursesCount:
              data.academic_summary?.enrolled_courses_count || 0,
            classRank: data.academic_summary?.class_rank || 0,
            totalStudents: data.academic_summary?.total_students || 0,
            percentile: data.academic_summary?.percentile || 0,
            creditHours: data.academic_summary?.credit_hours || 0,
            completedCourses: data.academic_summary?.completed_courses || 0,
          };
        }
      } catch (error) {
        console.error("Error loading academic stats:", error);
        // Keep default values if API fails
      }
    },

    async loadCourses() {
      this.loadingCourses = true;
      try {
        console.log('In loadCourses, studentInfo.id:', this.studentInfo.id);
        if (!this.studentInfo.id) {
          console.warn('No student ID, skipping course load');
          return;
        }
        const response = await studentPerformanceAPI.getStudentFullBreakdown(this.studentInfo.id);
        console.log('Course breakdown response:', response);
        if (response.success) {
          const coursesData = response.data.courses || [];

          // Transform API data to match component expectations
          this.courses = coursesData.map((course) => ({
            id: course.course_id,
            code: course.course_code,
            name: course.course_name,
            credits: course.credit_hours,
            currentMarks: course.total_marks || 0,
            totalMarks: course.max_marks || 100,
            progressPercentage: course.percentage || 0,
            currentGrade: course.grade_letter || "N/A",
            status: course.status || "ongoing",
            lecturer: course.lecturer_name || "TBA",
            creditHours: course.credit_hours || 3,
            components: course.components || [],
            // Additional properties for CourseCard
            classAverage: 75, // Placeholder - could be calculated from backend
            completedAssessments: course.graded_components || 0,
            pendingAssessments:
              (course.total_components || 0) - (course.graded_components || 0),
            // Map backend properties to frontend expectations
            currentMark: course.total_marks || 0,
            maxMark: course.max_marks || 100,
            percentage: course.percentage || 0,
            grade: course.grade_letter || "N/A",
          }));
        }
      } catch (error) {
        console.error("Error loading courses:", error);
        this.courses = []; // Reset to empty array on error
      } finally {
        this.loadingCourses = false;
      }
    },

    async loadRecentActivities() {
      this.loadingActivities = true;

      try {
        // Since there's no specific endpoint for activities in the current API,
        // we'll fetch from multiple sources and combine them
        const activities = [];

        if (this.studentInfo.id) {
          // Get performance trends which might include recent grade updates
          const trendsResponse =
            await studentPerformanceAPI.getPerformanceTrends(
              this.studentInfo.id
            );

          if (trendsResponse.data.success) {
            const trends = trendsResponse.data.data.recent_updates || [];

            trends.forEach((update) => {
              activities.push({
                id: update.id || Date.now() + Math.random(),
                type: "grade_update",
                title: "Grade Updated",
                description: `${update.course_name}: ${update.component_name} - ${update.marks}/${update.max_marks}`,
                timestamp: new Date(update.updated_at || Date.now()),
                actionRequired: false,
                courseId: update.course_id,
              });
            });
          }
        }

        // Sort activities by timestamp (most recent first)
        this.recentActivities = activities.sort(
          (a, b) => new Date(b.timestamp) - new Date(a.timestamp)
        );
      } catch (error) {
        console.error("Error loading recent activities:", error);
        // Fallback to empty array if API fails
        this.recentActivities = [];
      } finally {
        this.loadingActivities = false;
      }
    },

    // Event handlers
    handleViewAllCourses() {
      this.$router.push("/courses");
    },

    handleCourseDetails(courseId) {
      this.$router.push(`/course/${courseId}/details`);
    },

    handleActionClick(action) {
      if (action.route) {
        this.$router.push(action.route);
      }

      // Handle specific actions
      switch (action.id) {
        case "what-if":
          this.$router.push("/student/what-if-simulator");
          break;
        case "compare":
          this.$router.push("/student/performance-comparison");
          break;
        case "remark":
          this.$router.push("/student/remark-request");
          break;
        case "trends":
          this.$router.push("/student/performance-trends");
          break;
      }
    },

    handleActivityAction(activity) {
      // Handle activity-specific actions
      switch (activity.type) {
        case "grade_update":
          this.$router.push(`/course/${activity.courseId || 1}/marks`);
          break;
        case "remark_update":
          this.$router.push("/remark-requests");
          break;
        default:
          console.log("Activity action:", activity);
      }
    },

    retryLoadData() {
      this.loadDashboardData();
    },

    showError(message) {
      this.error = message;
      // Auto-hide error after 5 seconds
      setTimeout(() => {
        this.error = null;
      }, 5000);
    },
  },
};
</script>

<style scoped>
.dashboard-outer {
  width: 100vw;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  background: linear-gradient(135deg, #B5B682 0%, #7C9885 100%);
  padding: 3vh 0;
}

.dashboard-panel {
  background: rgba(255,255,255,0.95);
  border-radius: 2.5rem;
  box-shadow: 0 8px 40px 0 rgba(124, 152, 133, 0.18), 0 1.5px 8px 0 rgba(0,0,0,0.04);
  max-width: 1100px;
  width: 100%;
  margin: 3rem 0 2rem 0;
  padding: 3.5rem 3rem 2.5rem 3rem;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  min-height: 80vh;
  border: 1.5px solid #B5B682;
  transition: box-shadow 0.2s, background 0.2s;
}

.dashboard-panel:hover {
  box-shadow: 0 16px 48px 0 rgba(124, 152, 133, 0.22), 0 2px 12px 0 rgba(0,0,0,0.06);
  background: rgba(255,255,255,0.98);
}

@media (max-width: 1200px) {
  .dashboard-panel {
    max-width: 98vw;
    padding: 2rem 0.5rem 1.5rem 0.5rem;
  }
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
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

/* Global responsive adjustments */
@media (max-width: 768px) {
  .dashboard-panel {
    padding: 10px;
    border-radius: 1.2rem;
  }

  .error-message {
    top: 10px;
    right: 10px;
    left: 10px;
    max-width: none;
    padding: 12px;
  }
}
</style>
