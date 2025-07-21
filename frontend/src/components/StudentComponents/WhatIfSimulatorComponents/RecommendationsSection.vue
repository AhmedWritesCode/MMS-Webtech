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
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.recommendations h2 {
  margin: 0 0 24px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.recommendations-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 20px;
}

.recommendation-card {
  border-radius: 12px;
  padding: 20px;
  border-left: 4px solid;
}

.recommendation-card.high {
  background: #fef2f2;
  border-left-color: #ef4444;
}

.recommendation-card.medium {
  background: #fffbeb;
  border-left-color: #f59e0b;
}

.recommendation-card.low {
  background: #f0fdf4;
  border-left-color: #22c55e;
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
  color: #1e293b;
}

.priority-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
}

.recommendation-card.high .priority-badge {
  background: #fee2e2;
  color: #dc2626;
}

.recommendation-card.medium .priority-badge {
  background: #fef3c7;
  color: #d97706;
}

.recommendation-card.low .priority-badge {
  background: #dcfce7;
  color: #16a34a;
}

.recommendation-content p {
  color: #64748b;
  margin-bottom: 16px;
  line-height: 1.5;
}

.recommendation-details {
  margin-bottom: 16px;
}

.recommendation-details .detail-item {
  margin-bottom: 8px;
  font-size: 0.9rem;
  color: #64748b;
}

.recommendation-actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn.primary {
  background: #3b82f6;
  color: white;
}

.action-btn.primary:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .recommendations-grid {
    grid-template-columns: 1fr;
  }
}
</style>
