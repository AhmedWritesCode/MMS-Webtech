<template>
  <div v-if="show" class="notification" :class="type">
    <div class="notification-content">
      <span class="notification-icon">{{ getIcon() }}</span>
      <span class="notification-message">{{ message }}</span>
      <button class="notification-close" @click="$emit('close')">×</button>
    </div>
  </div>
</template>

<script>
export default {
  name: "NotificationToast",
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    message: {
      type: String,
      default: "",
    },
    type: {
      type: String,
      default: "info",
      validator: (value) =>
        ["success", "error", "info", "warning"].includes(value),
    },
  },
  emits: ["close"],
  methods: {
    getIcon() {
      const icons = {
        success: "✓",
        error: "✕",
        info: "ℹ",
        warning: "⚠",
      };
      return icons[this.type] || "ℹ";
    },
  },
};
</script>

<style scoped>
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 2000;
  min-width: 300px;
  max-width: 400px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  animation: slideIn 0.3s ease-out;
}

.notification.success {
  background: #dcfce7;
  border: 1px solid #22c55e;
  color: #166534;
}

.notification.error {
  background: #fee2e2;
  border: 1px solid #ef4444;
  color: #dc2626;
}

.notification.info {
  background: #dbeafe;
  border: 1px solid #3b82f6;
  color: #1d4ed8;
}

.notification.warning {
  background: #fef3c7;
  border: 1px solid #f59e0b;
  color: #d97706;
}

.notification-content {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  gap: 8px;
}

.notification-icon {
  font-size: 1.2rem;
  font-weight: bold;
  flex-shrink: 0;
}

.notification-message {
  flex: 1;
  font-size: 0.9rem;
  font-weight: 500;
}

.notification-close {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 0;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.notification-close:hover {
  background: rgba(0, 0, 0, 0.1);
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .notification {
    top: 10px;
    right: 10px;
    left: 10px;
    min-width: auto;
    max-width: none;
  }
}
</style>
