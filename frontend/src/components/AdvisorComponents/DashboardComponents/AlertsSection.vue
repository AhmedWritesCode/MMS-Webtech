<template>
  <div v-if="atRiskStudents.length > 0" class="alerts-section">
    <div class="alert-card urgent">
      <div class="alert-header">
        <div class="alert-icon">⚠️</div>
        <h3>Students Requiring Immediate Attention</h3>
        <span class="alert-count">{{ atRiskStudents.length }} students</span>
      </div>
      <div class="alert-students">
        <div
          v-for="student in atRiskStudents.slice(0, 3)"
          :key="student.id"
          class="alert-student-item"
          @click="viewStudent(student.id)"
        >
          <div class="student-info">
            <span class="student-name">{{ student.name }}</span>
            <span class="student-id">{{ student.matricNumber }}</span>
          </div>
          <div class="risk-indicator">
            <span class="gpa-warning">GPA: {{ student.currentGPA }}</span>
            <span class="risk-level">{{ student.riskLevel }}</span>
          </div>
        </div>
        <button
          v-if="atRiskStudents.length > 3"
          class="view-all-risks-btn"
          @click="viewAllAtRisk"
        >
          View All {{ atRiskStudents.length }} At-Risk Students
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AlertsSection",
  props: {
    atRiskStudents: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: ["view-student", "view-all-at-risk"],
  methods: {
    viewStudent(studentId) {
      this.$emit("view-student", studentId);
    },

    viewAllAtRisk() {
      this.$emit("view-all-at-risk");
    },
  },
};
</script>

<style scoped>
.alerts-section {
  margin-bottom: 30px;
  background-color: #ffffff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.alert-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 6px rgba(239, 68, 68, 0.1);
  border-left: 4px solid #ef4444;
}

.alert-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.alert-icon {
  font-size: 1.5rem;
}

.alert-header h3 {
  flex: 1;
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #dc2626;
}

.alert-count {
  background: #fee2e2;
  color: #dc2626;
  padding: 6px 14px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(220, 38, 38, 0.1);
}

.alert-students {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.alert-student-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background: #fef2f2;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 2px 4px rgba(220, 38, 38, 0.05);
}

.alert-student-item:hover {
  background: #fee2e2;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(220, 38, 38, 0.1);
}

.student-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.student-name {
  font-weight: 700;
  color: #1e293b;
}

.student-id {
  font-size: 0.9rem;
  color: #64748b;
}

.risk-indicator {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
}

.gpa-warning {
  font-weight: 700;
  color: #dc2626;
}

.risk-level {
  font-size: 0.8rem;
  padding: 4px 10px;
  border-radius: 8px;
  font-weight: 600;
  background: #dc2626;
  color: white;
  box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
}

.view-all-risks-btn {
  background: #dc2626;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(220, 38, 38, 0.2);
  margin-top: 8px;
}

.view-all-risks-btn:hover {
  background: #b91c1c;
  transform: translateY(-2px);
  box-shadow: 0 6px 10px rgba(220, 38, 38, 0.3);
}

@media (max-width: 480px) {
  .alert-student-item {
    flex-direction: column;
    gap: 12px;
    text-align: center;
  }
}
</style>
