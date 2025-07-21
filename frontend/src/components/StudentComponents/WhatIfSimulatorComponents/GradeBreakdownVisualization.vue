<template>
  <div class="grade-breakdown">
    <h2>Grade Breakdown Visualization</h2>
    <div class="breakdown-content">
      <div class="breakdown-chart">
        <div class="chart-container">
          <div class="grade-segments">
            <div
              v-for="segment in gradeBreakdown"
              :key="segment.name"
              class="grade-segment"
              :style="{ width: segment.weight + '%' }"
              :class="segment.status"
            >
              <div class="segment-label">{{ segment.name }}</div>
              <div class="segment-score">{{ segment.score }}%</div>
              <div class="segment-weight">{{ segment.weight }}%</div>
            </div>
          </div>
        </div>
      </div>

      <div class="breakdown-summary">
        <div class="summary-item">
          <span class="summary-label">Completed Assessments:</span>
          <span class="summary-value">{{ completedWeight }}%</span>
        </div>
        <div class="summary-item">
          <span class="summary-label">Remaining Assessments:</span>
          <span class="summary-value">{{ 100 - completedWeight }}%</span>
        </div>
        <div class="summary-item">
          <span class="summary-label">Current Contribution:</span>
          <span class="summary-value"
            >{{ currentContribution.toFixed(1) }}%</span
          >
        </div>
        <div class="summary-item">
          <span class="summary-label">Projected Contribution:</span>
          <span class="summary-value"
            >{{ projectedContribution.toFixed(1) }}%</span
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "GradeBreakdownVisualization",
  props: {
    gradeBreakdown: {
      type: Array,
      required: true,
      default: () => [],
    },
    completedWeight: {
      type: Number,
      required: true,
      default: 0,
    },
    currentContribution: {
      type: Number,
      required: true,
      default: 0,
    },
    projectedContribution: {
      type: Number,
      required: true,
      default: 0,
    },
  },
};
</script>

<style scoped>
.grade-breakdown {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.grade-breakdown h2 {
  margin: 0 0 24px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.breakdown-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 30px;
}

.chart-container {
  background: #f8fafc;
  border-radius: 12px;
  padding: 20px;
}

.grade-segments {
  display: flex;
  height: 60px;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 20px;
}

.grade-segment {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
  font-weight: 600;
  font-size: 0.8rem;
  position: relative;
  min-width: 80px;
}

.grade-segment.completed {
  background: #10b981;
}

.grade-segment.predicted {
  background: #f59e0b;
}

.segment-label {
  font-size: 0.7rem;
  margin-bottom: 2px;
}

.segment-score {
  font-size: 0.9rem;
  font-weight: 700;
}

.segment-weight {
  font-size: 0.6rem;
  opacity: 0.8;
}

.breakdown-summary {
  background: #f8fafc;
  border-radius: 12px;
  padding: 20px;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #e2e8f0;
}

.summary-item:last-child {
  border-bottom: none;
}

.summary-label {
  color: #64748b;
  font-weight: 500;
}

.summary-value {
  font-weight: 700;
  color: #1e293b;
}

@media (max-width: 1200px) {
  .breakdown-content {
    grid-template-columns: 1fr;
  }
}
</style>
