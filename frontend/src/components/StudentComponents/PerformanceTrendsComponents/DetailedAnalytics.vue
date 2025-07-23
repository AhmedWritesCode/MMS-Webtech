<template>
  <div class="detailed-analytics">
    <div class="analytics-top-row">
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
    </div>
    <div class="analytics-grid">
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
                <component :is="getAreaIcon(area.area)" class="lucide-icon" />
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
            <div class="insight-icon">
              <component :is="getInsightIcon(insight.type)" class="lucide-icon" />
            </div>
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
import { TrendingUp, Target, FlaskConical, BookOpen, Star, Rocket, Info, AlertTriangle, CheckCircle } from 'lucide-vue-next';
export default {
  name: "DetailedAnalytics",
  components: { TrendingUp, Target, FlaskConical, BookOpen, Star, Rocket, Info, AlertTriangle, CheckCircle },
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
    getAreaIcon(area) {
      if (area.includes('Quiz')) return Target;
      if (area.includes('Lab')) return FlaskConical;
      if (area.includes('Assignment')) return BookOpen;
      return Star;
    },
    getInsightIcon(type) {
      if (type === 'positive') return CheckCircle;
      if (type === 'warning') return AlertTriangle;
      if (type === 'info') return Info;
      return Info;
    }
  },
};
</script>

<style scoped>
.detailed-analytics {
  margin-bottom: 30px;
}

.analytics-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.analytics-top-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 24px;
}

.analytics-card {
  background: rgba(255,255,255,0.92);
  border-radius: 1.5rem;
  padding: 2.2rem 1.5rem 1.5rem 1.5rem;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  backdrop-filter: blur(6px);
}

.analytics-card h3 {
  margin: 0 0 20px 0;
  font-size: 1.2rem;
  font-weight: 800;
  color: #7C9885;
}

/* Assessment Type Performance */
.assessment-types {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.type-item {
  background: rgba(181, 182, 130, 0.10);
  border-radius: 1rem;
  padding: 16px;
}

.type-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.type-name {
  font-weight: 700;
  color: #7C9885;
}

.type-average {
  font-weight: 800;
  color: #B5B682;
}

.type-progress {
  height: 10px;
  background: #e2e8f0;
  border-radius: 5px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  border-radius: 5px;
  transition: width 0.3s ease;
}

.progress-fill.excellent {
  background: linear-gradient(90deg, #7C9885 0%, #B5B682 100%);
}

.progress-fill.good {
  background: #B5B682;
}

.progress-fill.average {
  background: #e6c972;
}

.progress-fill.needs-improvement {
  background: #e74c3c;
}

.type-details {
  display: flex;
  justify-content: space-between;
  font-size: 0.9rem;
}

.type-count {
  color: #7C9885;
}

.type-trend {
  font-weight: 700;
}

.type-trend.trend-up {
  color: #7C9885;
}

.type-trend.trend-down {
  color: #e74c3c;
}

.type-trend.trend-stable {
  color: #B5B682;
}

/* Monthly Progress */
.monthly-chart {
  display: flex;
  align-items: end;
  justify-content: space-around;
  height: 200px;
  gap: 10px;
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

.month-fill.excellent {
  background: linear-gradient(90deg, #7C9885 0%, #B5B682 100%);
}

.month-fill.good {
  background: #B5B682;
}

.month-fill.average {
  background: #e6c972;
}

.month-fill.needs-improvement {
  background: #e74c3c;
}

.month-label {
  font-size: 0.9rem;
  font-weight: 700;
  color: #7C9885;
  margin-bottom: 4px;
}

.month-value {
  font-size: 0.9rem;
  color: #B5B682;
}

/* Improvement Areas */
.improvement-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.improvement-item {
  border: 1.5px solid #B5B682;
  border-radius: 1rem;
  padding: 16px;
  background: rgba(181, 182, 130, 0.10);
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
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  background: #fff;
  color: #7C9885;
  box-shadow: 0 2px 8px #B5B68222;
}

.improvement-icon.high {
  background: #ffeaea;
  color: #e74c3c;
}

.improvement-icon.medium {
  background: #fffbe6;
  color: #e6c972;
}

.improvement-icon.low {
  background: #eaf7ea;
  color: #7C9885;
}

.improvement-content h4 {
  margin: 0 0 4px 0;
  font-weight: 800;
  color: #7C9885;
}

.improvement-description {
  margin: 0;
  font-size: 1rem;
  color: #7C9885;
}

.improvement-stats {
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.stat-label {
  font-size: 0.9rem;
  color: #7C9885;
}

.stat-value {
  font-weight: 800;
  color: #23272f;
}

.stat-value.target {
  color: #B5B682;
}

.stat-value.gap {
  color: #e6c972;
}

/* Performance Insights */
.insights-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.insight-item {
  border-radius: 1rem;
  padding: 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  border-left: 4px solid;
  background: rgba(181, 182, 130, 0.10);
}

.insight-item.positive {
  border-left-color: #7C9885;
}

.insight-item.warning {
  border-left-color: #e6c972;
}

.insight-item.info {
  border-left-color: #B5B682;
}

.insight-icon {
  font-size: 1.5rem;
  margin-top: 2px;
  color: #7C9885;
}

.insight-content h4 {
  margin: 0 0 4px 0;
  font-weight: 800;
  color: #7C9885;
}

.insight-content p {
  margin: 0 0 8px 0;
  color: #7C9885;
  font-size: 1rem;
}

.insight-metrics {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.metric {
  font-size: 0.9rem;
  color: #B5B682;
}

.lucide-icon {
  width: 2.1rem;
  height: 2.1rem;
  color: #7C9885;
  background: rgba(255,255,255,0.7);
  border-radius: 50%;
  padding: 0.4rem;
  border: 2px solid #B5B682;
  box-shadow: 0 2px 8px #B5B68222;
  display: flex;
  align-items: center;
  justify-content: center;
}
.improvement-icon.high .lucide-icon {
  background: #ffeaea;
  color: #e74c3c;
  border-color: #e74c3c;
}
.improvement-icon.medium .lucide-icon {
  background: #fffbe6;
  color: #e6c972;
  border-color: #e6c972;
}
.improvement-icon.low .lucide-icon {
  background: #eaf7ea;
  color: #7C9885;
  border-color: #7C9885;
}
.insight-item.positive .lucide-icon {
  background: #eaf7ea;
  color: #7C9885;
  border-color: #7C9885;
}
.insight-item.warning .lucide-icon {
  background: #fffbe6;
  color: #e6c972;
  border-color: #e6c972;
}
.insight-item.info .lucide-icon {
  background: #eaf7ea;
  color: #7C9885;
  border-color: #7C9885;
}

@media (max-width: 1200px) {
  .analytics-grid {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  }
}

@media (max-width: 900px) {
  .analytics-top-row {
    grid-template-columns: 1fr;
  }
  .analytics-grid {
    grid-template-columns: 1fr;
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
