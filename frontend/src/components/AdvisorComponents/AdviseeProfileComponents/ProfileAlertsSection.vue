<template>
  <div v-if="alerts && alerts.length > 0" class="alerts-section">
    <div
      v-for="alert in alerts"
      :key="alert.id"
      :class="['alert-banner', alert.type]"
    >
      <div class="alert-icon">{{ getAlertIcon(alert.type) }}</div>
      <div class="alert-content">
        <h4>{{ alert.title }}</h4>
        <p>{{ alert.message }}</p>
      </div>
      <div class="alert-actions">
        <button
          class="alert-action-btn"
          @click="$emit('handle-alert', alert.id)"
        >
          {{ alert.actionText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProfileAlertsSection",
  props: {
    alerts: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: ["handle-alert"],
  methods: {
    getAlertIcon(type) {
      const icons = {
        warning: "⚠️",
        info: "ℹ️",
        success: "✅",
        error: "❌",
      };
      return icons[type] || "ℹ️";
    },
  },
};
</script>

<style scoped>
.alerts-section {
  margin-bottom: 20px;
}

.alert-banner {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 20px;
  border-radius: 12px;
  margin-bottom: 12px;
  border-left: 4px solid;
}

.alert-banner.warning {
  background: #fef3c7;
  border-left-color: #f59e0b;
}

.alert-banner.info {
  background: #dbeafe;
  border-left-color: #3b82f6;
}

.alert-banner.success {
  background: #dcfce7;
  border-left-color: #10b981;
}

.alert-banner.error {
  background: #fee2e2;
  border-left-color: #ef4444;
}

.alert-icon {
  font-size: 1.5rem;
}

.alert-content {
  flex: 1;
}

.alert-content h4 {
  margin: 0 0 4px 0;
  font-weight: 700;
  color: #1e293b;
}

.alert-content p {
  margin: 0;
  color: #64748b;
}

.alert-action-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.alert-action-btn:hover {
  background: #2563eb;
}
</style>
