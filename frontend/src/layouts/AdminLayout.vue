<template>
  <div>
    <AdminNavbar />
    <router-view /> 
  </div>
</template>

<script>
import AdminNavbar from '@/components/navigation/AdminNavbar.vue';
export default {
  components: { AdminNavbar }
};
</script>
