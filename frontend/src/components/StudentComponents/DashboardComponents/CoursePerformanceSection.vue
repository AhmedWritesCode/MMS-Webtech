<template>
  <div class="course-performance-section">
    <div v-if="courses.length === 0 && !loading" class="no-courses-message">
      No courses to display.
    </div>
    <div v-else class="course-cards-grid">
      <CourseCard
        v-for="course in courses"
        :key="course.id"
        :course="course"
        @course-details="$emit('course-details', course)"
      />
    </div>
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner">Loading...</div>
    </div>
  </div>
</template>

<script>
import CourseCard from './CourseCard.vue';
export default {
  name: 'CoursePerformanceSection',
  components: { CourseCard },
  props: {
    courses: {
      type: Array,
      default: () => [],
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style scoped>
.course-performance-section {
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
}
.course-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 2rem;
  width: 100%;
}
.no-courses-message {
  text-align: center;
  color: #888;
  font-size: 1.1rem;
  margin: 2rem 0;
}
.loading-overlay {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  background: rgba(255,255,255,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}
</style>
