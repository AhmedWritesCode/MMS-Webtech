<template>
  <div class="lecturer-course-view">
    <h1>Course Management</h1>
    <p>Course ID: {{ $route.params.courseId }}</p>
    <p>This page will contain course management features for lecturers.</p>
  </div>
</template>

<script>
export default {
  name: "LecturerCourseView",
};
</script>

<style scoped>
.lecturer-course-view {
  padding: 24px;
}
</style>
