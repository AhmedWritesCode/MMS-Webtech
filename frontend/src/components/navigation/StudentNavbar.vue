<template>
  <aside :class="['student-sidebar', { collapsed: isCollapsed }]">
    <div class="sidebar-header">
      <div class="brand-logo">🎓</div>
      <span v-if="!isCollapsed" class="brand-name">StudentMarks</span>
      <button class="collapse-btn" @click="toggleSidebar">
        <span v-if="isCollapsed">▶</span>
        <span v-else>◀</span>
      </button>
    </div>
    <nav class="sidebar-nav">
      <router-link to="/student/dashboard" class="sidebar-link" :class="{ active: $route.path === '/student/dashboard' }">
       <Home class="sidebar-icon" />
        <span v-if="!isCollapsed" class="sidebar-text">Dashboard</span>
      </router-link>
      <router-link to="/student/performance-comparison" class="sidebar-link" :class="{ active: $route.path === '/student/performance-comparison' }">
       <BarChart2 class="sidebar-icon" />
        <span v-if="!isCollapsed" class="sidebar-text">Compare</span>
      </router-link>
      <router-link to="/student/performance-trends" class="sidebar-link" :class="{ active: $route.path === '/student/performance-trends' }">
       <TrendingUp class="sidebar-icon" />
        <span v-if="!isCollapsed" class="sidebar-text">Trends</span>
      </router-link>
      <router-link to="/student/what-if-simulator" class="sidebar-link" :class="{ active: $route.path === '/student/what-if-simulator' }">
    <Crosshair class="sidebar-icon" />
        <span v-if="!isCollapsed" class="sidebar-text">Simulator</span>
      </router-link>
      <router-link to="/student/profile" class="sidebar-link" :class="{ active: $route.path === '/student/profile' }">
       <User class="sidebar-icon" />
        <span v-if="!isCollapsed" class="sidebar-text">Profile</span>
      </router-link>
    </nav>
    <div class="sidebar-footer" v-if="!isCollapsed">
      <div class="sidebar-user">
        <span class="user-avatar">{{ userInitials }}</span>
        <span class="user-name">{{ userName }}</span>
      </div>
      <button class="logout-btn" @click="logout">Logout</button>
    </div>
  </aside>
</template>

<script>
import { Home, BarChart2, TrendingUp, Crosshair, User } from 'lucide-vue-next';

export default {
  name: "StudentNavbar",
  components: {
  Home,
  BarChart2,
  TrendingUp,
  Crosshair,
  User,
},
  data() {
    return {
      isCollapsed: false,
    };
  },
  computed: {
    userName() {
      // Replace with actual user name from store/auth
      return this.$store?.state?.auth?.user?.name || "Student";
    },
    userInitials() {
      const name = this.userName.split(" ");
      return name.length > 1 ? name[0][0] + name[1][0] : name[0][0];
    },
  },
  methods: {
    toggleSidebar() {
      this.isCollapsed = !this.isCollapsed;
    },
    logout() {
      // Replace with actual logout logic
      this.$router.push("/login");
    },
  },
};
</script>

<style scoped>
.student-sidebar {
  width: 220px;
  background: #23272f;
  color: #f3f4f6;
  height: 100vh;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  transition: width 0.2s;
  z-index: 1000;
}
.student-sidebar.collapsed {
  width: 64px;
}
.sidebar-header {
  display: flex;
  align-items: center;
  padding: 1.5rem 1rem 1rem 1rem;
  background: #23272f;
  justify-content: space-between;
}
.brand-logo {
  font-size: 2rem;
}
.brand-name {
  font-weight: bold;
  font-size: 1.2rem;
  margin-left: 0.5rem;
  color: #f3f4f6;
}
.collapse-btn {
  background: none;
  border: none;
  color: #f3f4f6;
  font-size: 1.2rem;
  cursor: pointer;
}
.sidebar-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  margin-top: 2rem;
}
.sidebar-link {
  display: flex;
  align-items: center;
  padding: 1rem;
  color: #f3f4f6;
  text-decoration: none;
  transition: background 0.2s, color 0.2s;
  border-left: 4px solid transparent;
  font-size: 1.1rem;
}
.sidebar-link.active {
  background: #2d323c;
  border-left: 4px solid #6c63ff;
  color: #6c63ff;
}
.sidebar-link:hover {
  background: #262b34;
  color: #6c63ff;
}
.sidebar-icon {
  font-size: 1.5rem;
  margin-right: 1rem;
}
.student-sidebar.collapsed .sidebar-icon {
  margin-right: 0;
}
.sidebar-text {
  transition: opacity 0.2s;
}
.student-sidebar.collapsed .sidebar-text {
  opacity: 0;
  width: 0;
  overflow: hidden;
}
.sidebar-footer {
  padding: 1rem;
  background: #23272f;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
.sidebar-user {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}
.user-avatar {
  background: #6c63ff;
  color: #fff;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.1rem;
  margin-right: 0.7rem;
}
.user-name {
  font-size: 1rem;
}
.logout-btn {
  background: #6c63ff;
  color: #fff;
  border: none;
  padding: 0.5rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  margin-top: 0.5rem;
  transition: background 0.2s;
}
.logout-btn:hover {
  background: #5548c8;
}
@media (max-width: 900px) {
  .student-sidebar {
    position: absolute;
    height: 100%;
    z-index: 2000;
  }
}
</style>
