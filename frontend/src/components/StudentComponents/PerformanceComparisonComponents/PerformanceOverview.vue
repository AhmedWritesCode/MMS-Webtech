<template>
  <div class="overview-cards">
    <div class="overview-card your-performance">
      <div class="card-header">
        <h3>Your Performance</h3>
        <div class="performance-icon">👤</div>
      </div>
      <div class="card-stats">
        <div class="stat-item">
          <span class="stat-value">{{ course.yourAverage }}%</span>
          <span class="stat-label">Overall Average</span>
        </div>
        <div class="stat-item">
          <span class="stat-value">#{{ yourRank }}</span>
          <span class="stat-label">Class Rank</span>
        </div>
        <div class="stat-item">
          <span class="stat-value">{{ yourPercentile }}%</span>
          <span class="stat-label">Percentile</span>
        </div>
      </div>
    </div>

    <div class="overview-card class-stats">
      <div class="card-header">
        <h3>Class Statistics</h3>
        <div class="performance-icon">👥</div>
      </div>
      <div class="card-stats">
        <div class="stat-item">
          <span class="stat-value">{{ classAverage }}%</span>
          <span class="stat-label">Class Average</span>
        </div>
        <div class="stat-item">
          <span class="stat-value">{{ totalStudents }}</span>
          <span class="stat-label">Total Students</span>
        </div>
        <div class="stat-item">
          <span class="stat-value">{{ standardDeviation }}%</span>
          <span class="stat-label">Std Deviation</span>
        </div>
      </div>
    </div>

    <div class="overview-card performance-trend">
      <div class="card-header">
        <h3>Performance Trend</h3>
        <div class="performance-icon">📈</div>
      </div>
      <div class="card-stats">
        <div class="stat-item">
          <span class="stat-value" :class="getTrendClass(trend)">
            {{ trend }}%
          </span>
          <span class="stat-label">Change from Last Assessment</span>
        </div>
        <div class="stat-item">
          <span class="stat-value">
            {{ aboveAverageCount }}/{{
              course.assessments ? course.assessments.length : 0
            }}
          </span>
          <span class="stat-label">Above Average</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PerformanceOverview",
  props: {
    course: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    rankData: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    classAverages: {
      type: Object,
      required: false,
      default: () => ({}),
    },
  },
  computed: {
    // Use real data from APIs instead of static course data
    yourRank() {
      return this.rankData?.rank || 0;
    },
    yourPercentile() {
      return this.rankData?.percentile || 0;
    },
    classAverage() {
      if (
        this.classAverages?.class_averages &&
        this.classAverages.class_averages.length > 0
      ) {
        const totalAverage = this.classAverages.class_averages.reduce(
          (sum, avg) => sum + (avg.average_percentage || 0),
          0
        );
        return (
          totalAverage / this.classAverages.class_averages.length
        ).toFixed(1);
      }
      return 0;
    },
    totalStudents() {
      return this.rankData?.total_students || 0;
    },
    standardDeviation() {
      // Calculate from class averages if available
      if (
        this.classAverages?.class_averages &&
        this.classAverages.class_averages.length > 0
      ) {
        const percentages = this.classAverages.class_averages.map(
          (avg) => avg.average_percentage || 0
        );
        const mean =
          percentages.reduce((sum, p) => sum + p, 0) / percentages.length;
        const variance =
          percentages.reduce((sum, p) => sum + Math.pow(p - mean, 2), 0) /
          percentages.length;
        return Math.sqrt(variance).toFixed(1);
      }
      return 0;
    },
    trend() {
      // This would need trend data from the API
      return 0;
    },
    aboveAverageCount() {
      if (!this.course.assessments) return 0;
      return this.course.assessments.filter((a) => a.yourScore > a.classAverage)
        .length;
    },
  },
  methods: {
    getTrendClass(trend) {
      return trend > 0
        ? "trend-positive"
        : trend < 0
        ? "trend-negative"
        : "trend-neutral";
    },

    getAboveAverageCount() {
      return this.aboveAverageCount;
    },
  },
};
</script>

<style scoped>
.overview-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.overview-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(124, 152, 133, 0.10);
  border-left: 4px solid #7C9885;
}

.overview-card.your-performance {
  border-left-color: #7C9885;
}

.overview-card.class-stats {
  border-left-color: #B5B682;
}

.overview-card.performance-trend {
  border-left-color: #7C9885;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.performance-icon {
  font-size: 1.5rem;
  opacity: 0.7;
}

.card-stats {
  display: flex;
  justify-content: space-between;
  gap: 16px;
}

.stat-item {
  text-align: center;
  flex: 1;
}

.stat-value {
  display: block;
  font-size: 1.8rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.stat-value.trend-positive {
  color: #7C9885;
}

.stat-value.trend-negative {
  color: #ef4444;
}

.stat-label {
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 500;
}

@media (max-width: 768px) {
  .overview-cards {
    grid-template-columns: 1fr;
  }

  .card-stats {
    flex-direction: column;
    gap: 12px;
  }
}
</style>
