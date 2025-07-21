<template>
  <div class="lecturer-analytics-view">
    <h1>Course Analytics</h1>
    <p>Course ID: {{ $route.params.courseId }}</p>
    <p>This page will contain course analytics for lecturers.</p>
  </div>
</template>

<script>
export default {
  name: "LecturerAnalyticsView",
};
</script>

<style scoped>
.lecturer-analytics-view {
  padding: 24px;
}
</style>
