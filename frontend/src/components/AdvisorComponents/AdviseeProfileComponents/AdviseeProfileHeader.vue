<template>
  <div class="profile-header">
    <div class="header-content">
      <div class="breadcrumb">
        <router-link to="/advisor/dashboard" class="breadcrumb-link"
          >Dashboard</router-link
        >
        <span class="breadcrumb-separator">›</span>
        <router-link to="/advisor/advisees" class="breadcrumb-link"
          >Advisees</router-link
        >
        <span class="breadcrumb-separator">›</span>
        <span class="breadcrumb-current">{{ student.name }}</span>
      </div>

      <div class="student-header">
        <div class="student-avatar-large">
          {{ getStudentInitials(student.name) }}
        </div>
        <div class="student-info">
          <h1 class="student-name">{{ student.name }}</h1>
          <p class="student-details">
            {{ student.matricNumber }} • {{ student.program }}
          </p>
          <p class="student-semester">
            {{ student.currentSemester }} • {{ student.yearOfStudy }}
          </p>
        </div>
        <div class="student-status">
          <div
            class="gpa-display"
            :class="getPerformanceClass(student.currentGPA)"
          >
            <span class="gpa-label">Current GPA</span>
            <span class="gpa-value">{{ student.currentGPA }}</span>
            <span class="gpa-trend" :class="getTrendClass(student.gpaTrend)">
              {{
                student.gpaTrend > 0 ? "↗" : student.gpaTrend < 0 ? "↘" : "→"
              }}
              {{ Math.abs(student.gpaTrend).toFixed(2) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdviseeProfileHeader",
  props: {
    student: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  methods: {
    getStudentInitials(name) {
      return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
    },

    getPerformanceClass(value) {
      if (typeof value === "string") return "in-progress";
      if (value >= 3.5) return "excellent";
      if (value >= 3.0) return "good";
      if (value >= 2.5) return "average";
      return "at-risk";
    },

    getTrendClass(trend) {
      return trend > 0 ? "trend-up" : trend < 0 ? "trend-down" : "trend-stable";
    },
  },
};
</script>

<style scoped>
.profile-header {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  border-radius: 16px;
  padding: 30px;
  margin-bottom: 30px;
  color: white;
}

.breadcrumb {
  margin-bottom: 20px;
  font-size: 0.9rem;
  opacity: 0.9;
}

.breadcrumb-link {
  color: white;
  text-decoration: none;
  transition: opacity 0.2s;
}

.breadcrumb-link:hover {
  opacity: 0.8;
  text-decoration: underline;
}

.breadcrumb-separator {
  margin: 0 8px;
}

.student-header {
  display: flex;
  align-items: center;
  gap: 24px;
}

.student-avatar-large {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
  backdrop-filter: blur(10px);
}

.student-info {
  flex: 1;
}

.student-name {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 8px 0;
}

.student-details,
.student-semester {
  margin: 0 0 4px 0;
  opacity: 0.9;
  font-size: 1.1rem;
}

.student-status {
  text-align: center;
}

.gpa-display {
  background: rgba(255, 255, 255, 0.15);
  padding: 16px;
  border-radius: 12px;
  backdrop-filter: blur(10px);
}

.gpa-label {
  display: block;
  font-size: 0.9rem;
  opacity: 0.8;
  margin-bottom: 4px;
}

.gpa-value {
  display: block;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 4px;
}

.gpa-trend {
  font-size: 0.9rem;
  font-weight: 600;
}

.gpa-trend.trend-up {
  color: #10b981;
}

.gpa-trend.trend-down {
  color: #ef4444;
}

.gpa-trend.trend-stable {
  color: #f59e0b;
}

@media (max-width: 1200px) {
  .student-header {
    flex-direction: column;
    text-align: center;
    gap: 16px;
  }
}

@media (max-width: 768px) {
  .student-name {
    font-size: 2rem;
  }
}

@media (max-width: 480px) {
  .student-header {
    gap: 12px;
  }

  .student-avatar-large {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
}
</style>
