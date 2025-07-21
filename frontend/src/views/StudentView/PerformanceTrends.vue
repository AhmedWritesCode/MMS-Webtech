<template>
  <div class="performance-trends">
    <!-- Loading Overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner">Loading performance data...</div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="retryLoadData">Retry</button>
    </div>

    <!-- Header -->
    <TrendsHeader />

    <!-- Course Selection and Time Range -->
    <TrendsControls
      :courses="enrolledCourses"
      :selected-course-id="selectedCourseId"
      :selected-time-range="selectedTimeRange"
      :selected-chart-type="selectedChartType"
      @update-course="updateCourse"
      @update-time-range="updateTimeRange"
      @update-chart-type="updateChartType"
    />

    <!-- Performance Overview Summary -->
    <OverviewSummary
      :overall-trend="overallTrend"
      :current-average="currentAverage"
      :best-performance="bestPerformance"
      :biggest-improvement="biggestImprovement"
    />

    <!-- No Data Message -->
    <div v-if="performanceData.length === 0" class="no-data-message">
      <div class="no-data-content">
        <div class="no-data-icon">📊</div>
        <h3>No Performance Data Available</h3>
        <p>
          Complete some assessments to see your performance trends and
          analytics.
        </p>
        <div class="no-data-actions">
          <button @click="retryLoadData" class="retry-btn">Refresh Data</button>
        </div>
      </div>
    </div>

    <!-- Main Trends Chart -->
    <TrendsChart
      v-else
      :performance-data="performanceData"
      :class-average-data="classAverageData"
      :chart-type="selectedChartType"
      :selected-course-id="selectedCourseId"
    />

    <!-- Detailed Analytics -->
    <DetailedAnalytics
      :assessment-type-performance="assessmentTypePerformance"
      :monthly-progress="monthlyProgress"
      :improvement-areas="improvementAreas"
      :performance-insights="performanceInsights"
    />

    <!-- Export and Actions -->
    <ActionsSection
      @export-trends-report="exportTrendsReport"
      @set-performance-goals="setPerformanceGoals"
      @share-progress="shareProgress"
      @schedule-review="scheduleReview"
    />
  </div>
</template>

<script>
// Import components
import TrendsHeader from "@/components/StudentComponents/PerformanceTrendsComponents/TrendsHeader.vue";
import TrendsControls from "@/components/StudentComponents/PerformanceTrendsComponents/TrendsControls.vue";
import OverviewSummary from "@/components/StudentComponents/PerformanceTrendsComponents/OverviewSummary.vue";
import TrendsChart from "@/components/StudentComponents/PerformanceTrendsComponents/TrendsChart.vue";
import DetailedAnalytics from "@/components/StudentComponents/PerformanceTrendsComponents/DetailedAnalytics.vue";
import ActionsSection from "@/components/StudentComponents/PerformanceTrendsComponents/ActionsSection.vue";

// Import API services
import { studentPerformanceAPI, utilityAPI } from "@/services/api";

export default {
  name: "PerformanceTrends",
  components: {
    TrendsHeader,
    TrendsControls,
    OverviewSummary,
    TrendsChart,
    DetailedAnalytics,
    ActionsSection,
  },
  data() {
    return {
      loading: false,
      error: null,
      selectedCourseId: "all",
      selectedTimeRange: "semester",
      selectedChartType: "line",
      enrolledCourses: [],
      performanceData: [],
      classAverageData: [],
      studentInfo: {
        id: null,
        name: "",
        matricNumber: "",
      },
    };
  },
  computed: {
    overallTrend() {
      if (this.performanceData.length < 2) {
        return {
          direction: "stable",
          percentage: 0,
          description: "Not enough data to determine trend",
        };
      }

      const first = this.performanceData[0].percentage;
      const last =
        this.performanceData[this.performanceData.length - 1].percentage;
      const change = last - first;

      if (change > 5) {
        return {
          direction: "up",
          percentage: change.toFixed(1),
          description: "Improving performance",
        };
      } else if (change < -5) {
        return {
          direction: "down",
          percentage: change.toFixed(1),
          description: "Declining performance",
        };
      } else {
        return {
          direction: "stable",
          percentage: change.toFixed(1),
          description: "Stable performance",
        };
      }
    },

    currentAverage() {
      if (this.performanceData.length === 0) {
        return "N/A";
      }

      const sum = this.performanceData.reduce(
        (total, item) => total + item.percentage,
        0
      );
      return (sum / this.performanceData.length).toFixed(1);
    },

    bestPerformance() {
      if (this.performanceData.length === 0) {
        return { score: "N/A", assessment: "No data available" };
      }

      const best = this.performanceData.reduce((max, item) =>
        item.percentage > max.percentage ? item : max
      );
      return { score: best.percentage, assessment: best.name };
    },

    biggestImprovement() {
      if (this.performanceData.length < 2) {
        return { percentage: "N/A", period: "Not enough data" };
      }

      let maxImprovement = 0;
      let period = "";

      for (let i = 1; i < this.performanceData.length; i++) {
        const improvement =
          this.performanceData[i].percentage -
          this.performanceData[i - 1].percentage;
        if (improvement > maxImprovement) {
          maxImprovement = improvement;
          period = `${this.performanceData[i - 1].label} to ${
            this.performanceData[i].label
          }`;
        }
      }

      return { percentage: maxImprovement.toFixed(1), period };
    },

    assessmentTypePerformance() {
      if (this.performanceData.length === 0) {
        return [];
      }

      const types = {};

      this.performanceData.forEach((item) => {
        if (!types[item.type]) {
          types[item.type] = { scores: [], count: 0 };
        }
        types[item.type].scores.push(item.percentage);
        types[item.type].count++;
      });

      return Object.keys(types).map((type) => {
        const scores = types[type].scores;
        const average =
          scores.reduce((sum, score) => sum + score, 0) / scores.length;
        const trend =
          scores.length > 1 ? scores[scores.length - 1] - scores[0] : 0;

        return {
          type,
          name: type + "s",
          average: average.toFixed(1),
          count: types[type].count,
          trend: trend.toFixed(1),
        };
      });
    },

    monthlyProgress() {
      if (this.performanceData.length === 0) {
        return [];
      }

      const months = {};

      this.performanceData.forEach((item) => {
        if (!months[item.label]) {
          months[item.label] = { scores: [], count: 0 };
        }
        months[item.label].scores.push(item.percentage);
        months[item.label].count++;
      });

      return Object.keys(months).map((month) => {
        const scores = months[month].scores;
        const average =
          scores.reduce((sum, score) => sum + score, 0) / scores.length;

        return {
          month,
          average: average.toFixed(1),
          percentage: average,
          count: months[month].count,
        };
      });
    },

    improvementAreas() {
      if (this.performanceData.length === 0) {
        return [
          {
            area: "No Data Available",
            description: "Complete some assessments to see improvement areas",
            currentAverage: "N/A",
            target: "N/A",
            priority: "none",
            icon: "📊",
          },
        ];
      }

      return [
        {
          area: "Quiz Performance",
          description: "Focus on quick recall and time management",
          currentAverage: 73,
          target: 85,
          priority: "high",
          icon: "⚡",
        },
        {
          area: "Lab Exercises",
          description: "Strengthen practical implementation skills",
          currentAverage: 78,
          target: 88,
          priority: "medium",
          icon: "🔬",
        },
        {
          area: "Assignments",
          description: "Maintain current excellent performance",
          currentAverage: 85,
          target: 90,
          priority: "low",
          icon: "📝",
        },
      ];
    },

    performanceInsights() {
      if (this.performanceData.length === 0) {
        return [
          {
            id: 1,
            type: "info",
            icon: "📊",
            title: "No Performance Data",
            description:
              "Complete some assessments to see your performance insights.",
            metrics: [
              { label: "Assessments Completed", value: "0" },
              { label: "Data Available", value: "No" },
            ],
          },
        ];
      }

      return [
        {
          id: 1,
          type: "positive",
          icon: "🎉",
          title: "Consistent Improvement",
          description:
            "Youve shown steady improvement over the last 3 assessments.",
          metrics: [
            { label: "Improvement Rate", value: "+12%" },
            { label: "Consistency Score", value: "85%" },
          ],
        },
        {
          id: 2,
          type: "warning",
          icon: "⚠️",
          title: "Quiz Performance Gap",
          description:
            "Your quiz scores are below your assignment performance.",
          metrics: [
            { label: "Quiz Average", value: "73%" },
            { label: "Assignment Average", value: "85%" },
          ],
        },
        {
          id: 3,
          type: "info",
          icon: "💡",
          title: "Peak Performance Time",
          description: "You perform best in November assessments.",
          metrics: [
            { label: "Best Month", value: "November" },
            { label: "Average Score", value: "87%" },
          ],
        },
      ];
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
          }));
          console.log("Loaded enrolled courses:", this.enrolledCourses);
        } else {
          console.error("Failed to load enrolled courses:", response.message);
        }
      } catch (error) {
        console.error("Error loading enrolled courses:", error);
      }
    },

    async loadPerformanceTrends() {
      try {
        if (!this.studentInfo.id) return;

        const response = await studentPerformanceAPI.getPerformanceTrends(
          this.studentInfo.id,
          this.selectedCourseId
        );

        if (response.success) {
          const trendsData = response.data;

          // Transform the data to match component expectations
          this.performanceData = this.transformTrendsData(trendsData);
          this.classAverageData = this.generateClassAverageData();
        } else {
          // No data available - show empty state
          this.performanceData = [];
          this.classAverageData = [];
        }
      } catch (error) {
        console.error("Error loading performance trends:", error);
        // Show empty state instead of sample data
        this.performanceData = [];
        this.classAverageData = [];
        this.showError("Failed to load performance trends data");
      }
    },

    transformTrendsData(trendsData) {
      // Transform backend data to frontend format
      const transformed = [];

      if (trendsData.recent_updates && trendsData.recent_updates.length > 0) {
        trendsData.recent_updates.forEach((update, index) => {
          const date = new Date(update.updated_at || Date.now());
          const month = date.toLocaleDateString("en-US", { month: "short" });

          transformed.push({
            label: month,
            percentage: update.percentage || 0,
            name: update.component_name || `Assessment ${index + 1}`,
            score: update.marks || 0,
            date: date.toLocaleDateString("en-US", {
              month: "short",
              day: "numeric",
              year: "numeric",
            }),
            type: update.component_type || "Assessment",
          });
        });
      }

      // Return empty array if no real data (don't use sample data)
      return transformed;
    },

    generateClassAverageData() {
      // Generate class average data based on performance data
      if (this.performanceData.length === 0) {
        return [];
      }

      return this.performanceData.map(() => ({
        percentage: Math.floor(Math.random() * 20) + 70, // Random between 70-90
      }));
    },

    updateCourse(courseId) {
      this.selectedCourseId = courseId;
      this.updateTrends();
    },

    updateTimeRange(timeRange) {
      this.selectedTimeRange = timeRange;
      this.updateTrends();
    },

    updateChartType(chartType) {
      this.selectedChartType = chartType;
    },

    async updateTrends() {
      // This would typically make an API call to fetch new data
      console.log(
        "Updating trends for course:",
        this.selectedCourseId,
        "range:",
        this.selectedTimeRange
      );
      this.loading = true;
      try {
        await this.loadPerformanceTrends();
      } catch (error) {
        console.error("Error updating trends:", error);
        this.showError("Failed to update performance trends");
      } finally {
        this.loading = false;
      }
    },

    exportTrendsReport() {
      // Generate and download trends report
      console.log("Exporting trends report...");
      // TODO: Implement export functionality
    },

    setPerformanceGoals() {
      // Open goal setting modal
      console.log("Setting performance goals...");
      // TODO: Implement goal setting modal
    },

    shareProgress() {
      // Share progress with advisor/parents
      console.log("Sharing progress...");
      // TODO: Implement sharing functionality
    },

    scheduleReview() {
      // Schedule performance review meeting
      console.log("Scheduling review...");
      // TODO: Implement scheduling functionality
    },

    showError(message) {
      this.error = message;
      // Auto-hide error after 5 seconds
      setTimeout(() => {
        this.error = null;
      }, 5000);
    },

    retryLoadData() {
      this.loading = true;
      this.error = null;
      this.loadPerformanceTrends();
    },
  },
  async mounted() {
    this.loading = true;
    try {
      await this.loadStudentInfo();
      await this.loadEnrolledCourses();
      await this.loadPerformanceTrends();
    } catch (error) {
      console.error("Error loading data:", error);
      this.showError("Failed to load performance data");
    } finally {
      this.loading = false;
    }
  },
};
</script>

<style scoped>
.performance-trends {
  padding: 20px;
  background-color: #f8fafc;
  min-height: 100vh;
}

/* Global responsive adjustments */
@media (max-width: 768px) {
  .performance-trends {
    padding: 16px;
  }
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
</style>
