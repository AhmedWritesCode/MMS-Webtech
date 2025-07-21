<template>
  <nav class="lecturer-navbar">
    <div class="navbar-container">
      <!-- Brand -->
      <router-link to="/lecturer/dashboard" class="navbar-brand">
        <span class="brand-logo">🎓</span>
        <span class="brand-title">Lecturer Portal</span>
      </router-link>

      <!-- Desktop Nav Links -->
      <div class="navbar-links" v-if="!showMobileMenu">
        <router-link to="/lecturer/dashboard" class="nav-link" :class="{ active: $route.path === '/lecturer/dashboard' }">
          <span class="nav-icon">🏠</span>
          <span class="nav-text">Dashboard</span>
        </router-link>
        <div class="nav-dropdown" @mouseenter="showCoursesDropdown = true" @mouseleave="showCoursesDropdown = false">
          <div class="nav-link dropdown-trigger" :class="{ active: isCoursesActive }">
            <span class="nav-icon">📚</span>
            <span class="nav-text">My Courses</span>
            <span class="dropdown-arrow">▼</span>
          </div>
          <div class="dropdown-menu" v-if="showCoursesDropdown">
            <router-link v-for="course in lecturerCourses" :key="course.id" :to="`/lecturer/course/${course.id}`" class="dropdown-item">
              <span class="course-code">{{ course.code }}</span>
              <span class="course-name">{{ course.name }}</span>
            </router-link>
          </div>
        </div>
        <router-link to="/lecturer/student-management" class="nav-link" :class="{ active: $route.path === '/lecturer/student-management' }">
          <span class="nav-icon">👥</span>
          <span class="nav-text">Student Management</span>
        </router-link>
        <router-link to="/lecturer/analytics" class="nav-link" :class="{ active: $route.path === '/lecturer/analytics' }">
          <span class="nav-icon">📊</span>
          <span class="nav-text">Analytics</span>
        </router-link>
        <router-link to="/lecturer/notifications" class="nav-link" :class="{ active: $route.path === '/lecturer/notifications' }">
          <span class="nav-icon">🔔</span>
          <span class="nav-text">Notifications</span>
        </router-link>
      </div>

      <!-- User Dropdown -->
      <div class="navbar-user" v-if="!showMobileMenu">
        <div class="user-dropdown" @click.stop="toggleUserDropdown">
          <div class="user-avatar">{{ getLecturerInitials(lecturerName) }}</div>
          <div class="user-info">
            <span class="user-name">{{ lecturerName }}</span>
            <span class="user-role">Lecturer</span>
          </div>
          <span class="dropdown-arrow">▼</span>
          <div class="user-menu" v-if="showUserDropdown">
            <router-link to="/lecturer/profile" class="user-menu-item">👤 Profile</router-link>
            <router-link to="/lecturer/settings" class="user-menu-item">⚙️ Settings</router-link>
            <div class="user-menu-divider"></div>
            <button class="user-menu-item logout" @click="logout">🚪 Logout</button>
          </div>
        </div>
      </div>

      <!-- Hamburger for Mobile -->
      <button class="navbar-hamburger" @click="toggleMobileMenu">
        <span class="hamburger-bar"></span>
        <span class="hamburger-bar"></span>
        <span class="hamburger-bar"></span>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" v-if="showMobileMenu">
      <router-link to="/lecturer/dashboard" class="mobile-menu-item" @click="closeMobileMenu">🏠 Dashboard</router-link>
      <div class="mobile-menu-section">
        <div class="mobile-menu-title">My Courses</div>
        <router-link v-for="course in lecturerCourses" :key="course.id" :to="`/lecturer/course/${course.id}`" class="mobile-menu-item" @click="closeMobileMenu">
          <span class="course-code">{{ course.code }}</span> - <span class="course-name">{{ course.name }}</span>
        </router-link>
      </div>
      <router-link to="/lecturer/analytics" class="mobile-menu-item" @click="closeMobileMenu">📊 Analytics</router-link>
      <router-link to="/lecturer/notifications" class="mobile-menu-item" @click="closeMobileMenu">🔔 Notifications</router-link>
      <router-link to="/lecturer/profile" class="mobile-menu-item" @click="closeMobileMenu">👤 Profile</router-link>
      <router-link to="/lecturer/settings" class="mobile-menu-item" @click="closeMobileMenu">⚙️ Settings</router-link>
      <button class="mobile-menu-item logout" @click="logout">🚪 Logout</button>
    </div>
  </nav>
</template>

<script>
import authService from "@/services/auth";
import { lecturerAPI, utilityAPI } from "@/services/api";

export default {
  name: "LecturerNavbar",
  data() {
    return {
      showCoursesDropdown: false,
      showUserDropdown: false,
      showMobileMenu: false,
      lecturerName: "Loading...",
      lecturerId: null,
      lecturerCourses: [],
    };
  },
  computed: {
    isCoursesActive() {
      return this.$route.path.startsWith("/lecturer/course/");
    },
  },
  async mounted() {
    await this.loadLecturerData();
    document.addEventListener("click", this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
  },
  methods: {
    async loadLecturerData() {
      try {
        const userResponse = await utilityAPI.getUserProfile();
        if (userResponse.success) {
          this.lecturerName = userResponse.data.full_name;
          this.lecturerId = userResponse.data.id;
          const coursesResponse = await lecturerAPI.getCourses(this.lecturerId);
          if (coursesResponse.success) {
            this.lecturerCourses = coursesResponse.data.courses || [];
          }
        }
      } catch (error) {
        console.error("Error loading lecturer data:", error);
      }
    },
    getLecturerInitials(name) {
      return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
    },
    toggleUserDropdown() {
      this.showUserDropdown = !this.showUserDropdown;
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.showUserDropdown = false;
        this.showCoursesDropdown = false;
        this.showMobileMenu = false;
      }
    },
    toggleMobileMenu() {
      this.showMobileMenu = !this.showMobileMenu;
    },
    closeMobileMenu() {
      this.showMobileMenu = false;
    },
    async logout() {
      try {
        await authService.logout();
        this.$router.push("/login");
      } catch (error) {
        this.$router.push("/login");
      }
    },
  },
};
</script>

<style scoped>
.lecturer-navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 64px;
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  display: flex;
  align-items: center;
}

.navbar-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100%;
}

.navbar-brand {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: #1e293b;
  font-weight: 700;
  font-size: 1.2rem;
  gap: 0.75rem;
}
.brand-logo {
  font-size: 2rem;
}
.brand-title {
  font-size: 1.1rem;
  font-weight: 600;
}

.navbar-links {
  display: flex;
  align-items: center;
  gap: 1.2rem;
}
.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b;
  text-decoration: none;
  font-size: 1rem;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: background 0.2s, color 0.2s;
}
.nav-link.active, .nav-link:hover {
  background: #eff6ff;
  color: #2563eb;
}
.nav-icon {
  font-size: 1.2rem;
}

.nav-dropdown {
  position: relative;
}
.dropdown-trigger {
  cursor: pointer;
}
.dropdown-arrow {
  font-size: 0.8rem;
  margin-left: 4px;
}
.dropdown-menu {
  position: absolute;
  top: 110%;
  left: 0;
  min-width: 220px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  padding: 0.5rem 0;
  z-index: 2000;
  display: flex;
  flex-direction: column;
  animation: fadeIn 0.2s;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.dropdown-item {
  padding: 0.75rem 1.2rem;
  color: #1e293b;
  text-decoration: none;
  font-size: 0.97rem;
  border: none;
  background: none;
  text-align: left;
  transition: background 0.2s;
  cursor: pointer;
}
.dropdown-item:hover {
  background: #f1f5f9;
}
.course-code {
  font-weight: 600;
  color: #2563eb;
  margin-right: 0.5rem;
}
.course-name {
  color: #64748b;
}

.navbar-user {
  position: relative;
  display: flex;
  align-items: center;
}
.user-dropdown {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  cursor: pointer;
  position: relative;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  transition: background 0.2s;
}
.user-dropdown:hover {
  background: #f1f5f9;
}
.user-avatar {
  width: 2.2rem;
  height: 2.2rem;
  background: #2563eb;
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.1rem;
}
.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
.user-name {
  font-weight: 600;
  font-size: 0.97rem;
  color: #1e293b;
}
.user-role {
  font-size: 0.85rem;
  color: #64748b;
}
.dropdown-arrow {
  font-size: 0.8rem;
  color: #64748b;
}
.user-menu {
  position: absolute;
  top: 120%;
  right: 0;
  min-width: 180px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  padding: 0.5rem 0;
  z-index: 3000;
  display: flex;
  flex-direction: column;
  animation: fadeIn 0.2s;
}
.user-menu-item {
  padding: 0.7rem 1.2rem;
  color: #1e293b;
  text-decoration: none;
  font-size: 0.97rem;
  border: none;
  background: none;
  text-align: left;
  transition: background 0.2s;
  cursor: pointer;
}
.user-menu-item:hover {
  background: #f1f5f9;
}
.user-menu-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 0.3rem 0;
}
.user-menu-item.logout {
  color: #ef4444;
}
.user-menu-item.logout:hover {
  background: #fef2f2;
}

.navbar-hamburger {
  display: none;
  flex-direction: column;
  gap: 4px;
  background: none;
  border: none;
  cursor: pointer;
  margin-left: 1rem;
}
.hamburger-bar {
  width: 28px;
  height: 3px;
  background: #64748b;
  border-radius: 2px;
  transition: all 0.2s;
}

/* Mobile Styles */
@media (max-width: 900px) {
  .navbar-links {
    gap: 0.5rem;
  }
  .navbar-container {
    padding: 0 0.5rem;
  }
}
@media (max-width: 768px) {
  .navbar-links,
  .navbar-user {
    display: none;
  }
  .navbar-hamburger {
    display: flex;
  }
  .mobile-menu {
    position: fixed;
    top: 64px;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    z-index: 4000;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    animation: fadeIn 0.2s;
  }
  .mobile-menu-item {
    padding: 1.1rem 1.5rem;
    color: #1e293b;
    text-decoration: none;
    font-size: 1.05rem;
    border: none;
    background: none;
    text-align: left;
    transition: background 0.2s;
    cursor: pointer;
  }
  .mobile-menu-item:hover {
    background: #f1f5f9;
  }
  .mobile-menu-item.logout {
    color: #ef4444;
  }
  .mobile-menu-item.logout:hover {
    background: #fef2f2;
  }
  .mobile-menu-section {
    margin: 0.5rem 0;
  }
  .mobile-menu-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #64748b;
    margin: 0.5rem 1.5rem 0.2rem 1.5rem;
  }
}
</style>
