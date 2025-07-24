<template>
  <div>
    <!-- Include Navbar -->
    <LecturerNavbar />
    <!-- Main Content Area -->
    <div class="main-content">
      <router-view />
    </div>
  </div>
</template>

<script>
import LecturerNavbar from "@/components/navigation/LecturerNavbar.vue";

export default {
  name: "LecturerLayout",
  components: {
    LecturerNavbar,
  },
};
</script>

<style scoped>
.main-content {
  /* margin-top: 80px; Account for fixed navbar height */
  /* padding: 20px; */
  min-height: calc(100vh - 80px); /* Ensure full height minus navbar */
}

/* Optional: Add some breathing room */
@media (max-width: 768px) {
  .main-content {
    padding: 16px;
  }
}
</style>