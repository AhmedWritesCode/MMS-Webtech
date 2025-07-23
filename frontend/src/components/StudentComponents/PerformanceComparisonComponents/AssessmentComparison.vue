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
  background: rgba(255,255,255,0.97);
  border-radius: 1.2rem;
  padding: 2.5rem 2rem;
  margin-bottom: 2.5rem;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  color: #23272f;
  font-family: 'Segoe UI', 'Arial', sans-serif;
  border: 2px solid #B5B682;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 36px;
  border-bottom: 2px solid #B5B682;
  padding-bottom: 18px;
}

.section-header h3 {
  margin: 0;
  font-size: 2rem;
  font-weight: 800;
  color: #7C9885;
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
  color: #7C9885;
}

.legend-color {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #B5B682;
  box-shadow: 0 0 6px #B5B68255;
}

.legend-color.your-score {
  background: linear-gradient(90deg, #7C9885 60%, #B5B682 100%);
}

.legend-color.class-average {
  background: linear-gradient(90deg, #B5B682 60%, #7C9885 100%);
}

.legend-color.highest-score {
  background: linear-gradient(90deg, #23272f 60%, #7C9885 100%);
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
  border: 2px solid #B5B682;
  border-radius: 1.2rem;
  padding: 2rem 1.5rem;
  background: #f9f9e0;
  box-shadow: 0 2px 12px #B5B68222;
  transition: box-shadow 0.2s;
}

.assessment-bar-item:hover {
  box-shadow: 0 4px 24px #7C9885aa;
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
  color: #7C9885;
  letter-spacing: 0.5px;
}

.assessment-type {
  background: #7C9885;
  color: #fff;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 1px;
  box-shadow: 0 1px 4px #7C988533;
}

.assessment-weight {
  background: #fff;
  color: #B5B682;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 700;
  border: 1px solid #B5B682;
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
  color: #B5B682;
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
  box-shadow: 0 1px 8px #B5B68255;
}

.bar.your-score {
  background: linear-gradient(90deg, #7C9885 70%, #B5B682 100%);
}

.bar.class-average {
  background: linear-gradient(90deg, #B5B682 60%, #7C9885 100%);
}

.bar.highest-score {
  background: linear-gradient(90deg, #23272f 60%, #7C9885 100%);
}

.bar-value {
  font-size: 1rem;
  font-weight: 700;
  color: #7C9885;
  min-width: 70px;
  letter-spacing: 0.5px;
}

.assessment-stats {
  display: flex;
  gap: 32px;
  padding-top: 18px;
  border-top: 1.5px dashed #B5B682;
}

.stat {
  display: flex;
  gap: 10px;
  align-items: center;
}

.stat-label {
  font-size: 1rem;
  color: #B5B682;
  letter-spacing: 0.5px;
}

.stat-value {
  font-weight: 800;
  font-size: 1rem;
  letter-spacing: 0.5px;
}

.stat-value.positive {
  color: #7C9885;
  text-shadow: 0 0 4px #7C988555;
}

.stat-value.negative {
  color: #e74c3c;
  text-shadow: 0 0 4px #e74c3c55;
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
