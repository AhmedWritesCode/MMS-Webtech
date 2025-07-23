<template>
  <aside :class="['student-sidebar', { collapsed: isCollapsed }]">
    <div class="sidebar-header">
      <div class="brand-logo"></div>
      <span v-if="!isCollapsed" class="brand-name">Course Marks</span>
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
  background: #B5B682;
  color: #23272f;
  height: 100vh;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  border-radius: 1.5rem;
  box-shadow: 0 6px 24px 0 rgba(124, 152, 133, 0.18);
  border: 2px solid #7C9885;
  transition: width 0.2s;
  z-index: 1000;
}

.student-sidebar.collapsed {
  width: 64px;
}

.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.2rem 1rem 1.2rem 1.2rem;
  background: #7C9885;
  border-top-left-radius: 1.5rem;
  border-top-right-radius: 1.5rem;
}

.brand-logo {
  font-size: 2rem;
  color: #fff;
}

.brand-name {
  font-weight: 700;
  font-size: 1.2rem;
  color: #fff;
  margin-left: 0.5rem;
}

.collapse-btn {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.1rem;
  cursor: pointer;
  margin-left: 0.5rem;
}

.sidebar-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 1.5rem 0.5rem 1.5rem 0.5rem;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.9rem 1.2rem;
  border-radius: 0.8rem;
  color: #23272f;
  font-weight: 500;
  font-size: 1rem;
  text-decoration: none;
  transition: background 0.2s, color 0.2s;
}

.sidebar-link.active {
  background: #7C9885;
  color: #fff;
}

.sidebar-link:hover {
  background: #7C9885;
  color: #fff;
}

.sidebar-icon {
  font-size: 1.3rem;
  color: #7C9885;
  transition: color 0.2s;
}

.sidebar-link.active .sidebar-icon,
.sidebar-link:hover .sidebar-icon {
  color: #fff;
}

.sidebar-footer {
  padding: 1.2rem 1rem;
  border-bottom-left-radius: 1.5rem;
  border-bottom-right-radius: 1.5rem;
  background: #B5B682;
  border-top: 1.5px solid #7C9885;
}

.sidebar-user {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  margin-bottom: 1rem;
}

.user-avatar {
  background: #7C9885;
  color: #fff;
  border-radius: 50%;
  width: 2.2rem;
  height: 2.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.1rem;
}

.user-name {
  color: #23272f;
  font-weight: 600;
  font-size: 1rem;
}

.logout-btn {
  width: 100%;
  background: #7C9885;
  color: #fff;
  border: none;
  border-radius: 0.7rem;
  padding: 0.7rem 0;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}

.logout-btn:hover {
  background: #23272f;
  color: #fff;
}
@media (max-width: 900px) {
  .student-sidebar {
    position: absolute;
    height: 100%;
    z-index: 2000;
  }
}
</style>
