<template>
  <div class="overview-cards">
    <div class="overview-card your-performance">
      <div class="card-header">
        <div class="performance-icon">👤</div>
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
        <div class="performance-icon">👥</div>
        <h3>Class Statistics</h3>
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
        <div class="performance-icon">📈</div>
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
  background: var(--black);
  padding: 32px 0;
}

.overview-card {
  background: linear-gradient(135deg, var(--dark-green) 60%, var(--main-green) 100%);
  border-radius: 18px;
  padding: 32px 28px;
  box-shadow: 0 6px 32px rgba(22, 163, 74, 0.18);
  border: none;
  color: var(--white);
  transition: transform 0.2s;
  position: relative;
  overflow: hidden;
}
.overview-card:before {
  content: "";
  position: absolute;
  top: -40px;
  right: -40px;
  width: 120px;
  height: 120px;
  background: rgba(34, 197, 94, 0.12);
  border-radius: 50%;
  z-index: 0;
}
.overview-card .card-header,
.overview-card .card-stats {
  position: relative;
  z-index: 1;
}

.overview-card:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 40px rgba(22, 163, 74, 0.28);
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
  color: var(--white);
  text-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);
}

.performance-icon {
  font-size: 2.2rem;
  background: var(--main-green);
  color: var(--white);
  border-radius: 50%;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 12px rgba(34, 197, 94, 0.18);
}

.card-stats {
  display: flex;
  justify-content: space-between;
  gap: 24px;
}

.stat-item {
  text-align: left;
  flex: 1;
  background: rgba(17, 24, 39, 0.7);
  border-radius: 10px;
  padding: 18px 14px;
  margin: 0 2px;
  box-shadow: 0 1px 6px rgba(34, 197, 94, 0.08);
  min-width: 0;
}

.stat-value {
  display: block;
  font-size: 2.1rem;
  font-weight: 900;
  color: var(--light-green);
  margin-bottom: 6px;
  letter-spacing: 1px;
  text-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);
  transition: color 0.2s;
}

.stat-value.trend-positive {
  color: #22d3ee;
}
.stat-value.trend-negative {
  color: #f87171;
}
.stat-value.trend-neutral {
  color: var(--light-green);
}

.stat-label {
  font-size: 0.95rem;
  color: #a7f3d0;
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
