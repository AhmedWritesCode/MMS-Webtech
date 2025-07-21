<template>
  <div class="comparison-section">
    <h3>Class Performance Comparison</h3>
    <div class="comparison-grid">
      <div class="comparison-card">
        <h4>Your Performance</h4>
        <div class="performance-bars">
          <div
            v-for="assessment in topAssessments"
            :key="assessment.id"
            class="performance-bar"
          >
            <span class="bar-label">{{ assessment.name }}</span>
            <div class="bar-container">
              <div
                class="bar-fill your-performance"
                :style="{ width: assessment.yourPercentage + '%' }"
              ></div>
              <div
                class="bar-fill class-average"
                :style="{ width: assessment.classPercentage + '%' }"
              ></div>
            </div>
            <div class="bar-values">
              <span class="your-value">
                You: {{ assessment.yourPercentage }}%
              </span>
              <span class="class-value">
                Class: {{ assessment.classPercentage }}%
              </span>
            </div>
          </div>
        </div>
        <div class="legend">
          <div class="legend-item">
            <div class="legend-color your-performance"></div>
            <span>Your Performance</span>
          </div>
          <div class="legend-item">
            <div class="legend-color class-average"></div>
            <span>Class Average</span>
          </div>
        </div>
      </div>

      <div class="stats-summary">
        <div class="summary-item">
          <h4>Above Average</h4>
          <p class="summary-value">
            {{ aboveAverageCount }}/{{ totalAssessments }}
          </p>
          <p class="summary-label">assessments</p>
        </div>

        <div class="summary-item">
          <h4>Percentile Rank</h4>
          <p class="summary-value">{{ percentileRank }}%</p>
          <p class="summary-label">of class</p>
        </div>

        <div class="summary-item">
          <h4>Improvement</h4>
          <p class="summary-value" :class="improvementClass">
            {{ improvementTrend }}
          </p>
          <p class="summary-label">from last assessment</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ClassPerformanceComparison",
  props: {
    topAssessments: {
      type: Array,
      required: true,
      default: () => [],
    },
    aboveAverageCount: {
      type: Number,
      required: true,
      default: 0,
    },
    totalAssessments: {
      type: Number,
      required: true,
      default: 0,
    },
    percentileRank: {
      type: Number,
      required: true,
      default: 0,
    },
    improvementTrend: {
      type: String,
      required: true,
      default: "N/A",
    },
    improvementClass: {
      type: String,
      required: true,
      default: "",
    },
  },
};
</script>

<style scoped>
.comparison-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.comparison-section h3 {
  margin: 0 0 24px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.comparison-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 30px;
}

.comparison-card {
  background: #f8fafc;
  border-radius: 12px;
  padding: 24px;
}

.comparison-card h4 {
  margin: 0 0 20px 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.performance-bars {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 20px;
}

.performance-bar {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.bar-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1e293b;
}

.bar-container {
  position: relative;
  height: 12px;
  background: #e2e8f0;
  border-radius: 6px;
  overflow: hidden;
}

.bar-fill {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  border-radius: 6px;
  transition: width 0.3s ease;
}

.bar-fill.your-performance {
  background: #3b82f6;
  z-index: 2;
}

.bar-fill.class-average {
  background: #94a3b8;
  z-index: 1;
}

.bar-values {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
}

.your-value {
  color: #3b82f6;
  font-weight: 600;
}

.class-value {
  color: #64748b;
  font-weight: 600;
}

.legend {
  display: flex;
  gap: 16px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.9rem;
  color: #64748b;
}

.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 2px;
}

.legend-color.your-performance {
  background: #3b82f6;
}

.legend-color.class-average {
  background: #94a3b8;
}

.stats-summary {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.summary-item {
  background: #f8fafc;
  padding: 20px;
  border-radius: 12px;
  text-align: center;
  border: 2px solid #e2e8f0;
}

.summary-item h4 {
  margin: 0 0 12px 0;
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 600;
}

.summary-value {
  margin: 0 0 4px 0;
  font-size: 1.8rem;
  font-weight: 700;
  color: #1e293b;
}

.summary-value.improvement-positive {
  color: #10b981;
}

.summary-value.improvement-negative {
  color: #ef4444;
}

.summary-label {
  margin: 0;
  font-size: 0.8rem;
  color: #64748b;
}

@media (max-width: 1200px) {
  .comparison-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .stats-summary {
    grid-template-columns: 1fr;
  }
}
</style>
