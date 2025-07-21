<template>
  <div class="course-detail-view">
    <!-- Loading Overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner">Loading course data...</div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="loadCourseData">Retry</button>
    </div>

    <!-- Course Content -->
    <div v-if="!loading && !error">
      <!-- Course Header -->
      <CourseHeader :course="course" />

      <!-- Progress Overview -->
      <ProgressOverview :course="course" />

      <!-- Assessment Components -->
      <AssessmentsSection
        :assessments="assessments"
        :active-filter="activeFilter"
        @update-filter="updateFilter"
        @request-remark="requestRemark"
        @view-assessment-details="viewAssessmentDetails"
      />

      <!-- Grade Breakdown Chart -->
      <GradeBreakdown :weight-categories="weightCategories" />

      <!-- Class Performance Comparison -->
      <ClassPerformanceComparison
        :top-assessments="topAssessments"
        :above-average-count="aboveAverageCount"
        :total-assessments="course.totalAssessments"
        :percentile-rank="course.percentileRank"
        :improvement-trend="improvementTrend"
        :improvement-class="improvementClass"
      />
    </div>
  </div>
</template>

<script>
// Import components
import CourseHeader from "@/components/StudentComponents/CourseDetailViewComponents/CourseHeader.vue";
import ProgressOverview from "@/components/StudentComponents/CourseDetailViewComponents/ProgressOverview.vue";
import AssessmentsSection from "@/components/StudentComponents/CourseDetailViewComponents/AssessmentsSection.vue";
import GradeBreakdown from "@/components/StudentComponents/CourseDetailViewComponents/GradeBreakdown.vue";
import ClassPerformanceComparison from "@/components/StudentComponents/CourseDetailViewComponents/ClassPerformanceComparison.vue";

// Import API services
import { studentPerformanceAPI, utilityAPI } from "@/services/api";

export default {
  name: "CourseDetailView",
  components: {
    CourseHeader,
    ProgressOverview,
    AssessmentsSection,
    GradeBreakdown,
    ClassPerformanceComparison,
  },
  data() {
    return {
      activeFilter: "all",
      loading: false,
      error: null,
      course: {
        id: null,
        code: "",
        name: "",
        credits: 0,
        semester: "",
        currentGrade: "N/A",
        currentMarks: 0,
        totalMarks: 100,
        classRank: 0,
        totalStudents: 0,
        classAverage: 0,
        progressPercentage: 0,
        completedAssessments: 0,
        totalAssessments: 0,
        percentileRank: 0,
      },
      assessments: [],
    };
  },
  computed: {
    weightCategories() {
      const categories = {
        Quizzes: {
          weight: 0,
          obtainedMarks: 0,
          totalMarks: 0,
          status: "completed",
        },
        Assignments: {
          weight: 0,
          obtainedMarks: 0,
          totalMarks: 0,
          status: "partial",
        },
        Labs: {
          weight: 0,
          obtainedMarks: 0,
          totalMarks: 0,
          status: "completed",
        },
        Tests: {
          weight: 0,
          obtainedMarks: 0,
          totalMarks: 0,
          status: "completed",
        },
        "Final Exam": {
          weight: 0,
          obtainedMarks: 0,
          totalMarks: 0,
          status: "pending",
        },
      };

      this.assessments.forEach((assessment) => {
        let category;
        switch (assessment.type) {
          case "quiz":
            category = "Quizzes";
            break;
          case "assignment":
            category = "Assignments";
            break;
          case "lab":
            category = "Labs";
            break;
          case "test":
            category = "Tests";
            break;
          case "final_exam":
            category = "Final Exam";
            break;
          default:
            return;
        }

        categories[category].weight += assessment.weight;
        categories[category].totalMarks += assessment.maxMarks;
        if (assessment.marksObtained !== null) {
          categories[category].obtainedMarks += assessment.marksObtained;
        }
      });

      return Object.entries(categories).map(([name, data]) => ({
        name,
        ...data,
      }));
    },

    topAssessments() {
      return this.assessments
        .filter((a) => a.marksObtained !== null)
        .map((a) => ({
          id: a.id,
          name: a.name,
          yourPercentage: Math.round((a.marksObtained / a.maxMarks) * 100),
          classPercentage: Math.round(Math.random() * 20 + 65), // Mock class average
        }))
        .slice(0, 4);
    },

    aboveAverageCount() {
      return this.topAssessments.filter(
        (a) => a.yourPercentage > a.classPercentage
      ).length;
    },

    improvementTrend() {
      const completed = this.assessments.filter(
        (a) => a.marksObtained !== null
      );
      if (completed.length < 2) return "N/A";

      const latest = completed[completed.length - 1];
      const previous = completed[completed.length - 2];

      const latestPercentage = (latest.marksObtained / latest.maxMarks) * 100;
      const previousPercentage =
        (previous.marksObtained / previous.maxMarks) * 100;

      const difference = latestPercentage - previousPercentage;
      return difference > 0
        ? `+${difference.toFixed(1)}%`
        : `${difference.toFixed(1)}%`;
    },

    improvementClass() {
      const trend = this.improvementTrend;
      if (trend === "N/A") return "";
      return trend.startsWith("+")
        ? "improvement-positive"
        : "improvement-negative";
    },
  },
  watch: {
    // Watch for route parameter changes to reload data when navigating between courses
    "$route.params.courseId": {
      handler(newCourseId, oldCourseId) {
        if (newCourseId !== oldCourseId) {
          console.log("Course ID changed from", oldCourseId, "to", newCourseId);
          this.loadCourseData();
        }
      },
      immediate: true,
    },
  },
  methods: {
    updateFilter(filterKey) {
      this.activeFilter = filterKey;
    },

    requestRemark(assessmentId) {
      // Navigate to the RemarkRequests view, optionally with assessmentId as a query
      this.$router.push({
        path: '/student/remark-requests',
        query: assessmentId ? { assessmentId } : undefined
      });
    },

    viewAssessmentDetails(assessmentId) {
      // Could open a modal or navigate to detailed view
      console.log("View assessment details:", assessmentId);
      // TODO: Implement assessment details view
    },

    async loadCourseData() {
      this.loading = true;
      this.error = null;

      try {
        const courseId = this.$route.params.courseId;
        console.log("Loading course data for courseId:", courseId);

        if (!courseId) {
          this.error = "No course ID provided";
          return;
        }

        // Get user profile to get student ID
        const userResponse = await utilityAPI.getUserProfile();
        console.log("User profile response:", userResponse);

        if (!userResponse.success) {
          this.error = "Failed to get user profile";
          return;
        }

        const studentId = userResponse.data.id;
        console.log("Student ID:", studentId);

        // Load course-specific data
        const response = await studentPerformanceAPI.getStudentCourseMarks(
          studentId,
          courseId
        );

        console.log("Course marks response:", response);

        if (response.success) {
          const data = response.data;
          console.log("Course data:", data);

          // Update course information
          this.course = {
            id: courseId,
            code: data.course.course_code,
            name: data.course.course_name,
            credits: 3, // Default value - could be fetched from course details
            semester: "2024/2025-1", // Default value - could be fetched from course details
            currentGrade: data.summary.grade_letter || "N/A",
            currentMarks: data.summary.overall_percentage || 0,
            totalMarks: 100,
            classRank: 15, // Placeholder - could be calculated from backend
            totalStudents: 45, // Placeholder - could be calculated from backend
            classAverage: 75, // Placeholder - could be calculated from backend
            progressPercentage: data.summary.overall_percentage || 0,
            completedAssessments: data.components.filter(
              (c) => c.marks_obtained !== null
            ).length,
            totalAssessments: data.components.length,
            percentileRank: 67, // Placeholder - could be calculated from backend
          };

          console.log("Updated course object:", this.course);

          // Transform assessment components to match component expectations
          this.assessments = data.components.map((component) => ({
            id: component.component_id,
            name: component.component_name,
            type: component.component_type,
            weight: component.weight_percentage,
            maxMarks: component.max_marks,
            marksObtained: component.marks_obtained,
            dueDate: component.graded_at
              ? new Date(component.graded_at).toISOString().split("T")[0]
              : null,
            gradedAt: component.graded_at,
            description: `${component.component_type} assessment`,
            remarks: component.remarks,
            status: component.marks_obtained !== null ? "completed" : "pending",
          }));

          console.log("Updated assessments array:", this.assessments);
        } else {
          this.error = "Failed to load course data";
          console.error("API response not successful:", response);
        }
      } catch (error) {
        console.error("Error loading course data:", error);
        this.error = "Failed to load course data";
      } finally {
        this.loading = false;
      }
    },

    showError(message) {
      // TODO: Implement proper error handling/notification
      alert(message);
    },
  },
  async mounted() {
    // Load course data when component mounts
    await this.loadCourseData();
  },
};
</script>

<style scoped>
.course-detail-view {
  padding: 32px;
  background: linear-gradient(135deg, #0f0f0f 0%, #1a2e1a 100%);
  min-height: 100vh;
  position: relative;
  color: #d1ffd6;
  font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
  box-shadow: 0 0 0 8px #0f0f0f inset;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(10, 30, 10, 0.96);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  background: #101d10;
  padding: 36px 48px;
  border-radius: 18px;
  box-shadow: 0 6px 32px rgba(0, 255, 128, 0.15);
  font-size: 1.2rem;
  color: #00ff88;
  font-weight: 700;
  letter-spacing: 1px;
  border: 2px solid #00ff88;
  text-shadow: 0 0 8px #00ff8844;
}

.error-message {
  background: #101d10;
  border: 2px solid #00ff88;
  border-radius: 18px;
  padding: 32px;
  margin: 32px auto;
  text-align: center;
  box-shadow: 0 4px 24px rgba(0, 255, 128, 0.08);
  max-width: 480px;
}

.error-message p {
  color: #ff4f4f;
  margin: 0 0 20px 0;
  font-weight: 700;
  font-size: 1.1rem;
  letter-spacing: 0.5px;
}

.error-message button {
  background: linear-gradient(90deg, #00ff88 0%, #007f44 100%);
  color: #101d10;
  border: none;
  padding: 10px 28px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 700;
  font-size: 1rem;
  box-shadow: 0 2px 8px #00ff8855;
  transition: background 0.2s, color 0.2s;
}

.error-message button:hover {
  background: linear-gradient(90deg, #007f44 0%, #00ff88 100%);
  color: #d1ffd6;
}

/* Custom scrollbar for dark theme */
.course-detail-view ::-webkit-scrollbar {
  width: 10px;
  background: #1a2e1a;
}
.course-detail-view ::-webkit-scrollbar-thumb {
  background: #00ff88;
  border-radius: 8px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .course-detail-view {
    padding: 12px;
  }
  .error-message {
    padding: 18px;
    margin: 14px auto;
  }
  .loading-spinner {
    padding: 18px 12px;
    font-size: 1rem;
  }
}
</style>
