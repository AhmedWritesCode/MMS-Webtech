<template>
  <div class="no-course-selected">
    <div class="empty-state">
      <div class="empty-icon">📊</div>
      <h3>Select a Course</h3>
      <p>Choose a course from above to view performance comparison</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "NoCourseSelected",
};
</script>

<style scoped>
.no-course-selected {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

.empty-state {
  text-align: center;
  color: #64748b;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
  opacity: 0.5;
}

.empty-state h3 {
  margin: 0 0 8px 0;
  font-size: 1.5rem;
  font-weight: 700;
}

.empty-state p {
  margin: 0;
  font-size: 1rem;
}
</style>
