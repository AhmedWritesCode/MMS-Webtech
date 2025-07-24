<template>
  <aside :class="['advisor-sidebar', { collapsed: isCollapsed }]">
    <div class="sidebar-header">
      <div class="brand-logo"></div>
      <span v-if="!isCollapsed" class="brand-name">Course Marks</span>
      <button class="collapse-btn" @click="toggleSidebar">
        <span v-if="isCollapsed">▶</span>
        <span v-else>◀</span>
      </button>
    </div>
    <nav class="sidebar-nav">
      <router-link to="/advisor/dashboard" class="sidebar-link" :class="{ active: $route.path === '/advisor/dashboard' }">
        <span class="sidebar-icon">🏠</span>
        <span v-if="!isCollapsed" class="sidebar-text">Dashboard</span>
      </router-link>
      <router-link to="/advisor/advisees" class="sidebar-link" :class="{ active: $route.path.startsWith('/advisor/advisee') }">
        <span class="sidebar-icon">👥</span>
        <span v-if="!isCollapsed" class="sidebar-text">My Advisees</span>
        <span v-if="!isCollapsed && atRiskCount > 0" class="risk-badge">{{ atRiskCount }}</span>
      </router-link>
      <router-link to="/advisor/analytics" class="sidebar-link" :class="{ active: $route.path === '/advisor/analytics' }">
        <span class="sidebar-icon">📊</span>
        <span v-if="!isCollapsed" class="sidebar-text">Analytics</span>
      </router-link>
      <router-link to="/advisor/reports" class="sidebar-link" :class="{ active: $route.path.startsWith('/advisor/reports') }">
        <span class="sidebar-icon">📈</span>
        <span v-if="!isCollapsed" class="sidebar-text">Reports</span>
      </router-link>
      <router-link to="/advisor/schedule-meeting" class="sidebar-link" :class="{ active: $route.path === '/advisor/schedule-meeting' }">
        <span class="sidebar-icon">📅</span>
        <span v-if="!isCollapsed" class="sidebar-text">Schedule Meeting</span>
      </router-link>
      <router-link to="/advisor/profile" class="sidebar-link" :class="{ active: $route.path === '/advisor/profile' }">
        <span class="sidebar-icon">👤</span>
        <span v-if="!isCollapsed" class="sidebar-text">Profile</span>
      </router-link>
    </nav>
    <div class="sidebar-footer" v-if="!isCollapsed">
      <div class="sidebar-user">
        <span class="user-avatar">{{ getAdvisorInitials(advisorName) }}</span>
        <span class="user-name">{{ advisorName }}</span>
      </div>
      <button class="logout-btn" @click="logout">Logout</button>
    </div>
  </aside>
</template>

<script>
import authService from "@/services/auth";
import { advisorAPI, utilityAPI } from "@/services/api";

export default {
  name: "AdvisorNavbar",
  data() {
    return {
      isCollapsed: false,
      advisorName: "Advisor",
      advisorId: null,
      totalAdvisees: 0,
      atRiskCount: 0,
      recentStudents: [],
    };
  },
  async mounted() {
    await this.loadAdvisorData();
    document.addEventListener("click", this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
  },
  methods: {
    async loadAdvisorData() {
      try {
        // Get user profile
        const userResponse = await utilityAPI.getUserProfile();
        if (userResponse.success) {
          this.advisorName = userResponse.data.full_name;
          this.advisorId = userResponse.data.id;
          
          // Get advisees data
          if (this.advisorId) {
            const adviseesResponse = await advisorAPI.getAdvisees(this.advisorId);
            if (adviseesResponse.success && adviseesResponse.data && adviseesResponse.data.advisees) {
              const advisees = adviseesResponse.data.advisees;
              this.totalAdvisees = advisees.length;
              this.atRiskCount = advisees.filter(
                (a) => a.risk_level === "High" || a.risk_level === "Critical"
              ).length;
              
              // Show up to 3 most recent advisees
              this.recentStudents = advisees.slice(0, 3).map((a) => ({
                id: a.id,
                name: a.full_name,
                gpa: parseFloat(a.current_gpa) || 0.0,
              }));
            }
          }
        }
      } catch (error) {
        console.error("Error loading advisor data:", error);
      }
    },
    getAdvisorInitials(name) {
      return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
    },
    toggleSidebar() {
      this.isCollapsed = !this.isCollapsed;
      // Emit event to parent components
      this.$emit('sidebar-toggle', this.isCollapsed);
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        // Only close dropdowns, not the sidebar
      }
    },
    async logout() {
      try {
        await authService.logout();
        this.$router.push("/login");
      } catch (error) {
        console.error("Logout error:", error);
        this.$router.push("/login");
      }
    },
  },
};
</script>

<style scoped>
.advisor-sidebar {
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

.advisor-sidebar.collapsed {
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
  position: relative;
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

.risk-badge {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: #e74c3c;
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  min-width: 18px;
  height: 18px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 5px;
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
  .advisor-sidebar {
    position: absolute;
    height: 100%;
    z-index: 2000;
  }
}
</style>
