<template>
  <div class="course-selection">
    <div class="selection-card">
      <h2>Select Course to Simulate</h2>
      <div class="course-grid">
        <div
          v-for="course in courses"
          :key="course.id"
          :class="[
            'course-option',
            { selected: selectedCourse?.id === course.id },
          ]"
          @click="selectCourse(course)"
        >
          <div class="course-header">
            <h3>{{ course.code }}</h3>
            <span
              class="current-grade"
              :class="getGradeClass(course.currentGrade)"
            >
              {{ course.currentGrade }}
            </span>
          </div>
          <h4>{{ course.name }}</h4>
          <div class="course-progress">
            <div class="progress-info">
              <span>Progress: {{ course.completedWeight }}% / 100%</span>
              <span>Current: {{ course.currentMarks }}%</span>
            </div>
            <div class="progress-bar">
              <div
                class="progress-fill"
                :style="{ width: course.completedWeight + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CourseSelection",
  props: {
    courses: {
      type: Array,
      required: true,
      default: () => [],
    },
    selectedCourse: {
      type: Object,
      default: null,
    },
  },
  emits: ["course-selected"],
  methods: {
    selectCourse(course) {
      this.$emit("course-selected", course);
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
.course-selection {
  margin-bottom: 30px;
}

.selection-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.selection-card h2 {
  margin: 0 0 20px 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
}

.course-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 16px;
}

.course-option {
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.2s;
  background: #f8fafc;
}

.course-option:hover {
  border-color: #3b82f6;
  background: #eff6ff;
  transform: translateY(-2px);
}

.course-option.selected {
  border-color: #3b82f6;
  background: #eff6ff;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.course-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.course-header h3 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

.current-grade {
  font-size: 1.1rem;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 6px;
  background: white;
}

.current-grade.grade-a-plus,
.current-grade.grade-a {
  color: #059669;
}
.current-grade.grade-a-minus,
.current-grade.grade-b-plus {
  color: #3b82f6;
}
.current-grade.grade-b {
  color: #f59e0b;
}
.current-grade.grade-b-minus,
.current-grade.grade-c-plus,
.current-grade.grade-c {
  color: #ef4444;
}

.course-option h4 {
  margin: 0 0 16px 0;
  color: #64748b;
  font-weight: 500;
}

.course-progress {
  margin-top: 12px;
}

.progress-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 0.9rem;
  color: #64748b;
}

.progress-bar {
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: #3b82f6;
  border-radius: 3px;
  transition: width 0.3s ease;
}

@media (max-width: 768px) {
  .course-grid {
    grid-template-columns: 1fr;
  }
}
</style>
