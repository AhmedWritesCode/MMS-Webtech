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
  background: rgba(255,255,255,0.92);
  padding: 2.5rem 2rem;
  border-radius: 1.5rem;
  box-shadow: 0 4px 24px rgba(181, 182, 130, 0.10);
  min-height: 400px;
  border: 2px solid #B5B682;
  position: relative;
}

.course-cards-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 2.5rem;
  justify-content: center;
  width: 100%;
}

.no-courses-message {
  text-align: center;
  color: #1ed760;
  font-size: 1.3rem;
  margin: 3rem 0;
  background: #181818;
  border: 2px dashed #1ed760;
  padding: 2rem 0;
  border-radius: 12px;
  letter-spacing: 1px;
  font-weight: bold;
}

.loading-overlay {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  background: rgba(16, 28, 16, 0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.loading-spinner {
  color: #1ed760;
  font-size: 1.5rem;
  font-weight: bold;
  letter-spacing: 2px;
  background: #181818;
  padding: 1.5rem 3rem;
  border-radius: 16px;
  box-shadow: 0 0 24px #1ed76044;
  border: 2px solid #1ed760;
  animation: pulse 1.2s infinite alternate;
}

@keyframes pulse {
  from { box-shadow: 0 0 24px #1ed76044; }
  to { box-shadow: 0 0 48px #1ed760aa; }
}
</style>
