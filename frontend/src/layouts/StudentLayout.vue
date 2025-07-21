<template>
  <div>
    <!-- Include Navbar -->
    <StudentNavbar />
    <!-- Main Content Area -->
    <div class="main-content">
      <router-view />
    </div>
  </div>
</template>

<script>
import StudentNavbar from "@/components/navigation/StudentNavbar.vue";

export default {
  components: {
    StudentNavbar,
  },
};
</script>
<style scoped>
.main-content {
  padding: 20px;
}
</style>
