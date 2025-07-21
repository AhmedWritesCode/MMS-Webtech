<template>
  <div class="section-card performance-trends">
    <div class="section-header">
      <h3>Performance Trends</h3>
      <div class="trend-controls">
        <select
          :value="selectedTrendPeriod"
          @change="$emit('update-trend-period', $event.target.value)"
          class="trend-select"
        >
          <option value="semester">By Semester</option>
          <option value="year">By Year</option>
          <option value="course">By Course Type</option>
        </select>
      </div>
    </div>
    <div class="trend-chart">
      <div class="chart-container">
        <div class="chart-bars">
          <div
            v-for="(period, index) in performanceTrends"
            :key="index"
            class="trend-bar"
          >
            <div class="bar-container">
              <div
                class="bar-fill"
                :style="{ height: (period.gpa / 4.0) * 100 + '%' }"
                :class="getPerformanceClass(period.gpa)"
              ></div>
            </div>
            <div class="bar-label">{{ period.label }}</div>
            <div class="bar-value">{{ period.gpa }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PerformanceTrendsCard",
  props: {
    performanceTrends: {
      type: Array,
      required: true,
      default: () => [],
    },
    selectedTrendPeriod: {
      type: String,
      required: true,
      default: "semester",
    },
  },
  emits: ["update-trend-period"],
  methods: {
    getPerformanceClass(value) {
      if (value >= 3.5) return "excellent";
      if (value >= 3.0) return "good";
      if (value >= 2.5) return "average";
      return "at-risk";
    },
  },
};
</script>

<style scoped>
.section-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
}

.trend-controls {
  display: flex;
  gap: 12px;
}

.trend-select {
  padding: 6px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background: white;
  font-size: 0.9rem;
}

.trend-chart {
  padding: 20px;
  background: #f8fafc;
  border-radius: 8px;
}

.chart-bars {
  display: flex;
  align-items: end;
  justify-content: space-around;
  height: 200px;
  gap: 8px;
}

.trend-bar {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  max-width: 60px;
}

.bar-container {
  height: 160px;
  width: 100%;
  background: #e2e8f0;
  border-radius: 4px;
  display: flex;
  align-items: end;
  overflow: hidden;
  margin-bottom: 8px;
}

.bar-fill {
  width: 100%;
  border-radius: 4px;
  transition: height 0.3s ease;
  min-height: 4px;
}

.bar-fill.excellent {
  background: #059669;
}

.bar-fill.good {
  background: #3b82f6;
}

.bar-fill.average {
  background: #f59e0b;
}

.bar-fill.at-risk {
  background: #ef4444;
}

.bar-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 4px;
}

.bar-value {
  font-size: 0.8rem;
  color: #64748b;
}

@media (max-width: 768px) {
  .chart-bars {
    height: 150px;
  }
}
</style>
