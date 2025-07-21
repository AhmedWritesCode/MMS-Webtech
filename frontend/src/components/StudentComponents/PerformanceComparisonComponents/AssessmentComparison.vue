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
  background: #101010;
  border-radius: 18px;
  padding: 36px;
  margin-bottom: 36px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.35);
  color: #e6ffe6;
  font-family: 'Segoe UI', 'Arial', sans-serif;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 36px;
  border-bottom: 2px solid #1db954;
  padding-bottom: 18px;
}

.section-header h3 {
  margin: 0;
  font-size: 2rem;
  font-weight: 800;
  color: #1db954;
  letter-spacing: 1px;
}

.legend {
  display: flex;
  gap: 28px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1rem;
  color: #b6ffb6;
}

.legend-color {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #1db954;
  box-shadow: 0 0 6px #1db95455;
}

.legend-color.your-score {
  background: linear-gradient(90deg, #1db954 60%, #0a3d0a 100%);
}

.legend-color.class-average {
  background: linear-gradient(90deg, #0a3d0a 60%, #1db954 100%);
}

.legend-color.highest-score {
  background: linear-gradient(90deg, #222 60%, #1db954 100%);
}

.assessment-bars {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 36px;
}

@media (max-width: 900px) {
  .assessment-bars {
    grid-template-columns: 1fr;
  }
}

.assessment-bar-item {
  border: 2px solid #1db954;
  border-radius: 18px;
  padding: 28px;
  background: #181818;
  box-shadow: 0 2px 12px #1db95422;
  transition: box-shadow 0.2s;
}

.assessment-bar-item:hover {
  box-shadow: 0 4px 24px #1db95455;
}

.assessment-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.assessment-info h4 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #e6ffe6;
  letter-spacing: 0.5px;
}

.assessment-type {
  background: #1db954;
  color: #101010;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 1px;
  box-shadow: 0 1px 4px #1db95433;
}

.assessment-weight {
  background: #101010;
  color: #1db954;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 700;
  border: 1px solid #1db954;
  letter-spacing: 1px;
}

.bars-container {
  display: flex;
  flex-direction: column;
  gap: 18px;
  margin-bottom: 20px;
}

.bar-group {
  display: flex;
  align-items: center;
  gap: 18px;
}

.bar-label {
  min-width: 120px;
  font-size: 1rem;
  font-weight: 700;
  color: #b6ffb6;
  letter-spacing: 0.5px;
}

.bar-wrapper {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 14px;
}

.bar {
  height: 18px;
  border-radius: 9px;
  transition: width 0.3s cubic-bezier(.4,2,.6,1);
  min-width: 6px;
  box-shadow: 0 1px 8px #1db95455;
}

.bar.your-score {
  background: linear-gradient(90deg, #1db954 70%, #0a3d0a 100%);
}

.bar.class-average {
  background: linear-gradient(90deg, #0a3d0a 60%, #1db954 100%);
}

.bar.highest-score {
  background: linear-gradient(90deg, #222 60%, #1db954 100%);
}

.bar-value {
  font-size: 1rem;
  font-weight: 700;
  color: #e6ffe6;
  min-width: 70px;
  letter-spacing: 0.5px;
}

.assessment-stats {
  display: flex;
  gap: 32px;
  padding-top: 18px;
  border-top: 1.5px dashed #1db954;
}

.stat {
  display: flex;
  gap: 10px;
  align-items: center;
}

.stat-label {
  font-size: 1rem;
  color: #b6ffb6;
  letter-spacing: 0.5px;
}

.stat-value {
  font-weight: 800;
  font-size: 1rem;
  letter-spacing: 0.5px;
}

.stat-value.positive {
  color: #1db954;
  text-shadow: 0 0 4px #1db95455;
}

.stat-value.negative {
  color: #ff4444;
  text-shadow: 0 0 4px #ff444455;
}

@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    gap: 20px;
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
    gap: 16px;
  }

  .bar-group {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .bar-wrapper {
    width: 100%;
  }
}
</style>
