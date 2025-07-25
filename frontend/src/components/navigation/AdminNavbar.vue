<template>
  <div class="admin-layout">
    <AdminNavbar @sidebar-toggle="handleSidebarToggle" />
    <main :class="['main-content', { 'content-expanded': isSidebarCollapsed }]">
      <router-view />
    </main>
  </div>
</template>

<script>
import AdminNavbar from '@/components/navigation/AdminNavbar.vue';

export default {
  components: { AdminNavbar },
  data() {
    return {
      isSidebarCollapsed: false
    };
  },
  methods: {
    handleSidebarToggle(collapsed) {
      this.isSidebarCollapsed = collapsed;
    }
  }
};
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  padding: 1.5rem;
  margin-left: 220px; /* Match sidebar width */
  transition: margin-left 0.2s ease;
}

.main-content.content-expanded {
  margin-left: 64px; /* Match collapsed sidebar width */
}

@media (max-width: 900px) {
  .main-content {
    margin-left: 0;
    padding: 1rem;
  }
}
</style>
