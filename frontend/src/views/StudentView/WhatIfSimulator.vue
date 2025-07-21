<template>
  <div class="what-if-simulator">
    <!-- Loading Overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner">Loading simulator data...</div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="retryLoadData">Retry</button>
    </div>

    <!-- Header -->
    <SimulatorHeader />

    <!-- Course Selection -->
    <CourseSelection
      :courses="enrolledCourses"
      :selected-course="selectedCourse"
      @course-selected="selectCourse"
    />

    <!-- Simulator Content -->
    <div v-if="selectedCourse" class="simulator-content">
      <!-- Current Status Overview -->
      <StatusOverview
        :course="selectedCourse"
        :projected-grade="projectedGrade"
        :grade-goals="gradeGoals"
        :selected-goal="selectedGoal"
        @goal-selected="selectGoal"
      />

      <!-- Assessment Simulator -->
      <AssessmentSimulator
        :assessments="selectedCourse.assessments"
        @update-projections="updateProjections"
        @reset-to-actual="resetToActual"
        @fill-optimistic="fillOptimistic"
        @fill-realistic="fillRealistic"
        @fill-conservative="fillConservative"
      />

      <!-- Scenario Analysis -->
      <ScenarioAnalysis :scenarios="scenarios" />

      <!-- Grade Breakdown Chart -->
      <GradeBreakdownVisualization
        :grade-breakdown="gradeBreakdown"
        :completed-weight="completedWeight"
        :current-contribution="currentContribution"
        :projected-contribution="projectedContribution"
      />

      <!-- Recommendations -->
      <RecommendationsSection
        :recommendations="recommendations"
        @recommendation-action="handleRecommendationAction"
      />
    </div>

    <!-- No Course Selected State -->
    <NoCourseSelected v-else />

    <!-- No Data Message -->
    <div
      v-if="enrolledCourses.length === 0 && !loading"
      class="no-data-message"
    >
      <div class="no-data-content">
        <div class="no-data-icon">📊</div>
        <h3>No Courses Available</h3>
        <p>
          You need to be enrolled in courses with assessment components to use
          the grade simulator.
        </p>
        <div class="no-data-actions">
          <button @click="retryLoadData" class="retry-btn">Refresh Data</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// Import components
import SimulatorHeader from "@/components/StudentComponents/WhatIfSimulatorComponents/SimulatorHeader.vue";
import CourseSelection from "@/components/StudentComponents/WhatIfSimulatorComponents/CourseSelection.vue";
import StatusOverview from "@/components/StudentComponents/WhatIfSimulatorComponents/StatusOverview.vue";
import AssessmentSimulator from "@/components/StudentComponents/WhatIfSimulatorComponents/AssessmentSimulator.vue";
import ScenarioAnalysis from "@/components/StudentComponents/WhatIfSimulatorComponents/ScenarioAnalysis.vue";
import GradeBreakdownVisualization from "@/components/StudentComponents/WhatIfSimulatorComponents/GradeBreakdownVisualization.vue";
import RecommendationsSection from "@/components/StudentComponents/WhatIfSimulatorComponents/RecommendationsSection.vue";
import NoCourseSelected from "@/components/StudentComponents/WhatIfSimulatorComponents/NoCourseSelected.vue";

// Import API services
import { studentPerformanceAPI, utilityAPI } from "@/services/api";

export default {
  name: "WhatIfSimulator",
  components: {
    SimulatorHeader,
    CourseSelection,
    StatusOverview,
    AssessmentSimulator,
    ScenarioAnalysis,
    GradeBreakdownVisualization,
    RecommendationsSection,
    NoCourseSelected,
  },
  data() {
    return {
      loading: false,
      error: null,
      selectedCourse: null,
      selectedGoal: null,
      enrolledCourses: [],
      studentInfo: {
        id: null,
        name: "",
        matricNumber: "",
      },
    };
  },
  computed: {
    projectedGrade() {
      if (!this.selectedCourse || !this.selectedCourse.assessments)
        return { letter: "N/A", percentage: 0, status: "", trend: "" };

      const totalScore = this.calculateTotalScore();
      const letter = this.getLetterGrade(totalScore);
      const current = this.selectedCourse.currentMarks || 0;

      let status = "";
      let trend = "";

      if (totalScore > current) {
        status = "Improving";
        trend = "positive";
      } else if (totalScore < current) {
        status = "Declining";
        trend = "negative";
      } else {
        status = "Stable";
        trend = "neutral";
      }

      return {
        letter,
        percentage: totalScore.toFixed(1),
        status,
        trend,
      };
    },

    gradeGoals() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return [];

      const goals = [
        { grade: "A+", minScore: 90 },
        { grade: "A", minScore: 80 },
        { grade: "A-", minScore: 75 },
        { grade: "B+", minScore: 70 },
        { grade: "B", minScore: 65 },
        { grade: "B-", minScore: 60 },
        { grade: "C+", minScore: 55 },
        { grade: "C", minScore: 50 },
      ];

      return goals.map((goal) => {
        const requiredScore = this.calculateRequiredScore(goal.minScore);
        return {
          grade: goal.grade,
          required: requiredScore.toFixed(1),
          achievable: requiredScore <= 100 && requiredScore >= 0,
        };
      });
    },

    scenarios() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return {};

      return {
        bestCase: this.calculateBestCase(),
        realistic: this.calculateRealisticCase(),
        worstCase: this.calculateWorstCase(),
      };
    },

    gradeBreakdown() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return [];

      return this.selectedCourse.assessments.map((assessment) => {
        const score = assessment.completed
          ? (assessment.actualScore / assessment.maxScore) * 100
          : (assessment.predictedScore / assessment.maxScore) * 100;

        return {
          name: assessment.name,
          weight: assessment.weight,
          score: score.toFixed(1),
          status: assessment.completed ? "completed" : "predicted",
        };
      });
    },

    completedWeight() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return 0;
      return this.selectedCourse.assessments
        .filter((a) => a.completed)
        .reduce((sum, a) => sum + a.weight, 0);
    },

    currentContribution() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return 0;
      return this.selectedCourse.assessments
        .filter((a) => a.completed)
        .reduce((sum, a) => sum + (a.actualScore / a.maxScore) * a.weight, 0);
    },

    projectedContribution() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return 0;
      return this.selectedCourse.assessments
        .filter((a) => !a.completed)
        .reduce(
          (sum, a) => sum + (a.predictedScore / a.maxScore) * a.weight,
          0
        );
    },

    recommendations() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return [];

      const recommendations = [];
      const totalScore = this.calculateTotalScore();

      // Add recommendations based on current performance
      if (totalScore < 65) {
        recommendations.push({
          id: 1,
          title: "Focus on Upcoming Assessments",
          description:
            "Your current trajectory suggests you need to improve significantly in remaining assessments.",
          priority: "high",
          icon: "🚨",
          focusArea: "All remaining assessments",
          impact: "Prevent failing grade",
          actionText: "Create Study Plan",
          timeframe: "Immediate",
        });
      }

      if (totalScore >= 65 && totalScore < 75) {
        recommendations.push({
          id: 2,
          title: "Target Higher Performance",
          description:
            "You're on track for a passing grade, but could achieve better with focused effort.",
          priority: "medium",
          icon: "📈",
          focusArea: "High-weight assessments",
          impact: "Improve final grade by 5-10%",
          actionText: "Optimize Strategy",
          timeframe: "2-3 weeks",
        });
      }

      if (totalScore >= 75) {
        recommendations.push({
          id: 3,
          title: "Maintain Excellence",
          description:
            "You're performing well! Focus on consistency to secure your target grade.",
          priority: "low",
          icon: "⭐",
          focusArea: "Consistency across all areas",
          impact: "Secure current grade level",
          actionText: "Stay on Track",
          timeframe: "Ongoing",
        });
      }

      // Check for specific assessment types that need attention
      const quizAverage = this.getAssessmentTypeAverage("quiz");
      if (quizAverage < 70) {
        recommendations.push({
          id: 4,
          title: "Improve Quiz Performance",
          description:
            "Your quiz scores are below optimal. Focus on quick recall and time management.",
          priority: "medium",
          icon: "⚡",
          focusArea: "Quiz preparation",
          impact: "Improve overall grade by 3-5%",
          actionText: "Practice Quizzes",
        });
      }

      return recommendations;
    },
  },
  methods: {
    async loadStudentInfo() {
      try {
        const response = await utilityAPI.getUserProfile();
        if (response.success) {
          const userData = response.data;
          this.studentInfo = {
            id: userData.id,
            name: userData.full_name || "Student",
            matricNumber: userData.username || "",
          };
          console.log("Loaded student info:", this.studentInfo);
        } else {
          console.error("Failed to load student info:", response.message);
        }
      } catch (error) {
        console.error("Error loading student info:", error);
      }
    },

    async loadEnrolledCourses() {
      try {
        if (!this.studentInfo.id) return;

        const response = await studentPerformanceAPI.getStudentFullBreakdown(
          this.studentInfo.id
        );

        if (response.success) {
          const coursesData = response.data.courses || [];
          this.enrolledCourses = coursesData.map((course) => ({
            id: course.course_id,
            code: course.course_code,
            name: course.course_name,
            currentGrade: course.grade_letter || "N/A",
            currentMarks: course.overall_percentage || 0,
            completedWeight: course.progress || 0,
            assessments: [], // Will be loaded separately
          }));
          console.log("Loaded enrolled courses:", this.enrolledCourses);
        } else {
          console.error("Failed to load enrolled courses:", response.message);
        }
      } catch (error) {
        console.error("Error loading enrolled courses:", error);
      }
    },

    async loadCourseAssessments(courseId) {
      try {
        const response = await studentPerformanceAPI.getStudentCourseMarks(
          this.studentInfo.id,
          courseId
        );

        if (response.success) {
          const components = response.data.components || [];
          return components.map((component) => ({
            id: component.component_id,
            name: component.component_name,
            type: component.component_type,
            weight: component.weight_percentage,
            maxScore: component.max_marks,
            actualScore: component.marks_obtained,
            completed: component.marks_obtained !== null,
            dueDate: component.graded_at
              ? new Date(component.graded_at).toISOString().split("T")[0]
              : null,
            description: component.component_name,
            predictedScore: component.marks_obtained || 0, // Default to actual score or 0
          }));
        } else {
          console.error("Failed to load course assessments:", response.message);
          return [];
        }
      } catch (error) {
        console.error("Error loading course assessments:", error);
        return [];
      }
    },

    async selectCourse(course) {
      this.selectedCourse = { ...course };

      // Load assessment components for the selected course
      if (this.selectedCourse && this.selectedCourse.id) {
        const assessments = await this.loadCourseAssessments(
          this.selectedCourse.id
        );
        this.selectedCourse.assessments = assessments;
        console.log(
          "Loaded assessments for course:",
          this.selectedCourse.id,
          assessments
        );
      }

      this.updateProjections();
    },

    selectGoal(grade) {
      this.selectedGoal = grade;
    },

    calculateTotalScore() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return 0;

      let totalScore = 0;

      this.selectedCourse.assessments.forEach((assessment) => {
        const score = assessment.completed
          ? assessment.actualScore
          : assessment.predictedScore;
        totalScore += (score / assessment.maxScore) * assessment.weight;
      });

      return totalScore;
    },

    getLetterGrade(percentage) {
      if (percentage >= 90) return "A+";
      if (percentage >= 80) return "A";
      if (percentage >= 75) return "A-";
      if (percentage >= 70) return "B+";
      if (percentage >= 65) return "B";
      if (percentage >= 60) return "B-";
      if (percentage >= 55) return "C+";
      if (percentage >= 50) return "C";
      return "F";
    },

    calculateRequiredScore(targetPercentage) {
      if (!this.selectedCourse) return 0;

      const currentContribution = this.currentContribution;
      const remainingWeight = 100 - this.completedWeight;

      if (remainingWeight === 0) return 0;

      const requiredContribution = targetPercentage - currentContribution;
      return (requiredContribution / remainingWeight) * 100;
    },

    updateProjections() {
      // This method is called when predicted scores change
      // The computed properties will automatically update
    },

    resetToActual() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return;

      this.selectedCourse.assessments.forEach((assessment) => {
        if (!assessment.completed) {
          assessment.predictedScore = 0;
        }
      });
      this.updateProjections();
    },

    fillOptimistic() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return;

      this.selectedCourse.assessments.forEach((assessment) => {
        if (!assessment.completed) {
          assessment.predictedScore = Math.round(assessment.maxScore * 0.9); // 90%
        }
      });
      this.updateProjections();
    },

    fillRealistic() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return;

      this.selectedCourse.assessments.forEach((assessment) => {
        if (!assessment.completed) {
          assessment.predictedScore = Math.round(assessment.maxScore * 0.75); // 75%
        }
      });
      this.updateProjections();
    },

    fillConservative() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return;

      this.selectedCourse.assessments.forEach((assessment) => {
        if (!assessment.completed) {
          assessment.predictedScore = Math.round(assessment.maxScore * 0.6); // 60%
        }
      });
      this.updateProjections();
    },

    calculateBestCase() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return {};

      // Calculate assuming 95% on all remaining assessments
      let totalScore = this.currentContribution;
      const pendingAssessments = this.selectedCourse.assessments.filter(
        (a) => !a.completed
      );

      pendingAssessments.forEach((assessment) => {
        totalScore += 0.95 * assessment.weight;
      });

      return {
        grade: this.getLetterGrade(totalScore),
        percentage: totalScore.toFixed(1),
        description:
          "Achieving excellent performance on all remaining assessments",
        requirements: [
          "Score 95% or higher on all remaining assessments",
          "Consistent study and preparation",
          "Excel in final examination",
        ],
      };
    },

    calculateRealisticCase() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return {};

      // Calculate based on current trend (average of completed assessments)
      const completedAssessments = this.selectedCourse.assessments.filter(
        (a) => a.completed
      );

      if (completedAssessments.length === 0) {
        return {
          grade: "N/A",
          percentage: "0.0",
          description: "No completed assessments to base prediction on",
          requirements: [
            "Complete at least one assessment to see realistic predictions",
          ],
        };
      }

      const averagePerformance =
        completedAssessments.reduce(
          (sum, a) => sum + a.actualScore / a.maxScore,
          0
        ) / completedAssessments.length;

      let totalScore = this.currentContribution;
      const pendingAssessments = this.selectedCourse.assessments.filter(
        (a) => !a.completed
      );

      pendingAssessments.forEach((assessment) => {
        totalScore += averagePerformance * assessment.weight;
      });

      return {
        grade: this.getLetterGrade(totalScore),
        percentage: totalScore.toFixed(1),
        description: "Maintaining current performance level",
        requirements: [
          `Maintain ${(averagePerformance * 100).toFixed(
            0
          )}% average on remaining assessments`,
          "Continue current study habits",
          "Stay consistent with effort",
        ],
      };
    },

    calculateWorstCase() {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return {};

      // Calculate assuming 50% on all remaining assessments
      let totalScore = this.currentContribution;
      const pendingAssessments = this.selectedCourse.assessments.filter(
        (a) => !a.completed
      );

      pendingAssessments.forEach((assessment) => {
        totalScore += 0.5 * assessment.weight;
      });

      return {
        grade: this.getLetterGrade(totalScore),
        percentage: totalScore.toFixed(1),
        description: "Poor performance on remaining assessments",
        requirements: [
          "Score at least 65% on remaining assessments",
          "Seek help from instructors",
          "Develop comprehensive study plan",
          "Consider tutoring or study groups",
        ],
      };
    },

    getAssessmentTypeAverage(type) {
      if (!this.selectedCourse || !this.selectedCourse.assessments) return 0;

      const assessments = this.selectedCourse.assessments.filter(
        (a) => a.type === type && a.completed
      );

      if (assessments.length === 0) return 0;

      const total = assessments.reduce(
        (sum, a) => sum + (a.actualScore / a.maxScore) * 100,
        0
      );

      return total / assessments.length;
    },

    handleRecommendationAction(recommendation) {
      // Handle recommendation actions
      console.log("Recommendation action:", recommendation.actionText);
      // TODO: Implement specific actions based on recommendation type

      switch (recommendation.id) {
        case 1:
          // Create study plan
          this.$router.push("/study-planner");
          break;
        case 2:
          // Optimize strategy
          this.$router.push("/performance-analysis");
          break;
        case 3:
          // Stay on track
          this.$router.push("/goal-tracker");
          break;
        case 4:
          // Practice quizzes
          this.$router.push("/quiz-practice");
          break;
        default:
          console.log("Unknown recommendation action");
      }
    },

    async loadSimulatorData() {
      try {
        this.loading = true;
        this.error = null;

        await this.loadStudentInfo();
        await this.loadEnrolledCourses();

        // Auto-select first course if available
        if (this.enrolledCourses.length > 0) {
          await this.selectCourse(this.enrolledCourses[0]);
        }
      } catch (error) {
        console.error("Error loading simulator data:", error);
        this.error = "Failed to load simulator data. Please try again later.";
      } finally {
        this.loading = false;
      }
    },

    retryLoadData() {
      this.loadSimulatorData();
    },
  },
  async mounted() {
    // Auto-select first course if available
    if (this.enrolledCourses.length > 0) {
      this.selectedCourse = { ...this.enrolledCourses[0] };
    }

    // Load simulator data
    await this.loadSimulatorData();
  },
};
</script>

<style scoped>
.what-if-simulator {
  padding: 20px;
  background-color: #f8fafc;
  min-height: 100vh;
}

.simulator-content {
  /* Main content styles if needed */
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.loading-spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.error-message {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background-color: #ffcccc;
  padding: 10px;
  z-index: 1000;
}

.error-message p {
  margin: 0;
  color: #ff0000;
}

.error-message button {
  background-color: #3498db;
  color: #fff;
  border: none;
  padding: 5px 10px;
  margin-top: 10px;
  cursor: pointer;
}

.no-data-message {
  background: white;
  border-radius: 12px;
  padding: 60px 30px;
  margin: 30px 0;
  text-align: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.no-data-content {
  max-width: 500px;
  margin: 0 auto;
}

.no-data-icon {
  font-size: 64px;
  margin-bottom: 20px;
  opacity: 0.6;
}

.no-data-content h3 {
  margin: 0 0 16px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.no-data-content p {
  margin: 0 0 24px 0;
  color: #64748b;
  font-size: 1rem;
  line-height: 1.5;
}

.no-data-actions {
  margin-top: 20px;
}

.retry-btn {
  background-color: #3b82f6;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.2s;
}

.retry-btn:hover {
  background-color: #2563eb;
}

/* Global responsive adjustments */
@media (max-width: 768px) {
  .what-if-simulator {
    padding: 16px;
  }
}
</style>
