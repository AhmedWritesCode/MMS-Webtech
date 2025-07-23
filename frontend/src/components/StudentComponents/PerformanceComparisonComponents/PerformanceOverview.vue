<template>
  <div class="overview-cards">
    <div class="overview-card your-performance">
      <div class="card-header">

        <h3>Your Performance</h3>
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

        <h3>Class Average</h3>
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
        
      </div>
    </div>

    <div class="overview-card performance-trend">
      <div class="card-header">

        <h3>Performance Trend</h3>
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
:root {
  --main-green: #16a34a;
  --dark-green: #065f46;
  --black: #111827;
  --gray: #374151;
  --light-green: #22c55e;
  --white: #fff;
}

.overview-cards {
  display: flex;
  flex-direction: column;
  gap: 32px;
  background: transparent;
  padding: 0;
}

.overview-card {
  background: rgba(255,255,255,0.97);
  border-radius: 1.2rem;
  padding: 2rem 1.5rem 1.5rem 1.5rem;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
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
  align-items: center;
  gap: 18px;
  margin-bottom: 28px;
}

.card-header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 800;
  letter-spacing: 1px;
  color: #7C9885;
  text-shadow: none;
}

.performance-icon {
  font-size: 2.2rem;
  background: #B5B682;
  color: #fff;
  border-radius: 50%;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 12px rgba(181, 182, 130, 0.18);
}

.card-stats {
  display: flex;
  justify-content: space-between;
  gap: 24px;
}

.stat-item {
  text-align: left;
  flex: 1;
  background: #f9f9e0;
  border-radius: 0.6rem;
  padding: 1rem 1rem;
  margin: 0 2px;
  box-shadow: 0 1px 6px rgba(181, 182, 130, 0.08);
  min-width: 0;
}

.stat-value {
  display: block;
  font-size: 2.1rem;
  font-weight: 900;
  color: #7C9885;
  margin-bottom: 6px;
  letter-spacing: 1px;
  transition: color 0.2s;
}

.stat-value.trend-positive {
  color: #7C9885;
}
.stat-value.trend-negative {
  color: #e74c3c;
}
.stat-value.trend-neutral {
  color: #B5B682;
}

.stat-label {
  font-size: 0.95rem;
  color: #B5B682;
  font-weight: 600;
  letter-spacing: 0.5px;
  opacity: 0.85;
}

.overview-card.your-performance {
  box-shadow: 0 6px 32px rgba(34, 197, 94, 0.18);
}
.overview-card.class-stats {
  box-shadow: 0 6px 32px rgba(16, 185, 129, 0.18);
}
.overview-card.performance-trend {
  box-shadow: 0 6px 32px rgba(34, 197, 94, 0.18);
}

@media (max-width: 900px) {
  .overview-cards {
    flex-direction: column;
    gap: 24px;
    padding: 18px 0;
  }
  .card-stats {
    flex-direction: column;
    gap: 14px;
  }
  .overview-card {
    padding: 22px 10px;
  }
}
</style>
