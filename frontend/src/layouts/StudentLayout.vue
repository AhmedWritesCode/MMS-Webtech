<template>
  <div class="student-layout">
    <StudentNavbar />
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script>
import StudentNavbar from "@/components/navigation/StudentNavbar.vue";
export default {
  name: "StudentLayout",
  components: { StudentNavbar },
};
</script>

<style scoped>
.student-layout {
  display: flex;
  min-height: 100vh;
  background: #ffffff;
}
.main-content {
  flex: 1;
  margin-left: 220px;
  /* padding: 2.5rem 2rem 2rem 2rem; */
  min-height: 100vh;
  transition: margin-left 0.2s;
}
@media (max-width: 900px) {
  .main-content {
    margin-left: 64px;
  }
}
</style>
