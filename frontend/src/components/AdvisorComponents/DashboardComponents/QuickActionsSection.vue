<template>
  <div class="quick-actions-section">
    <h2>Quick Actions</h2>
    <div class="actions-grid">
  

      <button class="action-card" @click="exportStudentData">
        <div class="action-icon">📤</div>
        <div class="action-content">
          <h3>Export Student Data</h3>
          <p>Download student performance data</p>
        </div>
      </button>

      <button class="action-card" @click="viewAnalytics">
        <div class="action-icon">📈</div>
        <div class="action-content">
          <h3>Performance Analytics</h3>
          <p>Detailed insights and trends</p>
        </div>
      </button>

      <button class="action-card" @click="manageNotifications">
        <div class="action-icon">🔔</div>
        <div class="action-content">
          <h3>Notification Settings</h3>
          <p>Configure alerts and reminders</p>
        </div>
      </button>

    </div>
  </div>
</template>

<script>
export default {
  name: "QuickActionsSection",
  emits: [
    "generate-progress-reports",
    "schedule-group-meeting",
    "export-student-data",
    "view-analytics",
    "manage-notifications",
    "access-resources",
  ],
  methods: {
    generateProgressReports() {
      this.$emit("generate-progress-reports");
    },

    scheduleGroupMeeting() {
      this.$emit("schedule-group-meeting");
    },

    exportStudentData() {
      this.$emit("export-student-data");
    },

    viewAnalytics() {
      this.$emit("view-analytics");
    },

    manageNotifications() {
      this.$emit("manage-notifications");
    },

    accessResources() {
      this.$emit("access-resources");
    },
  },
};
</script>

<style scoped>
.quick-actions-section {
  margin-bottom: 30px;
  background-color: #ffffff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.quick-actions-section h2 {
  margin: 0 0 20px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #4f46e5;
  padding-bottom: 16px;
  border-bottom: 1px solid #f1f5f9;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 16px;
}

.action-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  cursor: pointer;
  transition: all 0.3s;
  text-align: left;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.action-card:hover {
  border-color: #4f46e5;
  transform: translateY(-4px);
  box-shadow: 0 10px 15px rgba(79, 70, 229, 0.15);
}

.action-icon {
  font-size: 2rem;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(79, 70, 229, 0.1);
  color: #4f46e5;
  border-radius: 10px;
  transition: all 0.3s;
}

.action-card:hover .action-icon {
  transform: scale(1.1);
}

.action-content h3 {
  margin: 0 0 4px 0;
  font-weight: 600;
  color: #1e293b;
  transition: color 0.3s;
}

.action-card:hover .action-content h3 {
  color: #4f46e5;
}

.action-content p {
  margin: 0;
  color: #64748b;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
