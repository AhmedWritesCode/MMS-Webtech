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
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.actions-card h3 {
  margin: 0 0 20px 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

.action-buttons {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.action-btn {
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.action-btn.primary {
  background: #3b82f6;
  color: white;
}

.action-btn.primary:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.action-btn.secondary {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.action-btn.secondary:hover {
  background: #e2e8f0;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .action-buttons {
    flex-direction: column;
  }
}
</style>
