<template>
  <div class="dashboard-header">
    <div class="header-content">
      <div class="advisor-info">
        <h1 class="welcome-text">Welcome, {{ advisorName }}</h1>
        <p class="advisor-details">Academic Advisor • {{ department }}</p>
        <p class="advisor-id">Staff ID: {{ advisorId }}</p>
      </div>
      <div class="quick-stats">
        <div class="stat-item">
          <span class="stat-number">{{ totalAdvisees }}</span>
          <span class="stat-label">Total Advisees</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">{{ atRiskStudents }}</span>
          <span class="stat-label">At-Risk Students</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">{{ recentMeetings }}</span>
          <span class="stat-label">Recent Meetings</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdvisorDashboardHeader",
  props: {
    advisorName: {
      type: String,
      required: true,
      default: "Advisor",
    },
    advisorId: {
      type: String,
      required: true,
      default: "",
    },
    department: {
      type: String,
      required: true,
      default: "",
    },
    totalAdvisees: {
      type: Number,
      required: true,
      default: 0,
    },
    atRiskStudents: {
      type: Number,
      required: true,
      default: 0,
    },
    recentMeetings: {
      type: Number,
      required: true,
      default: 0,
    },
  },
};
</script>

<style scoped>
.dashboard-header {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  border-radius: 16px;
  padding: 30px;
  margin-bottom: 30px;
  color: white;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.welcome-text {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 8px 0;
}

.advisor-details {
  font-size: 1.1rem;
  margin: 0 0 4px 0;
  opacity: 0.9;
}

.advisor-id {
  font-size: 0.9rem;
  margin: 0;
  opacity: 0.8;
}

.quick-stats {
  display: flex;
  gap: 30px;
}

.stat-item {
  text-align: center;
}

.stat-number {
  display: block;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.9rem;
  opacity: 0.9;
}

@media (max-width: 1200px) {
  .header-content {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }

  .quick-stats {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .welcome-text {
    font-size: 2rem;
  }
}

@media (max-width: 480px) {
  .quick-stats {
    flex-direction: column;
    gap: 16px;
  }
}
</style>
