<template>
  <div class="class-summary">
    <h3>Class Performance Summary</h3>
    <div class="summary-grid">
      <div class="summary-card">
        <div class="summary-icon">📊</div>
        <div class="summary-content">
          <h4>Average Performance</h4>
          <p class="summary-value">{{ classAverage }}%</p>
          <p class="summary-detail">Across all assessments</p>
        </div>
      </div>

      <div class="summary-card">
        <div class="summary-icon">🎯</div>
        <div class="summary-content">
          <h4>Highest Achievement</h4>
          <p class="summary-value">{{ getHighestClassScore() }}%</p>
          <p class="summary-detail">Best individual performance</p>
        </div>
      </div>

      <div class="summary-card">
        <div class="summary-icon">📈</div>
        <div class="summary-content">
          <h4>Performance Range</h4>
          <p class="summary-value">{{ getPerformanceRange() }}%</p>
          <p class="summary-detail">Difference between highest and lowest</p>
        </div>
      </div>

      <div class="summary-card">
        <div class="summary-icon">🏆</div>
        <div class="summary-content">
          <h4>Your Position</h4>
          <p class="summary-value">Top {{ 100 - yourPercentile }}%</p>
          <p class="summary-detail">Of the class</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ClassSummary",
  props: {
    selectedCourse: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    classAverages: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    rankData: {
      type: Object,
      required: false,
      default: () => ({}),
    },
  },
  computed: {
    // Use real data from API instead of static course data
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
    yourPercentile() {
      return this.rankData?.percentile || 0;
    },
    highestClassScore() {
      if (
        this.classAverages?.class_averages &&
        this.classAverages.class_averages.length > 0
      ) {
        return Math.max(
          ...this.classAverages.class_averages.map(
            (avg) => avg.max_percentage || 0
          )
        ).toFixed(1);
      }
      return 0;
    },
    performanceRange() {
      if (
        this.classAverages?.class_averages &&
        this.classAverages.class_averages.length > 0
      ) {
        const maxPercentage = Math.max(
          ...this.classAverages.class_averages.map(
            (avg) => avg.max_percentage || 0
          )
        );
        const minPercentage = Math.min(
          ...this.classAverages.class_averages.map(
            (avg) => avg.min_percentage || 0
          )
        );
        return (maxPercentage - minPercentage).toFixed(1);
      }
      return 0;
    },
  },
  methods: {
    getHighestClassScore() {
      return this.highestClassScore;
    },

    getPerformanceRange() {
      return this.performanceRange;
    },
  },
};
</script>

<style scoped>
.class-summary {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.class-summary h3 {
  margin: 0 0 24px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.summary-card {
  background: #f8fafc;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  transition: transform 0.2s;
}

.summary-card:hover {
  transform: translateY(-2px);
}

.summary-icon {
  font-size: 2rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.summary-content h4 {
  margin: 0 0 8px 0;
  font-size: 1rem;
  color: #64748b;
  font-weight: 600;
}

.summary-value {
  margin: 0 0 4px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.summary-detail {
  margin: 0;
  font-size: 0.8rem;
  color: #64748b;
}

@media (max-width: 768px) {
  .summary-grid {
    grid-template-columns: 1fr;
  }
}
</style>
