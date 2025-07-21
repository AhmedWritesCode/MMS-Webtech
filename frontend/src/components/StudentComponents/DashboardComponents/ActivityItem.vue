<template>
  <div class="activity-item">
    <div class="activity-icon" :class="activity.type">
      {{ getActivityIcon(activity.type) }}
    </div>
    <div class="activity-content">
      <h4>{{ activity.title }}</h4>
      <p>{{ activity.description }}</p>
      <span class="activity-time">{{ formatTime(activity.timestamp) }}</span>
    </div>
    <div class="activity-action" v-if="activity.actionRequired">
      <button class="action-btn" @click="$emit('activity-action', activity)">
        {{ activity.actionText }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "ActivityItem",
  props: {
    activity: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  emits: ["activity-action"],
  methods: {
    getActivityIcon(type) {
      const icons = {
        grade_update: "📊",
        reminder: "⏰",
        remark_update: "✅",
        announcement: "📢",
      };
      return icons[type] || "📝";
    },

    formatTime(timestamp) {
      const now = new Date();
      const diff = now - timestamp;
      const hours = Math.floor(diff / (1000 * 60 * 60));
      const days = Math.floor(hours / 24);

      if (days > 0) return `${days} day${days > 1 ? "s" : ""} ago`;
      if (hours > 0) return `${hours} hour${hours > 1 ? "s" : ""} ago`;
      return "Just now";
    },
  },
};
</script>

<style scoped>
.activity-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  border-bottom: 1px solid #e2e8f0;
}

.activity-item:last-child {
  border-bottom: none;
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  background: #f1f5f9;
}

.activity-content {
  flex: 1;
}

.activity-content h4 {
  margin: 0 0 4px 0;
  font-weight: 600;
  color: #1e293b;
}

.activity-content p {
  margin: 0 0 4px 0;
  color: #64748b;
  font-size: 0.9rem;
}

.activity-time {
  font-size: 0.8rem;
  color: #94a3b8;
}

.action-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.action-btn:hover {
  background: #2563eb;
}
</style>
