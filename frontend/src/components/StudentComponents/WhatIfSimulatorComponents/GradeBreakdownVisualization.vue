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
  background: rgba(255,255,255,0.92);
  border-radius: 1.5rem;
  padding: 2.2rem 1.5rem 1.5rem 1.5rem;
  margin-bottom: 30px;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  backdrop-filter: blur(6px);
}

.grade-breakdown h2 {
  margin: 0 0 24px 0;
  font-size: 1.5rem;
  font-weight: 800;
  color: #7C9885;
}

.breakdown-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 30px;
}

.chart-container {
  background: rgba(181, 182, 130, 0.10);
  border-radius: 1.2rem;
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
  background: #7C9885;
}

.grade-segment.predicted {
  background: #B5B682;
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
  background: rgba(181, 182, 130, 0.10);
  border-radius: 1.2rem;
  padding: 20px;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1.5px solid #B5B682;
}

.summary-item:last-child {
  border-bottom: none;
}

.summary-label {
  color: #7C9885;
  font-weight: 500;
}

.summary-value {
  font-weight: 700;
  color: #23272f;
}

@media (max-width: 1200px) {
  .breakdown-content {
    grid-template-columns: 1fr;
  }
}
</style>
