<template>
  <div class="section-card academic-history">
    <div class="section-header">
      <h3>Academic History</h3>
      <div class="history-controls">
        <select
          :value="selectedHistoryView"
          @change="$emit('update-history-view', $event.target.value)"
          class="history-select"
        >
          <option value="semester">By Semester</option>
          <option value="cumulative">Cumulative</option>
        </select>
      </div>
    </div>
    <div class="history-content">
      <div
        v-for="semester in academicHistory"
        :key="semester.id"
        class="semester-item"
      >
        <div class="semester-header">
          <h4>{{ semester.name }}</h4>
          <div class="semester-gpa" :class="getPerformanceClass(semester.gpa)">
            GPA: {{ semester.gpa }}
          </div>
        </div>
        <div class="semester-stats">
          <div class="stat">
            <span class="stat-label">Credits:</span>
            <span class="stat-value">{{ semester.credits }}</span>
          </div>
          <div class="stat">
            <span class="stat-label">Courses:</span>
            <span class="stat-value">{{ semester.courseCount }}</span>
          </div>
          <div class="stat">
            <span class="stat-label">Status:</span>
            <span class="stat-value" :class="getStandingClass(semester.status)">
              {{ semester.status }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AcademicHistoryCard",
  props: {
    academicHistory: {
      type: Array,
      required: true,
      default: () => [],
    },
    selectedHistoryView: {
      type: String,
      required: true,
      default: "semester",
    },
  },
  emits: ["update-history-view"],
  methods: {
    getPerformanceClass(value) {
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

.history-controls {
  display: flex;
  gap: 12px;
}

.history-select {
  padding: 6px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background: white;
  font-size: 0.9rem;
}

.history-content {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.semester-item {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
  background: #f8fafc;
}

.semester-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.semester-header h4 {
  margin: 0;
  font-weight: 700;
  color: #1e293b;
}

.semester-gpa {
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 6px;
  background: #f1f5f9;
}

.semester-gpa.excellent {
  background: #dcfce7;
  color: #059669;
}

.semester-gpa.good {
  background: #dbeafe;
  color: #3b82f6;
}

.semester-gpa.average {
  background: #fef3c7;
  color: #d97706;
}

.semester-gpa.at-risk {
  background: #fee2e2;
  color: #dc2626;
}

.semester-stats {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.stat {
  display: flex;
  gap: 4px;
  font-size: 0.9rem;
}

.stat-label {
  color: #64748b;
}

.stat-value {
  font-weight: 600;
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

.stat-value.in-progress {
  color: #64748b;
}

@media (max-width: 480px) {
  .semester-stats {
    flex-direction: column;
    gap: 8px;
  }
}
</style>
