<template>
  <div class="assessment-comparison">
    <div class="section-header">
      <h3>Assessment-wise Comparison</h3>
      <div class="legend">
        <div class="legend-item">
          <div class="legend-color your-score"></div>
          <span>Your Score</span>
        </div>
        <div class="legend-item">
          <div class="legend-color class-average"></div>
          <span>Class Average</span>
        </div>
        <div class="legend-item">
          <div class="legend-color highest-score"></div>
          <span>Highest Score</span>
        </div>
      </div>
    </div>

    <div class="assessment-bars">
      <div
        v-for="assessment in assessments"
        :key="assessment.id"
        class="assessment-bar-item"
      >
        <div class="assessment-info">
          <h4>{{ assessment.name }}</h4>
          <span class="assessment-type">{{
            assessment.type.toUpperCase()
          }}</span>
          <span class="assessment-weight">{{ assessment.weight }}%</span>
        </div>

        <div class="bars-container">
          <div class="bar-group">
            <div class="bar-label">Your Score</div>
            <div class="bar-wrapper">
              <div
                class="bar your-score"
                :style="{
                  width:
                    getBarWidth(assessment.yourScore, assessment.maxScore) +
                    '%',
                }"
              ></div>
              <span class="bar-value"
                >{{ assessment.yourScore }}/{{ assessment.maxScore }}</span
              >
            </div>
          </div>

          <div class="bar-group">
            <div class="bar-label">Class Average</div>
            <div class="bar-wrapper">
              <div
                class="bar class-average"
                :style="{
                  width:
                    getBarWidth(assessment.classAverage, assessment.maxScore) +
                    '%',
                }"
              ></div>
              <span class="bar-value"
                >{{ assessment.classAverage.toFixed(1) }}/{{
                  assessment.maxScore
                }}</span
              >
            </div>
          </div>

          <div class="bar-group">
            <div class="bar-label">Highest Score</div>
            <div class="bar-wrapper">
              <div
                class="bar highest-score"
                :style="{
                  width:
                    getBarWidth(assessment.highestScore, assessment.maxScore) +
                    '%',
                }"
              ></div>
              <span class="bar-value"
                >{{ assessment.highestScore }}/{{ assessment.maxScore }}</span
              >
            </div>
          </div>
        </div>

        <div class="assessment-stats">
          <div class="stat">
            <span class="stat-label">Your Percentile:</span>
            <span class="stat-value">{{ assessment.yourPercentile }}%</span>
          </div>
          <div class="stat">
            <span class="stat-label">Above Average:</span>
            <span
              class="stat-value"
              :class="
                assessment.yourScore > assessment.classAverage
                  ? 'positive'
                  : 'negative'
              "
            >
              {{
                assessment.yourScore > assessment.classAverage ? "Yes" : "No"
              }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AssessmentComparison",
  props: {
    assessments: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  methods: {
    getBarWidth(score, maxScore) {
      return (score / maxScore) * 100;
    },
  },
};
</script>

<style scoped>
.assessment-comparison {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.section-header h3 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.legend {
  display: flex;
  gap: 20px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  color: #64748b;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

.legend-color.your-score {
  background: #3b82f6;
}

.legend-color.class-average {
  background: #10b981;
}

.legend-color.highest-score {
  background: #f59e0b;
}

.assessment-bars {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.assessment-bar-item {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 24px;
  background: #f8fafc;
}

.assessment-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 12px;
}

.assessment-info h4 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

.assessment-type {
  background: #e2e8f0;
  color: #475569;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.7rem;
  font-weight: 600;
}

.assessment-weight {
  background: #3b82f6;
  color: white;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 700;
}

.bars-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.bar-group {
  display: flex;
  align-items: center;
  gap: 12px;
}

.bar-label {
  min-width: 100px;
  font-size: 0.9rem;
  font-weight: 600;
  color: #64748b;
}

.bar-wrapper {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 12px;
}

.bar {
  height: 12px;
  border-radius: 6px;
  transition: width 0.3s ease;
  min-width: 4px;
}

.bar.your-score {
  background: #3b82f6;
}

.bar.class-average {
  background: #10b981;
}

.bar.highest-score {
  background: #f59e0b;
}

.bar-value {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1e293b;
  min-width: 60px;
}

.assessment-stats {
  display: flex;
  gap: 24px;
  padding-top: 16px;
  border-top: 1px solid #e2e8f0;
}

.stat {
  display: flex;
  gap: 8px;
  align-items: center;
}

.stat-label {
  font-size: 0.9rem;
  color: #64748b;
}

.stat-value {
  font-weight: 700;
  font-size: 0.9rem;
}

.stat-value.positive {
  color: #10b981;
}

.stat-value.negative {
  color: #ef4444;
}

@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }

  .legend {
    flex-wrap: wrap;
  }

  .assessment-info {
    flex-direction: column;
    align-items: flex-start;
  }

  .assessment-stats {
    flex-direction: column;
    gap: 12px;
  }

  .bar-group {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .bar-wrapper {
    width: 100%;
  }
}
</style>
