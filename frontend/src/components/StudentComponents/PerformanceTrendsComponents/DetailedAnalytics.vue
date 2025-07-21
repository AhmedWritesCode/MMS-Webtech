<template>
  <div class="detailed-analytics">
    <div class="analytics-grid">
      <!-- Assessment Type Performance -->
      <div class="analytics-card">
        <h3>Performance by Assessment Type</h3>
        <div class="assessment-types">
          <div
            v-for="type in assessmentTypePerformance"
            :key="type.type"
            class="type-item"
          >
            <div class="type-header">
              <span class="type-name">{{ type.name }}</span>
              <span class="type-average">{{ type.average }}%</span>
            </div>
            <div class="type-progress">
              <div
                class="progress-fill"
                :style="{ width: type.average + '%' }"
                :class="getPerformanceClass(type.average)"
              ></div>
            </div>
            <div class="type-details">
              <span class="type-count">{{ type.count }} assessments</span>
              <span class="type-trend" :class="getTrendClass(type.trend)">
                {{ type.trend > 0 ? "+" : "" }}{{ type.trend }}%
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Monthly Progress -->
      <div class="analytics-card">
        <h3>Monthly Progress</h3>
        <div class="monthly-chart">
          <div
            v-for="month in monthlyProgress"
            :key="month.month"
            class="month-bar"
          >
            <div class="month-container">
              <div
                class="month-fill"
                :style="{ height: month.percentage + '%' }"
                :class="getPerformanceClass(month.average)"
              ></div>
            </div>
            <div class="month-label">{{ month.month }}</div>
            <div class="month-value">{{ month.average }}%</div>
          </div>
        </div>
      </div>

      <!-- Improvement Opportunities -->
      <div class="analytics-card">
        <h3>Areas for Improvement</h3>
        <div class="improvement-list">
          <div
            v-for="area in improvementAreas"
            :key="area.area"
            class="improvement-item"
          >
            <div class="improvement-header">
              <div class="improvement-icon" :class="area.priority">
                {{ area.icon }}
              </div>
              <div class="improvement-content">
                <h4>{{ area.area }}</h4>
                <p class="improvement-description">{{ area.description }}</p>
              </div>
            </div>
            <div class="improvement-stats">
              <div class="stat-item">
                <span class="stat-label">Current Avg:</span>
                <span class="stat-value">{{ area.currentAverage }}%</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Target:</span>
                <span class="stat-value target">{{ area.target }}%</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Gap:</span>
                <span class="stat-value gap"
                  >{{ area.target - area.currentAverage }}%</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Insights -->
      <div class="analytics-card">
        <h3>Performance Insights</h3>
        <div class="insights-list">
          <div
            v-for="insight in performanceInsights"
            :key="insight.id"
            class="insight-item"
            :class="insight.type"
          >
            <div class="insight-icon">{{ insight.icon }}</div>
            <div class="insight-content">
              <h4>{{ insight.title }}</h4>
              <p>{{ insight.description }}</p>
              <div class="insight-metrics" v-if="insight.metrics">
                <span
                  v-for="metric in insight.metrics"
                  :key="metric.label"
                  class="metric"
                >
                  <strong>{{ metric.label }}:</strong> {{ metric.value }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DetailedAnalytics",
  props: {
    assessmentTypePerformance: {
      type: Array,
      required: true,
      default: () => [],
    },
    monthlyProgress: {
      type: Array,
      required: true,
      default: () => [],
    },
    improvementAreas: {
      type: Array,
      required: true,
      default: () => [],
    },
    performanceInsights: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  methods: {
    getPerformanceClass(percentage) {
      if (percentage >= 85) return "excellent";
      if (percentage >= 75) return "good";
      if (percentage >= 65) return "average";
      return "needs-improvement";
    },

    getTrendClass(trend) {
      return trend > 0 ? "trend-up" : trend < 0 ? "trend-down" : "trend-stable";
    },
  },
};
</script>

<style scoped>
.detailed-analytics {
  margin-bottom: 30px;
}

.analytics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 20px;
}

.analytics-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.analytics-card h3 {
  margin: 0 0 20px 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

/* Assessment Type Performance */
.assessment-types {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.type-item {
  background: #f8fafc;
  border-radius: 8px;
  padding: 16px;
}

.type-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.type-name {
  font-weight: 600;
  color: #1e293b;
}

.type-average {
  font-weight: 700;
  color: #3b82f6;
}

.type-progress {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-fill.excellent {
  background: #10b981;
}

.progress-fill.good {
  background: #3b82f6;
}

.progress-fill.average {
  background: #f59e0b;
}

.progress-fill.needs-improvement {
  background: #ef4444;
}

.type-details {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
}

.type-count {
  color: #64748b;
}

.type-trend {
  font-weight: 600;
}

.type-trend.trend-up {
  color: #10b981;
}

.type-trend.trend-down {
  color: #ef4444;
}

.type-trend.trend-stable {
  color: #64748b;
}

/* Monthly Progress */
.monthly-chart {
  display: flex;
  align-items: end;
  justify-content: space-around;
  height: 200px;
  gap: 8px;
  padding: 0 10px;
}

.month-bar {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
}

.month-container {
  height: 150px;
  width: 30px;
  background: #e2e8f0;
  border-radius: 15px;
  display: flex;
  align-items: end;
  overflow: hidden;
  margin-bottom: 8px;
}

.month-fill {
  width: 100%;
  border-radius: 15px;
  transition: height 0.3s ease;
  min-height: 4px;
}

.month-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 4px;
}

.month-value {
  font-size: 0.8rem;
  color: #64748b;
}

/* Improvement Areas */
.improvement-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.improvement-item {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
  background: #f8fafc;
}

.improvement-header {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 12px;
}

.improvement-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.improvement-icon.high {
  background: #fee2e2;
  color: #dc2626;
}

.improvement-icon.medium {
  background: #fef3c7;
  color: #d97706;
}

.improvement-icon.low {
  background: #dcfce7;
  color: #16a34a;
}

.improvement-content h4 {
  margin: 0 0 4px 0;
  font-weight: 700;
  color: #1e293b;
}

.improvement-description {
  margin: 0;
  font-size: 0.9rem;
  color: #64748b;
}

.improvement-stats {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.stat-label {
  font-size: 0.8rem;
  color: #64748b;
}

.stat-value {
  font-weight: 700;
  color: #1e293b;
}

.stat-value.target {
  color: #10b981;
}

.stat-value.gap {
  color: #f59e0b;
}

/* Performance Insights */
.insights-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.insight-item {
  border-radius: 8px;
  padding: 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  border-left: 4px solid;
}

.insight-item.positive {
  background: #f0fdf4;
  border-left-color: #10b981;
}

.insight-item.warning {
  background: #fffbeb;
  border-left-color: #f59e0b;
}

.insight-item.info {
  background: #eff6ff;
  border-left-color: #3b82f6;
}

.insight-icon {
  font-size: 1.5rem;
  margin-top: 2px;
}

.insight-content h4 {
  margin: 0 0 4px 0;
  font-weight: 700;
  color: #1e293b;
}

.insight-content p {
  margin: 0 0 8px 0;
  color: #64748b;
  font-size: 0.9rem;
}

.insight-metrics {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.metric {
  font-size: 0.8rem;
  color: #64748b;
}

@media (max-width: 1200px) {
  .analytics-grid {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  }
}

@media (max-width: 768px) {
  .analytics-grid {
    grid-template-columns: 1fr;
  }

  .improvement-stats {
    flex-direction: column;
    gap: 8px;
  }

  .insight-metrics {
    flex-direction: column;
    gap: 4px;
  }
}
</style>
