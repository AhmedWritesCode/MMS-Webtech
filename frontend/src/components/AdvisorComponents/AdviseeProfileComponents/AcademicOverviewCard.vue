<template>
  <div class="section-card academic-overview">
    <div class="section-header">
      <h3>Academic Overview</h3>
      <div class="section-icon">🎓</div>
    </div>
    <div class="overview-stats">
      <div class="stat-group">
        <div class="stat-item">
          <span class="stat-label">Cumulative GPA</span>
          <span
            class="stat-value"
            :class="getPerformanceClass(student.cumulativeGPA)"
          >
            {{ student.cumulativeGPA }}
          </span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Current Semester GPA</span>
          <span
            class="stat-value"
            :class="getPerformanceClass(student.currentGPA)"
          >
            {{ student.currentGPA }}
          </span>
        </div>
      </div>
      <div class="stat-group">
        <div class="stat-item">
          <span class="stat-label">Total Credit Hours</span>
          <span class="stat-value">{{ student.totalCreditHours }}</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Current Load</span>
          <span class="stat-value"
            >{{ student.currentCreditLoad }} credits</span
          >
        </div>
      </div>
      <div class="stat-group">
        <div class="stat-item">
          <span class="stat-label">Academic Standing</span>
          <span
            class="stat-value"
            :class="getStandingClass(student.academicStanding)"
          >
            {{ student.academicStanding }}
          </span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Expected Graduation</span>
          <span class="stat-value">{{ student.expectedGraduation }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AcademicOverviewCard",
  props: {
    student: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  methods: {
    getPerformanceClass(value) {
      if (typeof value === "string") return "in-progress";
      if (value >= 3.5) return "excellent";
      if (value >= 3.0) return "good";
      if (value >= 2.5) return "average";
      return "at-risk";
    },

    getStandingClass(standing) {
      const standingMap = {
        "Excellent Standing": "excellent",
        "Good Standing": "good",
        Probation: "at-risk",
        "In Progress": "in-progress",
        Completed: "good",
      };
      return standingMap[standing] || "average";
    },
  },
};
</script>

<style scoped>
.section-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
}

.section-icon {
  font-size: 1.5rem;
  opacity: 0.7;
}

.overview-stats {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.stat-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.stat-label {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 500;
}

.stat-value {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

.stat-value.excellent {
  color: #059669;
}

.stat-value.good {
  color: #3b82f6;
}

.stat-value.average {
  color: #f59e0b;
}

.stat-value.at-risk {
  color: #ef4444;
}

@media (max-width: 768px) {
  .stat-group {
    grid-template-columns: 1fr;
  }
}
</style>
