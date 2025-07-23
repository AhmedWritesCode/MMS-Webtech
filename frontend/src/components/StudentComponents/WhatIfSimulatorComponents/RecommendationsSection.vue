<template>
  <div class="recommendations">
    <h2>Personalized Recommendations</h2>
    <div class="recommendations-grid">
      <div
        v-for="recommendation in recommendations"
        :key="recommendation.id"
        class="recommendation-card"
        :class="recommendation.priority"
      >
        <div class="recommendation-header">
          <div class="recommendation-icon">{{ recommendation.icon }}</div>
          <h3>{{ recommendation.title }}</h3>
          <span class="priority-badge">{{ recommendation.priority }}</span>
        </div>
        <div class="recommendation-content">
          <p>{{ recommendation.description }}</p>
          <div class="recommendation-details">
            <div class="detail-item">
              <strong>Focus Area:</strong> {{ recommendation.focusArea }}
            </div>
            <div class="detail-item">
              <strong>Expected Impact:</strong> {{ recommendation.impact }}
            </div>
            <div class="detail-item" v-if="recommendation.timeframe">
              <strong>Timeframe:</strong> {{ recommendation.timeframe }}
            </div>
          </div>
          <div class="recommendation-actions">
            <button
              class="action-btn primary"
              @click="handleAction(recommendation)"
            >
              {{ recommendation.actionText }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "RecommendationsSection",
  props: {
    recommendations: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: ["recommendation-action"],
  methods: {
    handleAction(recommendation) {
      this.$emit("recommendation-action", recommendation);
    },
  },
};
</script>

<style scoped>
.recommendations {
  background: rgba(255,255,255,0.92);
  border-radius: 1.5rem;
  padding: 2.2rem 1.5rem 1.5rem 1.5rem;
  margin-bottom: 30px;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  backdrop-filter: blur(6px);
}

.recommendations h2 {
  margin: 0 0 24px 0;
  font-size: 1.5rem;
  font-weight: 800;
  color: #7C9885;
}

.recommendations-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 24px;
}

.recommendation-card {
  border-radius: 1.2rem;
  padding: 20px;
  border-left: 4px solid;
}

.recommendation-card.high {
  background: #ffeaea;
  border-left-color: #e74c3c;
}

.recommendation-card.medium {
  background: #fffbe6;
  border-left-color: #e6c972;
}

.recommendation-card.low {
  background: #eaf7ea;
  border-left-color: #7C9885;
}

.recommendation-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.recommendation-icon {
  font-size: 1.5rem;
}

.recommendation-header h3 {
  margin: 0;
  flex: 1;
  font-size: 1.1rem;
  font-weight: 700;
  color: #23272f;
}

.priority-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
}

.recommendation-card.high .priority-badge {
  background: #ffeaea;
  color: #e74c3c;
}

.recommendation-card.medium .priority-badge {
  background: #fffbe6;
  color: #e6c972;
}

.recommendation-card.low .priority-badge {
  background: #eaf7ea;
  color: #7C9885;
}

.recommendation-content p {
  color: #7C9885;
  margin-bottom: 16px;
  line-height: 1.5;
}

.recommendation-details {
  margin-bottom: 16px;
}

.recommendation-details .detail-item {
  margin-bottom: 8px;
  font-size: 0.98rem;
  color: #7C9885;
}

.recommendation-actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn.primary {
  background: linear-gradient(90deg, #B5B682 0%, #7C9885 100%);
  color: #23272f;
}

.action-btn.primary:hover {
  background: linear-gradient(90deg, #7C9885 0%, #B5B682 100%);
  color: #fff;
  transform: translateY(-2px) scale(1.03);
}

@media (max-width: 768px) {
  .recommendations-grid {
    grid-template-columns: 1fr;
  }
}
</style>
