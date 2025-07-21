<template>
  <div class="quick-actions-bar">
    <button class="quick-action-btn primary" @click="$emit('schedule-meeting')">
      📅 Schedule Meeting
    </button>
    <button class="quick-action-btn secondary" @click="$emit('add-note')">
      📝 Add Note
    </button>
    <button class="quick-action-btn secondary" @click="$emit('export-report')">
      📊 Export Report
    </button>
    <button class="quick-action-btn secondary" @click="$emit('send-message')">
      💬 Send Message
    </button>
  </div>
</template>

<script>
export default {
  name: "QuickActionsBar",
  emits: ["schedule-meeting", "add-note", "export-report", "send-message"],
};
</script>

<style scoped>
.quick-actions-bar {
  display: flex;
  gap: 12px;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.quick-action-btn {
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

.quick-action-btn.primary {
  background: #3b82f6;
  color: white;
}

.quick-action-btn.primary:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.quick-action-btn.secondary {
  background: white;
  color: #475569;
  border: 2px solid #e2e8f0;
}

.quick-action-btn.secondary:hover {
  border-color: #3b82f6;
  color: #3b82f6;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .quick-actions-bar {
    flex-direction: column;
  }

  .quick-action-btn {
    justify-content: center;
  }
}
</style>
