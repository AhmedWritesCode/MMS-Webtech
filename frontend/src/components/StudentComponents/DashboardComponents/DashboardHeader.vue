<template>
  <div class="dashboard-header">
    <div class="student-info">
      <h1 class="welcome-text">Welcome, {{ studentName }}</h1>
      <p class="matric-number">{{ matricNumber }}</p>
    </div>
    <div class="semester-info">
      <span class="semester-badge">{{ currentSemester }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: "DashboardHeader",
  props: {
    studentName: {
      type: String,
      required: true,
      default: "Student",
    },
    matricNumber: {
      type: String,
      required: true,
      default: "",
    },
    currentSemester: {
      type: String,
      required: true,
      default: "",
    },
  },
};
</script>

<style scoped>
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 30px;
  border-radius: 16px;
  color: white;
}

.welcome-text {
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
}

.matric-number {
  font-size: 1.1rem;
  opacity: 0.9;
  margin: 5px 0 0 0;
}

.semester-badge {
  background: rgba(255, 255, 255, 0.2);
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 600;
  backdrop-filter: blur(10px);
}

/* Responsive Design */
@media (max-width: 768px) {
  .dashboard-header {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }
}
</style>
