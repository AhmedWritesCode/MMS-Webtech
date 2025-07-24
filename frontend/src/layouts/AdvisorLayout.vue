<template>
  <div class="advisor-layout">
    <AdvisorNavbar @sidebar-toggle="handleSidebarToggle" />
    <div class="main-content" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
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
  data() {
    return {
      sidebarCollapsed: false,
    };
  },
  methods: {
    handleSidebarToggle(collapsed) {
      this.sidebarCollapsed = collapsed;
    },
  },
};
</script>

<style scoped>
.advisor-layout {
  display: flex;
  min-height: 100vh;
  background: linear-gradient(135deg, #7C9885 0%, #B5B682 100%);
}

.main-content {
  flex: 1;
  margin-left: 220px; /* Match the width of the sidebar */
  width: calc(100% - 220px);
  transition: margin-left 0.2s, width 0.2s;
  min-height: 100vh;
}

.main-content.sidebar-collapsed {
  margin-left: 64px;
  width: calc(100% - 64px);
}

@media (max-width: 900px) {
  .main-content {
    margin-left: 64px;
    width: calc(100% - 64px);
  }
}
</style>
