<template>
  <div class="class-summary">
    <h3>Class Performance Summary</h3>
    <div class="summary-grid">
      <div class="summary-card">
        <BarChart2 class="summary-icon" />
        <div class="summary-content">
          <h4>Average Performance</h4>
          <p class="summary-value">{{ classAverage }}%</p>
          <p class="summary-detail">Across all assessments</p>
        </div>
      </div>

      <div class="summary-card">
        <Target class="summary-icon" />
        <div class="summary-content">
          <h4>Highest Achievement</h4>
          <p class="summary-value">{{ getHighestClassScore() }}%</p>
          <p class="summary-detail">Best individual performance</p>
        </div>
      </div>

      <div class="summary-card">
        <LineChart class="summary-icon" />
        <div class="summary-content">
          <h4>Performance Range</h4>
          <p class="summary-value">{{ getPerformanceRange() }}%</p>
          <p class="summary-detail">Difference between highest and lowest</p>
        </div>
      </div>

      <div class="summary-card">
        <Award class="summary-icon" />
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
import { BarChart2, Target, LineChart, Award } from 'lucide-vue-next';
export default {
  name: "ClassSummary",
  components: {
    BarChart2,
    Target,
    LineChart,
    Award,
  },
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
  background: rgba(255,255,255,0.97);
  border-radius: 1.2rem;
  padding: 2.5rem 2rem;
  margin-bottom: 2.5rem;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  border: 2px solid #B5B682;
}

.class-summary h3 {
  margin: 0 0 32px 0;
  font-size: 2rem;
  font-weight: 800;
  color: #7C9885;
  letter-spacing: 1px;
  text-align: center;
  text-shadow: none;
}

.summary-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 28px;
  justify-content: center;
}

.summary-card {
  background: #f9f9e0;
  border-radius: 1.2rem;
  padding: 2rem 1.5rem;
  min-width: 240px;
  max-width: 320px;
  flex: 1 1 240px;
  display: flex;
  align-items: flex-start;
  gap: 18px;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  border: 1.5px solid #B5B682;
  transition: transform 0.2s, box-shadow 0.2s;
  position: relative;
}

.summary-card:hover {
  transform: translateY(-6px) scale(1.03);
  box-shadow: 0 8px 32px rgba(124, 152, 133, 0.18);
  z-index: 2;
}

.summary-icon {
  font-size: 2.5rem;
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #7C9885;
  border-radius: 50%;
  box-shadow: 0 2px 12px rgba(181, 182, 130, 0.12);
  color: #fff;
  border: 2px solid #B5B682;
}

.summary-content {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.summary-content h4 {
  margin: 0 0 10px 0;
  font-size: 1.1rem;
  color: #7C9885;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-shadow: none;
}

.summary-value {
  margin: 0 0 6px 0;
  font-size: 2rem;
  font-weight: 900;
  color: #B5B682;
  text-shadow: none;
  letter-spacing: 1px;
}

.summary-detail {
  margin: 0;
  font-size: 0.95rem;
  color: #23272f;
  font-weight: 600;
  opacity: 0.85;
}

@media (max-width: 900px) {
  .summary-grid {
    flex-direction: column;
    gap: 22px;
    align-items: stretch;
  }
  .summary-card {
    max-width: 100%;
    min-width: 0;
  }
}
</style>
