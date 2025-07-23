<template>
  <div class="assessment-simulator">
    <div class="simulator-header-section">
      <h2>Assessment Score Simulator</h2>
      <!-- Removed simulator-controls buttons as requested -->
    </div>

    <div class="assessments-grid">
      <div
        v-for="assessment in assessments"
        :key="assessment.id"
        class="assessment-card"
        :class="{
          completed: assessment.completed,
          pending: !assessment.completed,
        }"
      >
        <div class="assessment-header">
          <div class="assessment-info">
            <h4>{{ assessment.name }}</h4>
            <span class="assessment-type">{{
              assessment.type.toUpperCase()
            }}</span>
          </div>
          <div class="assessment-weight">{{ assessment.weight }}%</div>
        </div>

        <div class="assessment-content">
          <div v-if="assessment.completed" class="completed-assessment">
            <div class="actual-score">
              <span class="score-label">Actual Score:</span>
              <span class="score-value"
                >{{ assessment.actualScore }}/{{ assessment.maxScore }}</span
              >
              <span class="score-percentage"
                >({{
                  Math.round(
                    (assessment.actualScore / assessment.maxScore) * 100
                  )
                }}%)</span
              >
            </div>
            <div class="score-impact">
              <span class="impact-label">Grade Impact:</span>
              <span class="impact-value"
                >{{
                  (
                    (assessment.actualScore / assessment.maxScore) *
                    assessment.weight
                  ).toFixed(1)
                }}%</span
              >
            </div>
          </div>

          <div v-else class="pending-assessment">
            <div class="score-input-section">
              <label class="input-label">Predicted Score:</label>
              <div class="score-input-group">
                <input
                  type="number"
                  v-model.number="assessment.predictedScore"
                  :max="assessment.maxScore"
                  :min="0"
                  class="score-input"
                  @input="updateAssessment(assessment)"
                />
                <span class="max-score">/ {{ assessment.maxScore }}</span>
                <div class="percentage-display">
                  {{
                    Math.round(
                      (assessment.predictedScore / assessment.maxScore) * 100
                    )
                  }}%
                </div>
              </div>
            </div>

            <div class="score-slider">
              <input
                type="range"
                v-model.number="assessment.predictedScore"
                :max="assessment.maxScore"
                :min="0"
                :step="1"
                class="slider"
                @input="updateAssessment(assessment)"
              />
              <div class="slider-labels">
                <span>0</span>
                <span>{{ Math.round(assessment.maxScore * 0.5) }}</span>
                <span>{{ assessment.maxScore }}</span>
              </div>
            </div>

            <div class="predicted-impact">
              <span class="impact-label">Projected Impact:</span>
              <span class="impact-value"
                >{{
                  (
                    (assessment.predictedScore / assessment.maxScore) *
                    assessment.weight
                  ).toFixed(1)
                }}%</span
              >
            </div>

            <div class="quick-scores">
              <button
                v-for="quickScore in getQuickScores(assessment.maxScore)"
                :key="quickScore.label"
                class="quick-score-btn"
                :class="{
                  active: assessment.predictedScore === quickScore.score,
                }"
                @click="setQuickScore(assessment, quickScore.score)"
              >
                {{ quickScore.label }}
              </button>
            </div>
          </div>

          <div class="assessment-details">
            <div class="detail-item" v-if="assessment.dueDate">
              <span class="detail-icon">📅</span>
              <span class="detail-text"
                >Due: {{ formatDate(assessment.dueDate) }}</span
              >
            </div>
            <div class="detail-item" v-if="assessment.description">
              <span class="detail-icon">📝</span>
              <span class="detail-text">{{ assessment.description }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AssessmentSimulator",
  props: {
    assessments: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: [
    "update-projections",
    "reset-to-actual",
    "fill-optimistic",
    "fill-realistic",
    "fill-conservative",
  ],
  methods: {
    updateAssessment() {
      this.$emit("update-projections");
    },

    resetToActual() {
      this.$emit("reset-to-actual");
    },

    fillOptimistic() {
      this.$emit("fill-optimistic");
    },

    fillRealistic() {
      this.$emit("fill-realistic");
    },

    fillConservative() {
      this.$emit("fill-conservative");
    },

    setQuickScore(assessment, score) {
      assessment.predictedScore = score;
      this.updateAssessment(assessment);
    },

    getQuickScores(maxScore) {
      return [
        { label: "90%", score: Math.round(maxScore * 0.9) },
        { label: "80%", score: Math.round(maxScore * 0.8) },
        { label: "70%", score: Math.round(maxScore * 0.7) },
        { label: "60%", score: Math.round(maxScore * 0.6) },
        { label: "50%", score: Math.round(maxScore * 0.5) },
      ];
    },

    formatDate(dateString) {
      if (!dateString) return "";
      return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },
  },
};
</script>

<style scoped>
.assessment-simulator {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.simulator-header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 16px;
}

.simulator-header-section h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.simulator-controls {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.control-btn {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.control-btn:hover {
  border-color: #3b82f6;
  color: #3b82f6;
  background: #eff6ff;
}

.assessments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 20px;
}

.assessment-card {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 20px;
  background: #f8fafc;
}

.assessment-card.completed {
  border-left: 4px solid #10b981;
  background: #f0fdf4;
}

.assessment-card.pending {
  border-left: 4px solid #f59e0b;
  background: #fffbeb;
}

.assessment-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}

.assessment-info h4 {
  margin: 0 0 4px 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.assessment-type {
  background: #e2e8f0;
  color: #475569;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: 600;
}

.assessment-weight {
  background: #3b82f6;
  color: white;
  padding: 6px 10px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.9rem;
}

/* Completed Assessment */
.completed-assessment {
  margin-bottom: 16px;
}

.actual-score {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.score-label {
  font-weight: 600;
  color: #64748b;
}

.score-value {
  font-weight: 700;
  color: #1e293b;
  font-size: 1.1rem;
}

.score-percentage {
  color: #10b981;
  font-weight: 600;
}

.score-impact,
.predicted-impact {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 12px;
  background: rgba(59, 130, 246, 0.1);
  border-radius: 6px;
  font-size: 0.9rem;
}

.impact-label {
  color: #64748b;
  font-weight: 600;
}

.impact-value {
  color: #3b82f6;
  font-weight: 700;
}

/* Pending Assessment */
.pending-assessment {
  margin-bottom: 16px;
}

.score-input-section {
  margin-bottom: 16px;
}

.input-label {
  display: block;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 8px;
}

.score-input-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.score-input {
  width: 80px;
  padding: 8px;
  border: 2px solid #e2e8f0;
  border-radius: 6px;
  font-weight: 700;
  text-align: center;
}

.score-input:focus {
  outline: none;
  border-color: #3b82f6;
}

.max-score {
  color: #64748b;
  font-weight: 600;
}

.percentage-display {
  background: #3b82f6;
  color: white;
  padding: 6px 10px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.9rem;
}

.score-slider {
  margin-bottom: 16px;
}

.slider {
  width: 100%;
  height: 6px;
  border-radius: 3px;
  background: #e2e8f0;
  outline: none;
  -webkit-appearance: none;
}

.slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
}

.slider::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
  border: none;
}

.slider-labels {
  display: flex;
  justify-content: space-between;
  margin-top: 4px;
  font-size: 0.8rem;
  color: #64748b;
}

.quick-scores {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.quick-score-btn {
  padding: 4px 8px;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.quick-score-btn:hover,
.quick-score-btn.active {
  border-color: #3b82f6;
  color: #3b82f6;
  background: #eff6ff;
}

.assessment-details {
  border-top: 1px solid #e2e8f0;
  padding-top: 12px;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 4px;
  font-size: 0.8rem;
  color: #64748b;
}

.detail-icon {
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .simulator-header-section {
    flex-direction: column;
    align-items: flex-start;
  }

  .assessments-grid {
    grid-template-columns: 1fr;
  }
}
</style>
