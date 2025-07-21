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
      ctx.fillStyle = "#16a34a";
      ctx.fillRect(50, 50, 100, 100);
      ctx.fillStyle = "#000000";
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
  display: flex;
  flex-direction: column;
  gap: 32px;
  background: #111;
  border-radius: 18px;
  padding: 36px 24px;
  box-shadow: 0 4px 24px rgba(22, 163, 74, 0.15);
}

.chart-container,
.breakdown-summary {
  background: #181818;
  border-radius: 14px;
  padding: 28px 20px;
  box-shadow: 0 2px 12px rgba(22, 163, 74, 0.08);
  border: 1px solid #16a34a;
}

.chart-container h3,
.breakdown-summary h3 {
  margin: 0 0 18px 0;
  font-size: 1.3rem;
  font-weight: 800;
  color: #16a34a;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.chart-wrapper {
  position: relative;
  height: 200px;
  background: #101d12;
  border-radius: 8px;
  border: 1px solid #16a34a;
  display: flex;
  align-items: center;
  justify-content: center;
}

.weight-list {
  display: flex;
  flex-direction: column;
  gap: 22px;
}

.weight-item {
  display: flex;
  align-items: center;
  gap: 18px;
}

.weight-bar {
  flex: 1;
  height: 12px;
  background: #222;
  border-radius: 6px;
  overflow: hidden;
  border: 1px solid #16a34a;
}

.weight-fill {
  height: 100%;
  transition: width 0.3s cubic-bezier(.4,2,.6,1);
  border-radius: 6px 0 0 6px;
}

.weight-fill.completed {
  background: linear-gradient(90deg, #16a34a 60%, #22c55e 100%);
}
.weight-fill.partial {
  background: linear-gradient(90deg, #22c55e 60%, #a3e635 100%);
}
.weight-fill.pending {
  background: linear-gradient(90deg, #334d36 60%, #16a34a 100%);
}

.weight-info {
  min-width: 140px;
  font-size: 1rem;
  color: #16a34a;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 2px;
}

.weight-name {
  font-weight: 700;
  color: #16a34a;
  letter-spacing: 0.5px;
}

.weight-percentage {
  color: #a3e635;
  font-size: 0.85rem;
  font-weight: 600;
}

.weight-marks {
  color: #fff;
  font-size: 0.85rem;
  font-weight: 500;
}

@media (max-width: 900px) {
  .grade-breakdown-section {
    padding: 18px 6px;
  }
  .chart-container,
  .breakdown-summary {
    padding: 16px 8px;
  }
  .weight-info {
    min-width: 90px;
    font-size: 0.9rem;
  }
}
</style>
