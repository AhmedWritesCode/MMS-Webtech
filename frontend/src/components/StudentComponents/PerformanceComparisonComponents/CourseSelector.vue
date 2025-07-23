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
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.selector-header h2 {
  margin: 0 0 20px 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
}

.course-tabs {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
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
  font-weight: 700;
  font-size: 1.1rem;
  margin-bottom: 4px;
}

.course-name {
  display: block;
  font-size: 0.9rem;
  opacity: 0.8;
}

@media (max-width: 768px) {
  .course-tabs {
    flex-direction: column;
  }
}
</style>
