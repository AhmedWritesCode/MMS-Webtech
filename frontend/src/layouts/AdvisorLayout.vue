<template>
  <div>
    <!-- Include Navbar -->
    <AdvisorNavbar />
    <!-- Main Content Area -->
    <div class="main-content">
      <router-view />
    </div>
  </div>
</template>

<script>
import AdvisorNavbar from "@/components/navigation/AdvisorNavbar.vue";

export default {
  components: {
    AdvisorNavbar,
  },
};
</script>
<style scoped>
.main-content {
  padding: 20px;
}
</style>
