<template>
  <div class="section-card current-courses">
    <div class="section-header">
      <h3>Current Semester Courses</h3>
      <span class="course-count">{{ courses.length }} courses</span>
    </div>
    <div class="courses-list">
      <div
        v-for="course in courses"
        :key="course.id"
        class="course-item"
        :class="getPerformanceClass(course.currentGrade)"
        @click="$emit('view-course-details', course.id)"
      >
        <div class="course-header">
          <div class="course-info">
            <h4>{{ course.code }}</h4>
            <p>{{ course.name }}</p>
          </div>
          <div
            class="course-grade"
            :class="getPerformanceClass(course.currentGrade)"
          >
            {{ course.currentGrade || "In Progress" }}
          </div>
        </div>
        <div class="course-details">
          <div class="detail-item">
            <span class="detail-label">Credits:</span>
            <span class="detail-value">{{ course.credits }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Progress:</span>
            <span class="detail-value">{{ course.progress }}%</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Lecturer:</span>
            <span class="detail-value">{{ course.lecturer }}</span>
          </div>
        </div>
        <div class="course-progress">
          <div class="progress-bar">
            <div
              class="progress-fill"
              :style="{ width: course.progress + '%' }"
              :class="getProgressClass(course.progress)"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CurrentCoursesCard",
  props: {
    courses: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: ["view-course-details"],
  methods: {
    getPerformanceClass(value) {
      if (typeof value === "string" || value === null) return "in-progress";
      if (value >= 3.5) return "excellent";
      if (value >= 3.0) return "good";
      if (value >= 2.5) return "average";
      return "at-risk";
    },

    getProgressClass(progress) {
      if (progress >= 80) return "excellent";
      if (progress >= 60) return "good";
      if (progress >= 40) return "average";
      return "at-risk";
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

.course-count {
  background: #f1f5f9;
  color: #475569;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.courses-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.course-item {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
  cursor: pointer;
  transition: all 0.2s;
  border-left: 4px solid #e2e8f0;
}

.course-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.course-item.excellent {
  border-left-color: #059669;
}

.course-item.good {
  border-left-color: #3b82f6;
}

.course-item.average {
  border-left-color: #f59e0b;
}

.course-item.at-risk {
  border-left-color: #ef4444;
}

.course-item.in-progress {
  border-left-color: #94a3b8;
}

.course-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.course-info h4 {
  margin: 0 0 4px 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.course-info p {
  margin: 0;
  color: #64748b;
  font-size: 0.9rem;
}

.course-grade {
  font-weight: 700;
  padding: 6px 12px;
  border-radius: 6px;
  background: #f1f5f9;
}

.course-grade.excellent {
  background: #dcfce7;
  color: #059669;
}

.course-grade.good {
  background: #dbeafe;
  color: #3b82f6;
}

.course-grade.average {
  background: #fef3c7;
  color: #d97706;
}

.course-grade.at-risk {
  background: #fee2e2;
  color: #dc2626;
}

.course-grade.in-progress {
  background: #f1f5f9;
  color: #64748b;
}

.course-details {
  display: flex;
  gap: 20px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}

.detail-item {
  display: flex;
  gap: 4px;
  font-size: 0.9rem;
}

.detail-label {
  color: #64748b;
}

.detail-value {
  font-weight: 600;
  color: #1e293b;
}

.course-progress {
  margin-top: 8px;
}

.progress-bar {
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 3px;
  transition: width 0.3s ease;
}

.progress-fill.excellent {
  background: #059669;
}

.progress-fill.good {
  background: #3b82f6;
}

.progress-fill.average {
  background: #f59e0b;
}

.progress-fill.at-risk {
  background: #ef4444;
}

@media (max-width: 768px) {
  .course-details {
    flex-direction: column;
    gap: 8px;
  }
}
</style>
