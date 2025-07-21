<template>
  <div class="performance-comparison">
    <!-- Header -->
    <ComparisonHeader />

    <!-- Course Selection -->
    <CourseSelector
      :courses="enrolledCourses"
      :selected-course="selectedCourse"
      @course-selected="selectCourse"
    />

    <div v-if="selectedCourse" class="comparison-content">
      <!-- Performance Overview Cards -->
      <PerformanceOverview
        :course="selectedCourse"
        :rank-data="rankData"
        :class-averages="classAverages"
      />

      <!-- Comparison Controls -->
      <ComparisonControls
        :selected-comparison-type="selectedComparisonType"
        :selected-view-type="selectedViewType"
        @update-comparison-type="updateComparisonType"
        @update-view-type="updateViewType"
      />

      <!-- Assessment Comparison (Bar Chart View) -->
      <AssessmentComparison
        v-if="selectedViewType === 'assessments'"
        :assessments="transformedAssessments"
      />

      <!-- Grade Distribution (Chart View) -->
      <GradeDistribution
        v-if="selectedViewType === 'distribution'"
        :grade-distribution="gradeDistribution"
        :selected-course="selectedCourse"
      />

      <!-- Percentile Ranking (Table View) -->
      <PercentileRanking
        v-if="selectedViewType === 'percentiles'"
        :selected-course="selectedCourse"
        :rank-data="rankData"
      />

      <!-- Class Performance Summary -->
      <ClassSummary
        :selected-course="selectedCourse"
        :class-averages="classAverages"
        :rank-data="rankData"
      />

      <!-- Anonymous Comments Section -->
      <AnonymousInsights
        :selected-course="selectedCourse"
        :comparison-data="comparisonData"
      />
    </div>

    <!-- No Course Selected State -->
    <NoCourseSelected v-else />
  </div>
</template>

<script>
// Import components
import ComparisonHeader from "@/components/StudentComponents/PerformanceComparisonComponents/ComparisonHeader.vue";
import CourseSelector from "@/components/StudentComponents/PerformanceComparisonComponents/CourseSelector.vue";
import PerformanceOverview from "@/components/StudentComponents/PerformanceComparisonComponents/PerformanceOverview.vue";
import ComparisonControls from "@/components/StudentComponents/PerformanceComparisonComponents/ComparisonControls.vue";
import AssessmentComparison from "@/components/StudentComponents/PerformanceComparisonComponents/AssessmentComparison.vue";
import GradeDistribution from "@/components/StudentComponents/PerformanceComparisonComponents/GradeDistribution.vue";
import PercentileRanking from "@/components/StudentComponents/PerformanceComparisonComponents/PercentileRanking.vue";
import ClassSummary from "@/components/StudentComponents/PerformanceComparisonComponents/ClassSummary.vue";
import AnonymousInsights from "@/components/StudentComponents/PerformanceComparisonComponents/AnonymousInsights.vue";
import NoCourseSelected from "@/components/StudentComponents/PerformanceComparisonComponents/NoCourseSelected.vue";

// Import API services
import { studentPerformanceAPI, utilityAPI } from "@/services/api";

export default {
  name: "PerformanceComparison",
  components: {
    ComparisonHeader,
    CourseSelector,
    PerformanceOverview,
    ComparisonControls,
    AssessmentComparison,
    GradeDistribution,
    PercentileRanking,
    ClassSummary,
    AnonymousInsights,
    NoCourseSelected,
  },
  data() {
    return {
      selectedCourse: null,
      selectedComparisonType: "individual",
      selectedViewType: "assessments",
      enrolledCourses: [],
      loading: false,
      error: null,
      comparisonData: null,
      rankData: null,
      classAverages: null,
    };
  },
  computed: {
    transformedAssessments() {
      if (!this.comparisonData || !this.comparisonData.comparisons) {
        return [];
      }

      return this.comparisonData.comparisons.map((comparison) => ({
        id: comparison.component_name,
        name: comparison.component_name,
        type: comparison.component_type,
        weight: 0, // This would need to come from assessment_components table
        maxScore: comparison.max_marks,
        yourScore: comparison.my_marks || 0,
        classAverage: comparison.class_average || 0,
        highestScore: comparison.max_marks, // Using max_marks as highest for now
        yourPercentile: comparison.percentile || 0,
        submissions: comparison.submissions || 0,
      }));
    },

    gradeDistribution() {
      if (!this.selectedCourse || !this.comparisonData) return [];

      // Generate grade distribution from real comparison data
      const distribution = [
        { grade: "A+", count: 0 },
        { grade: "A", count: 0 },
        { grade: "A-", count: 0 },
        { grade: "B+", count: 0 },
        { grade: "B", count: 0 },
        { grade: "B-", count: 0 },
        { grade: "C+", count: 0 },
        { grade: "C", count: 0 },
        { grade: "C-", count: 0 },
      ];

      // Calculate distribution based on comparison data
      if (this.comparisonData.comparisons) {
        // This is a simplified calculation - in a real app, you'd have actual grade distribution data
        const totalStudents =
          this.comparisonData.comparisons[0]?.submissions || 0;
        if (totalStudents > 0) {
          distribution[0].count = Math.floor(totalStudents * 0.1); // A+ ~10%
          distribution[1].count = Math.floor(totalStudents * 0.15); // A ~15%
          distribution[2].count = Math.floor(totalStudents * 0.2); // A- ~20%
          distribution[3].count = Math.floor(totalStudents * 0.25); // B+ ~25%
          distribution[4].count = Math.floor(totalStudents * 0.2); // B ~20%
          distribution[5].count = Math.floor(totalStudents * 0.1); // B- ~10%
        }
      }

      return distribution;
    },
  },
  async mounted() {
    await this.loadPerformanceData();
  },
  methods: {
    async selectCourse(course) {
      this.selectedCourse = course;
      this.loading = true;

      try {
        // Load real comparison data for the selected course
        await this.loadCourseComparisonData(course.id);
        await this.loadStudentRankData(course.id);
        await this.loadClassAveragesData(course.id);
      } catch (error) {
        console.error("Error loading course comparison data:", error);
        this.error = "Failed to load comparison data";
      } finally {
        this.loading = false;
      }
    },

    updateComparisonType(type) {
      this.selectedComparisonType = type;
    },

    updateViewType(type) {
      this.selectedViewType = type;
    },

    // Load real comparison data from API
    async loadCourseComparisonData(courseId) {
      try {
        const response = await studentPerformanceAPI.getCourseComparison(
          courseId
        );
        if (response.success) {
          this.comparisonData = response.data;
          console.log("Loaded comparison data:", this.comparisonData);
        } else {
          console.error("Failed to load comparison data:", response.message);
        }
      } catch (error) {
        console.error("Error loading comparison data:", error);
      }
    },

    // Load student rank data from API
    async loadStudentRankData(courseId) {
      try {
        const userResponse = await utilityAPI.getUserProfile();
        const isStudent =
          userResponse.success &&
          (userResponse.data.role === "student" || userResponse.data.user_type === "student");
        if (isStudent) {
          const studentId = userResponse.data.id;
          const response = await studentPerformanceAPI.getStudentRank(
            studentId,
            courseId
          );
          if (response.success) {
            this.rankData = response.data;
            console.log("Loaded rank data:", this.rankData);
          }
        }
      } catch (error) {
        console.error("Error loading rank data:", error);
      }
    },

    // Load class averages data from API
    async loadClassAveragesData(courseId) {
      try {
        const response = await studentPerformanceAPI.getClassAverages(courseId);
        if (response.success) {
          this.classAverages = response.data;
          console.log("Loaded class averages:", this.classAverages);
        }
      } catch (error) {
        console.error("Error loading class averages:", error);
      }
    },

    // API methods for loading data
    async loadPerformanceData() {
      this.loading = true;
      this.error = null;

      try {
        // Get user profile first
        const userResponse = await utilityAPI.getUserProfile();
        const isStudent =
          userResponse.success &&
          (userResponse.data.role === "student" || userResponse.data.user_type === "student");
        if (isStudent) {
          const studentId = userResponse.data.id;

          // Load student breakdown to get enrolled courses
          const response = await studentPerformanceAPI.getStudentFullBreakdown(
            studentId
          );

          if (response.success) {
            const coursesData = response.data.courses || [];
            console.log("Loaded coursesData:", coursesData);
            // Transform API data to match component expectations
            this.enrolledCourses = coursesData.map((course) => ({
              id: course.course_id,
              code: course.course_code,
              name: course.course_name,
              yourAverage: course.overall_percentage || 0,
              yourRank: 0, // Will be loaded when course is selected
              yourPercentile: 0, // Will be loaded when course is selected
              yourGrade: course.grade_letter || "N/A",
              classAverage: 0, // Will be loaded when course is selected
              totalStudents: 0, // Will be loaded when course is selected
              standardDeviation: 0, // Will be loaded when course is selected
              trend: 0, // Will be loaded when course is selected
              assessments: [], // Will be loaded when course is selected
            }));
            console.log("enrolledCourses after mapping:", this.enrolledCourses);
          }
        }
      } catch (error) {
        console.error("Error loading performance data:", error);
        this.error = "Failed to load performance data";
      } finally {
        this.loading = false;
      }
    },

    showError(message) {
      // TODO: Implement proper error handling/notification
      alert(message);
    },
  },
  watch: {
    enrolledCourses(newCourses) {
      console.log("Watcher: enrolledCourses changed", newCourses);
      if (newCourses.length > 0 && !this.selectedCourse) {
        console.log("Auto-selecting first course:", newCourses[0]);
        this.selectCourse(newCourses[0]);
      }
    },
  },
};
</script>

<style scoped>
.performance-comparison {
  padding: 20px;
  background-color: #f8fafc;
  min-height: 100vh;
}

.comparison-content {
  /* Content container styles if needed */
}

/* Global responsive adjustments */
@media (max-width: 768px) {
  .performance-comparison {
    padding: 16px;
  }
}
</style>
