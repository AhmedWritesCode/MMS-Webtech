<template>
  <div class="course-selector">
    <div class="selector-header">
      <h2>Select Course</h2>
      <div class="course-tabs">
        <button
          v-for="course in courses"
          :key="course.id"
          :class="['course-tab', { active: selectedCourse?.id === course.id }]"
          @click="selectCourse(course)"
        >
          <span class="course-code">{{ course.code }}</span>
          <span class="course-name">{{ course.name }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CourseSelector",
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
  },
};
</script>

<style scoped>
.course-selector {
  background: #111;
  border-radius: 20px;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 4px 24px rgba(0, 128, 0, 0.15);
  border: 2px solid #22c55e;
}

.selector-header h2 {
  margin: 0 0 28px 0;
  font-size: 1.5rem;
  font-weight: 800;
  color: #22c55e;
  letter-spacing: 1px;
  text-shadow: 0 2px 8px #000a;
}

.course-tabs {
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
  justify-content: flex-start;
}

.course-tab {
  background: #f1f5f9;
  border: 2px solid #7C9885;
  border-radius: 12px;
  padding: 16px 20px;
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
  color: #7C9885;
}

.course-tab:hover {
  border-color: #B5B682;
  background: #F3F5E6;
  color: #7C9885;
}

.course-tab.active {
  border-color: #7C9885;
  background: #7C9885;
  color: #fff;
}

.course-code {
  display: block;
  font-weight: 900;
  font-size: 1.2rem;
  margin-bottom: 6px;
  color: inherit;
  letter-spacing: 1px;
}

.course-name {
  display: block;
  font-size: 1rem;
  opacity: 0.85;
  color: inherit;
}

@media (max-width: 768px) {
  .course-tabs {
    flex-direction: column;
    gap: 14px;
  }
  .course-tab {
    width: 100%;
    padding: 18px 16px;
  }
}
</style>
