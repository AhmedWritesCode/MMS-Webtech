<template>
  <div class="course-card" @click="$emit('course-clicked', course.id)">
    <div class="course-header">
      <h3 class="course-code">{{ course.code || "N/A" }}</h3>
      <span class="course-credits"
        >{{ course.credits != null ? course.credits : "—" }} Credits</span
      >
    </div>
    <h4 class="course-name">{{ course.name || "Untitled Course" }}</h4>

    <!-- Progress Section -->
    <div class="progress-section">
      <div class="progress-label">Progress</div>
      <div class="progress-bar">
        <div
          class="progress-fill"
          :style="{
            width:
              (course.progressPercentage != null
                ? course.progressPercentage
                : 0) + '%',
          }"
          :class="
            getProgressClass(
              course.progressPercentage != null ? course.progressPercentage : 0
            )
          "
        ></div>
      </div>
      <div class="progress-numbers">
        <span>{{ course.currentMarks != null ? course.currentMarks : 0 }}</span>
        <span class="divider">/</span>
        <span>{{ course.totalMarks != null ? course.totalMarks : 100 }}</span>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-block">
        <div class="stat-label">Current Grade</div>
        <div
          class="stat-value"
          :class="getGradeClass(course.currentGrade || 'N/A')"
        >
          {{ course.currentGrade || "N/A" }}
        </div>
      </div>
      <div class="stat-block">
        <div class="stat-label">Class Average</div>
        <div class="stat-value">
          {{ course.classAverage != null ? course.classAverage : "—" }}%
        </div>
      </div>
    </div>

    <!-- Assessment Status -->
    <div class="status-grid">
      <div class="status-block completed">
        <span class="status-dot completed"></span>
        <span
          >{{
            course.completedAssessments != null
              ? course.completedAssessments
              : 0
          }}
          Completed</span
        >
      </div>
      <div class="status-block pending">
        <span class="status-dot pending"></span>
        <span
          >{{
            course.pendingAssessments != null ? course.pendingAssessments : 0
          }}
          Pending</span
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CourseCard",
  props: {
    course: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  emits: ["course-clicked"],
  methods: {
    getProgressClass(percentage) {
      if (percentage >= 80) return "excellent";
      if (percentage >= 70) return "good";
      if (percentage >= 60) return "average";
      return "needs-improvement";
    },

    getGradeClass(grade) {
      const gradeMap = {
        "A+": "grade-a-plus",
        A: "grade-a",
        "A-": "grade-a-minus",
        "B+": "grade-b-plus",
        B: "grade-b",
        "B-": "grade-b-minus",
        "C+": "grade-c-plus",
        C: "grade-c",
      };
      return gradeMap[grade] || "grade-default";
    },
  },
};
</script>

<style scoped>
.course-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  min-width: 280px;
  max-width: 100%;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.course-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.course-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
  min-width: 0;
}

.course-code {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
  max-width: 140px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.course-credits {
  background: #e2e8f0;
  color: #475569;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  max-width: 80px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.course-name {
  font-size: 1rem;
  color: #64748b;
  margin: 0 0 10px 0;
  font-weight: 500;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.progress-section {
  margin-bottom: 8px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.progress-label {
  font-size: 0.95rem;
  font-weight: 700;
  color: #2563eb;
  margin-bottom: 2px;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  transition: width 0.3s ease;
  border-radius: 4px;
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
.progress-fill.needs-improvement {
  background: #ef4444;
}

.progress-numbers {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  font-size: 0.95rem;
  font-weight: 600;
  color: #334155;
  gap: 2px;
}
.progress-numbers .divider {
  margin: 0 2px;
}

.stats-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-bottom: 4px;
}
.stat-block {
  background: #f8fafc;
  border-radius: 8px;
  padding: 10px 8px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 2px;
}
.stat-label {
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 600;
}
.stat-value {
  font-weight: 700;
  font-size: 1.1rem;
  color: #1e293b;
}
/* Grade Colors */
.grade-a-plus,
.grade-a {
  color: #059669;
}
.grade-a-minus,
.grade-b-plus {
  color: #3b82f6;
}
.grade-b {
  color: #f59e0b;
}
.grade-b-minus,
.grade-c-plus,
.grade-c {
  color: #ef4444;
}

.status-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}
.status-block {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.9rem;
  color: #64748b;
  background: #f1f5f9;
  border-radius: 8px;
  padding: 6px 8px;
}
.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}
.status-dot.completed {
  background: #059669;
}
.status-dot.pending {
  background: #f59e0b;
}

/* Responsive Design */
@media (max-width: 700px) {
  .course-card {
    min-width: 220px;
    padding: 12px;
  }
  .stats-grid,
  .status-grid {
    grid-template-columns: 1fr;
  }
}
</style>
