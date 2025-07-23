<template>
  <div class="compare-bg">
    <div class="compare-hero">
      <div class="compare-hero-content">
        <h1 class="compare-title">Anonymous Performance Comparison</h1>
        <p class="compare-subtitle">
          Compare your performance with classmates anonymously. All data is displayed without identifying individual students.
        </p>
      </div>
    </div>
    <div class="course-selector-row">
      <div class="course-selector">
        <button
          v-for="course in courses"
          :key="course.id"
          :class="['course-pill', { active: selectedCourse && selectedCourse.id === course.id }]"
          @click="selectCourse(course)"
        >
          {{ course.code }}<span v-if="course.name"> - {{ course.name }}</span>
        </button>
      </div>
      <div class="select-course-label">Select Course</div>
    </div>
    <div class="performance-comparison">
      <!-- Header -->
 

    <!-- Course Selection -->
    <CourseSelector
      :courses="enrolledCourses"
      :selected-course="selectedCourse"
      @course-selected="selectCourse"
    />

    <div v-if="selectedCourse" class="comparison-content">
      <PerformanceOverview
        :course="selectedCourse"
        :rank-data="rankData"
        :class-averages="classAverages"
      />
      <ComparisonControls
        :selected-comparison-type="selectedComparisonType"
        :selected-view-type="selectedViewType"
        @update-comparison-type="updateComparisonType"
        @update-view-type="updateViewType"
      />
      <AssessmentComparison
        v-if="selectedViewType === 'assessments'"
        :assessments="transformedAssessments"
      />
      <GradeDistribution
        v-if="selectedViewType === 'distribution'"
        :grade-distribution="gradeDistribution"
        :selected-course="selectedCourse"
      />
      <PercentileRanking
        v-if="selectedViewType === 'percentiles'"
        :selected-course="selectedCourse"
        :rank-data="rankData"
      />
      <ClassSummary
        :selected-course="selectedCourse"
        :class-averages="classAverages"
        :rank-data="rankData"
      />
      <AnonymousInsights
        :selected-course="selectedCourse"
        :comparison-data="comparisonData"
      />
    </div>
    <NoCourseSelected v-else />
  </div>
</div>
</template>

<script>
// Import components
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
      courses: [],
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
        weight: 0,
        maxScore: comparison.max_marks,
        yourScore: comparison.my_marks || 0,
        classAverage: comparison.class_average || 0,
        highestScore: comparison.max_marks,
        yourPercentile: comparison.percentile || 0,
        submissions: comparison.submissions || 0,
      }));
    },
    gradeDistribution() {
      if (!this.selectedCourse || !this.comparisonData) return [];
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
      if (this.comparisonData.comparisons) {
        const totalStudents =
          this.comparisonData.comparisons[0]?.submissions || 0;
        if (totalStudents > 0) {
          distribution[0].count = Math.floor(totalStudents * 0.1);
          distribution[1].count = Math.floor(totalStudents * 0.15);
          distribution[2].count = Math.floor(totalStudents * 0.2);
          distribution[3].count = Math.floor(totalStudents * 0.25);
          distribution[4].count = Math.floor(totalStudents * 0.2);
          distribution[5].count = Math.floor(totalStudents * 0.1);
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
    async loadPerformanceData() {
      this.loading = true;
      this.error = null;
      try {
        const userResponse = await utilityAPI.getUserProfile();
        const isStudent =
          userResponse.success &&
          (userResponse.data.role === "student" || userResponse.data.user_type === "student");
        if (isStudent) {
          const studentId = userResponse.data.id;
          const response = await studentPerformanceAPI.getStudentFullBreakdown(
            studentId
          );
          if (response.success) {
            const coursesData = response.data.courses || [];
            console.log("Loaded coursesData:", coursesData);
            this.enrolledCourses = coursesData.map((course) => ({
              id: course.course_id,
              code: course.course_code,
              name: course.course_name,
              yourAverage: course.overall_percentage || 0,
              yourRank: 0,
              yourPercentile: 0,
              yourGrade: course.grade_letter || "N/A",
              classAverage: 0,
              totalStudents: 0,
              standardDeviation: 0,
              trend: 0,
              assessments: [],
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
.compare-bg {
  min-height: 100vh;
  width: 100vw;
  background: linear-gradient(135deg, #7C9885 0%, #B5B682 100%);
  padding: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.performance-comparison {
  background: #fff;
  border-radius: 1.5rem;
  box-shadow: 0 6px 24px 0 rgba(124, 152, 133, 0.10);
  padding: 2.5rem;
  margin: 2.5rem 0 2rem 0;
  max-width: 1200px;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.compare-hero {
  width: 100%;
  max-width: 1200px;
  margin: 2.5rem 0 1.5rem 0;
  border-radius: 1.5rem 1.5rem 0 0;
  background: #fff;
  color: #7C9885;
  box-shadow: 0 4px 24px 0 rgba(124, 152, 133, 0.10);
  padding: 2.5rem 2rem 2rem 2rem;
  display: flex;
  align-items: center;
  min-height: 120px;
  border-bottom: 4px solid #0f0;
}
.compare-hero-content {
  width: 100%;
  text-align: left;
}
.compare-title {
  font-size: 2.2rem;
  font-weight: 800;
  margin: 0 0 0.5rem 0;
  letter-spacing: -1px;
  color: #7C9885;
  font-family: 'Segoe UI', 'Arial', 'Helvetica Neue', Arial, sans-serif;
}
.compare-subtitle {
  font-size: 1.15rem;
  opacity: 0.95;
  margin: 0;
  color: #B5B682;
  font-family: 'Segoe UI', 'Arial', 'Helvetica Neue', Arial, sans-serif;
}
.course-selector-row {
  width: 100%;
  max-width: 1200px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  margin-top: 0;
}
.course-selector {
  display: flex;
  gap: 1rem;
}
.course-pill {
  background: #fff;
  color: #7C9885;
  border: 2px solid #7C9885;
  border-radius: 2rem;
  padding: 0.7rem 1.5rem;
  font-size: 1.05rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s, color 0.2s, box-shadow 0.2s, border 0.2s;
  box-shadow: 0 2px 8px 0 rgba(124, 152, 133, 0.06);
}
.course-pill.active, .course-pill:hover {
  background: #7C9885;
  color: #fff;
  border-color: #7C9885;
  box-shadow: 0 4px 16px 0 rgba(124, 152, 133, 0.12);
}
.select-course-label {
  font-size: 1.1rem;
  color: #0f0;
  margin-left: 2rem;
}

button, .btn, .comparison-controls button, .comparison-controls .active {
  background: #fff;
  color: #7C9885;
  border: 2px solid #7C9885;
  border-radius: 2rem;
  font-weight: 600;
  transition: background 0.2s, color 0.2s, border 0.2s;
}
button.active, .btn.active, .comparison-controls button.active, .comparison-controls .active {
  background: #B5B682;
  color: #fff;
  border-color: #B5B682;
}
button:hover, .btn:hover, .comparison-controls button:hover {
  background: #7C9885;
  color: #fff;
  border-color: #7C9885;
}
@media (max-width: 1200px) {
  .performance-comparison, .compare-hero, .course-selector-row {
    max-width: 98vw;
    padding-left: 1vw;
    padding-right: 1vw;
  }
}
@media (max-width: 900px) {
  .compare-hero {
    padding: 1.5rem 1rem 1rem 1rem;
    min-height: 90px;
    margin: 1.5rem 0 1rem 0;
  }
  .course-selector-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
  }
  .select-course-label {
    margin-left: 0;
    margin-top: 0.5rem;
  }
  .performance-comparison {
    padding: 1rem;
    margin: 1rem 0 1rem 0;
  }
}
body, .performance-comparison {
  background: none !important;
}
</style>
