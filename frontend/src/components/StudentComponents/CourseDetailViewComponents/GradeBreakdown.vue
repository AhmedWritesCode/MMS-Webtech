<template>
  <div class="grade-breakdown-section">
    <div class="chart-container">
      <h3>Grade Breakdown</h3>
      <div class="chart-wrapper">
        <canvas ref="gradeChart" width="400" height="200"></canvas>
      </div>
    </div>

    <div class="breakdown-summary">
      <h3>Weight Distribution</h3>
      <div class="weight-list">
        <div
          v-for="category in weightCategories"
          :key="category.name"
          class="weight-item"
        >
          <div class="weight-bar">
            <div
              class="weight-fill"
              :style="{ width: category.weight + '%' }"
              :class="category.status"
            ></div>
          </div>
          <div class="weight-info">
            <span class="weight-name">{{ category.name }}</span>
            <span class="weight-percentage">{{ category.weight }}%</span>
            <span class="weight-marks">
              {{ category.obtainedMarks }}/{{ category.totalMarks }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "GradeBreakdown",
  props: {
    weightCategories: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  mounted() {
    this.initChart();
  },
  watch: {
    weightCategories: {
      handler() {
        this.updateChart();
      },
      deep: true,
    },
  },
  methods: {
    initChart() {
      // Initialize chart (placeholder for now)
      // In a real implementation, you would use Chart.js or similar
      const canvas = this.$refs.gradeChart;
      const ctx = canvas.getContext("2d");

      // Simple placeholder visualization
      ctx.fillStyle = "#3b82f6";
      ctx.fillRect(50, 50, 100, 100);
      ctx.fillStyle = "#ffffff";
      ctx.font = "16px Arial";
      ctx.fillText("Chart Placeholder", 60, 105);
    },

    updateChart() {
      // Update chart when data changes
      this.initChart();
    },
  },
};
</script>

<style scoped>
.grade-breakdown-section {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 30px;
  margin-bottom: 30px;
}

.chart-container,
.breakdown-summary {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.chart-container h3,
.breakdown-summary h3 {
  margin: 0 0 20px 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

.chart-wrapper {
  position: relative;
  height: 200px;
}

.weight-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.weight-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.weight-bar {
  flex: 1;
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.weight-fill {
  height: 100%;
  transition: width 0.3s ease;
}

.weight-fill.completed {
  background: #10b981;
}
.weight-fill.partial {
  background: #f59e0b;
}
.weight-fill.pending {
  background: #94a3b8;
}

.weight-info {
  min-width: 120px;
  font-size: 0.9rem;
}

.weight-name {
  display: block;
  font-weight: 600;
  color: #1e293b;
}

.weight-percentage {
  display: block;
  color: #64748b;
  font-size: 0.8rem;
}

.weight-marks {
  display: block;
  color: #64748b;
  font-size: 0.8rem;
}

@media (max-width: 1200px) {
  .grade-breakdown-section {
    grid-template-columns: 1fr;
  }
}
</style>
