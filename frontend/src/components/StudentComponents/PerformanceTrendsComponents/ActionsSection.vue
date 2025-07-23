<template>
  <div class="actions-section">
    <div class="actions-card">
      <h3>Export & Actions</h3>
      <div class="action-buttons">
        <button class="action-btn primary" @click="exportTrendsReport">
          📊 Export Trends Report
        </button>
        <button class="action-btn secondary" @click="setPerformanceGoals">
          🎯 Set Performance Goals
        </button>
        <button class="action-btn secondary" @click="shareProgress">
          📤 Share Progress
        </button>
        <button class="action-btn secondary" @click="scheduleReview">
          📅 Schedule Review
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ActionsSection",
  emits: [
    "export-trends-report",
    "set-performance-goals",
    "share-progress",
    "schedule-review",
  ],
  methods: {
    exportTrendsReport() {
      this.$emit("export-trends-report");
    },

    setPerformanceGoals() {
      this.$emit("set-performance-goals");
    },

    shareProgress() {
      this.$emit("share-progress");
    },

    scheduleReview() {
      this.$emit("schedule-review");
    },
  },
};
</script>

<style scoped>
.actions-section {
  margin-bottom: 30px;
}

.actions-card {
  background: rgba(255,255,255,0.92);
  border-radius: 1.5rem;
  padding: 2.2rem 1.5rem 1.5rem 1.5rem;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  backdrop-filter: blur(6px);
}

.actions-card h3 {
  margin: 0 0 20px 0;
  font-size: 1.2rem;
  font-weight: 800;
  color: #7C9885;
}

.action-buttons {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
}

.action-btn {
  padding: 14px 24px;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1rem;
  box-shadow: 0 1px 4px rgba(124, 152, 133, 0.08);
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

.action-btn.secondary {
  background: #fff;
  color: #7C9885;
  border: 2px solid #B5B682;
}

.action-btn.secondary:hover {
  background: #B5B682;
  color: #fff;
  transform: translateY(-2px) scale(1.03);
}

@media (max-width: 768px) {
  .action-buttons {
    flex-direction: column;
    gap: 10px;
  }
}
</style>
