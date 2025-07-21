<template>
  <nav class="student-navbar">
    <div class="navbar-container">
      <!-- Logo and Brand -->
      <div class="navbar-brand">
        <router-link to="/student/dashboard" class="brand-link">
          <div class="brand-logo">🎓</div>
          <div class="brand-text">
            <span class="brand-name">StudentMarks</span>
            <span class="brand-subtitle">Student Portal</span>
          </div>
        </router-link>
      </div>

      <!-- Desktop Navigation Links -->
      <div class="navbar-nav">
        <router-link
          to="/student"
          class="nav-item"
          :class="{ active: $route.path === '/student/dashboard' }"
        >
          <div class="nav-icon">🏠</div>
          <span class="nav-text">Dashboard</span>
        </router-link>

        <div
          class="nav-dropdown"
          @mouseover="showCoursesDropdown = true"
          @mouseleave="showCoursesDropdown = false"
        >
          <div
            class="nav-item dropdown-trigger"
            :class="{ active: isCoursesActive }"
          >
            <div class="nav-icon">📚</div>
            <span class="nav-text">Courses</span>
            <div class="dropdown-arrow">▼</div>
          </div>
          <div class="dropdown-menu" :class="{ show: showCoursesDropdown }">
            <router-link
              v-for="course in enrolledCourses"
              :key="course.id"
              :to="`/student/course/${course.id}`"
              class="dropdown-item"
            >
              <div class="course-info">
                <span class="course-code">{{ course.code }}</span>
                <span class="course-name">{{ course.name }}</span>
              </div>
              <div
                class="course-grade"
                :class="getGradeClass(course.currentGrade)"
              >
                {{ course.currentGrade }}
              </div>
            </router-link>
          </div>
        </div>

        <router-link
          to="/student/performance-comparison"
          class="nav-item"
          :class="{ active: $route.path === '/student/performance-comparison' }"
        >
          <div class="nav-icon">📊</div>
          <span class="nav-text">Compare Performance</span>
        </router-link>

        <router-link
          to="/student/performance-trends"
          class="nav-item"
          :class="{ active: $route.path === '/student/performance-trends' }"
        >
          <div class="nav-icon">📈</div>
          <span class="nav-text">Trends</span>
        </router-link>

        <router-link
          to="/student/what-if-simulator"
          class="nav-item"
          :class="{ active: $route.path === '/student/what-if-simulator' }"
        >
          <div class="nav-icon">🎯</div>
          <span class="nav-text">Grade Simulator</span>
        </router-link>
      </div>

      <!-- Right Side - Notifications, Profile -->
      <div class="navbar-right">
        <!-- Notifications -->
        <div class="notification-dropdown" @click="toggleNotifications">
          <div class="notification-button">
            <div class="notification-icon">🔔</div>
            <div v-if="unreadNotifications > 0" class="notification-badge">
              {{ unreadNotifications }}
            </div>
          </div>
          <div class="notification-menu" :class="{ show: showNotifications }">
            <div class="notification-header">
              <h3>Notifications</h3>
              <button class="mark-all-read" @click="markAllAsRead">
                Mark all as read
              </button>
            </div>
            <div class="notification-list">
              <div
                v-for="notification in notifications"
                :key="notification.id"
                class="notification-item"
                :class="{ unread: !notification.read }"
                @click="markAsRead(notification.id)"
              >
                <div class="notification-icon-type" :class="notification.type">
                  {{ getNotificationIcon(notification.type) }}
                </div>
                <div class="notification-content">
                  <p class="notification-title">{{ notification.title }}</p>
                  <p class="notification-message">{{ notification.message }}</p>
                  <span class="notification-time">{{
                    formatTimeAgo(notification.timestamp)
                  }}</span>
                </div>
              </div>
            </div>
            <div class="notification-footer">
              <router-link
                to="/student/notifications"
                class="view-all-notifications"
              >
                View All Notifications
              </router-link>
            </div>
          </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="profile-dropdown" @click="toggleProfile">
          <div class="profile-button">
            <div class="profile-avatar">
              {{ getStudentInitials(studentName) }}
            </div>
            <div class="profile-info">
              <span class="profile-name">{{ studentName }}</span>
              <span class="profile-id">{{ studentId }}</span>
            </div>
            <div class="profile-arrow">▼</div>
          </div>
          <div class="profile-menu" :class="{ show: showProfile }">
            <div class="profile-header">
              <div class="profile-avatar-large">
                {{ getStudentInitials(studentName) }}
              </div>
              <div class="profile-details">
                <h4>{{ studentName }}</h4>
                <p>{{ studentId }}</p>
                <p>{{ studentProgram }}</p>
              </div>
            </div>
            <div class="profile-links">
              <router-link to="/student/profile" class="profile-link">
                <div class="link-icon">👤</div>
                <span>My Profile</span>
              </router-link>
              <router-link to="/student/settings" class="profile-link">
                <div class="link-icon">⚙️</div>
                <span>Settings</span>
              </router-link>
              <router-link to="/student/help" class="profile-link">
                <div class="link-icon">❓</div>
                <span>Help & Support</span>
              </router-link>
              <div class="profile-divider"></div>
              <button class="profile-link logout" @click="logout">
                <div class="link-icon">🚪</div>
                <span>Logout</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" @click="toggleMobileMenu">
          <div class="hamburger" :class="{ active: showMobileMenu }">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </button>
      </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div class="mobile-nav" :class="{ show: showMobileMenu }">
      <div class="mobile-nav-header">
        <div class="mobile-profile">
          <div class="mobile-avatar">
            {{ getStudentInitials(studentName) }}
          </div>
          <div class="mobile-profile-info">
            <h4>{{ studentName }}</h4>
            <p>{{ studentId }}</p>
          </div>
        </div>
      </div>

      <div class="mobile-nav-links">
        <router-link
          to="/student/dashboard"
          class="mobile-nav-item"
          @click="closeMobileMenu"
        >
          <div class="mobile-nav-icon">🏠</div>
          <span>Dashboard</span>
        </router-link>

        <div class="mobile-nav-section">
          <h5>My Courses</h5>
          <router-link
            v-for="course in enrolledCourses"
            :key="course.id"
            :to="`/student/course/${course.id}`"
            class="mobile-nav-item course-item"
            @click="closeMobileMenu"
          >
            <div class="mobile-course-info">
              <span class="mobile-course-code">{{ course.code }}</span>
              <span class="mobile-course-name">{{ course.name }}</span>
            </div>
            <div
              class="mobile-course-grade"
              :class="getGradeClass(course.currentGrade)"
            >
              {{ course.currentGrade }}
            </div>
          </router-link>
        </div>

        <router-link
          to="/student/performance-comparison"
          class="mobile-nav-item"
          @click="closeMobileMenu"
        >
          <div class="mobile-nav-icon">📊</div>
          <span>Compare Performance</span>
        </router-link>

        <router-link
          to="/student/performance-trends"
          class="mobile-nav-item"
          @click="closeMobileMenu"
        >
          <div class="mobile-nav-icon">📈</div>
          <span>Performance Trends</span>
        </router-link>

        <router-link
          to="/student/what-if-simulator"
          class="mobile-nav-item"
          @click="closeMobileMenu"
        >
          <div class="mobile-nav-icon">🎯</div>
          <span>Grade Simulator</span>
        </router-link>

        <div class="mobile-nav-divider"></div>

        <router-link
          to="/student/profile"
          class="mobile-nav-item"
          @click="closeMobileMenu"
        >
          <div class="mobile-nav-icon">👤</div>
          <span>My Profile</span>
        </router-link>

        <router-link
          to="/student/settings"
          class="mobile-nav-item"
          @click="closeMobileMenu"
        >
          <div class="mobile-nav-icon">⚙️</div>
          <span>Settings</span>
        </router-link>

        <button class="mobile-nav-item logout" @click="logout">
          <div class="mobile-nav-icon">🚪</div>
          <span>Logout</span>
        </button>
      </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div
      v-if="showMobileMenu"
      class="mobile-overlay"
      @click="closeMobileMenu"
    ></div>
  </nav>
</template>

<script>
import authService from "@/services/auth";
import { studentPerformanceAPI, utilityAPI } from "@/services/api";

export default {
  name: "StudentNavbar",
  data() {
    return {
      showCoursesDropdown: false,
      showNotifications: false,
      showProfile: false,
      showMobileMenu: false,
      studentName: "Loading...",
      studentId: "Loading...",
      studentProgram: "Loading...",
      enrolledCourses: [],
      notifications: [
        {
          id: 1,
          type: "grade",
          title: "New Grade Posted",
          message: "Your Assignment 2 grade for Web Technology has been posted",
          timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000),
          read: false,
        },
        {
          id: 2,
          type: "reminder",
          title: "Assignment Due Soon",
          message: "Database Systems Lab Exercise 3 is due in 2 days",
          timestamp: new Date(Date.now() - 4 * 60 * 60 * 1000),
          read: false,
        },
        {
          id: 3,
          type: "info",
          title: "Schedule Update",
          message: "Software Engineering class has been moved to Room 301",
          timestamp: new Date(Date.now() - 6 * 60 * 60 * 1000),
          read: true,
        },
      ],
    };
  },
  computed: {
    unreadNotifications() {
      return this.notifications.filter((n) => !n.read).length;
    },

    isCoursesActive() {
      return this.$route.path.includes("/student/course");
    },
  },
  async mounted() {
    await this.loadStudentData();

    // Close dropdowns when clicking outside
    document.addEventListener("click", (event) => {
      if (!this.$el.contains(event.target)) {
        this.showNotifications = false;
        this.showProfile = false;
        this.showCoursesDropdown = false;
      }
    });
  },
  methods: {
    async loadStudentData() {
      try {
        // Get user profile first
        const userResponse = await utilityAPI.getUserProfile();
        if (userResponse.success) {
          const userData = userResponse.data;

          // Update student info
          this.studentName = userData.full_name || "Student";
          this.studentId = userData.username || "N/A";
          this.studentProgram = "Computer Science"; // Could be fetched from backend

          // Load enrolled courses
          if (userData.role === "student" || userData.user_type === "student") {
            await this.loadEnrolledCourses(userData.id);
          }
        }
      } catch (error) {
        console.error("Error loading student data for navbar:", error);
      }
    },

    async loadEnrolledCourses(studentId) {
      try {
        const response = await studentPerformanceAPI.getStudentFullBreakdown(
          studentId
        );

        if (response.success) {
          const coursesData = response.data.courses || [];

          // Transform API data to match navbar expectations
          this.enrolledCourses = coursesData.map((course) => ({
            id: course.course_id,
            code: course.course_code,
            name: course.course_name,
            currentGrade: course.grade_letter || "N/A",
          }));
        }
      } catch (error) {
        console.error("Error loading enrolled courses for navbar:", error);
        // Keep empty array if API fails
        this.enrolledCourses = [];
      }
    },

    getStudentInitials(name) {
      return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
    },

    getGradeClass(grade) {
      const gradeMap = {
        "A+": "grade-a-plus",
        A: "grade-a",
        "A-": "grade-a-minus",
        "B+": "grade-b-plus",
        B: "grade-b",
        "B-": "grade-b-minus",
        "C+": "grade-c-plus",
        C: "grade-c",
      };
      return gradeMap[grade] || "grade-default";
    },

    getNotificationIcon(type) {
      const icons = {
        grade: "📊",
        reminder: "⏰",
        info: "ℹ️",
        alert: "⚠️",
      };
      return icons[type] || "ℹ️";
    },

    formatTimeAgo(timestamp) {
      const now = new Date();
      const diff = now - timestamp;
      const hours = Math.floor(diff / (1000 * 60 * 60));
      const days = Math.floor(hours / 24);

      if (days > 0) return `${days} day${days > 1 ? "s" : ""} ago`;
      if (hours > 0) return `${hours} hour${hours > 1 ? "s" : ""} ago`;
      return "Just now";
    },

    toggleNotifications() {
      this.showNotifications = !this.showNotifications;
      this.showProfile = false;
    },

    toggleProfile() {
      this.showProfile = !this.showProfile;
      this.showNotifications = false;
    },

    toggleMobileMenu() {
      this.showMobileMenu = !this.showMobileMenu;
    },

    closeMobileMenu() {
      this.showMobileMenu = false;
    },

    markAsRead(notificationId) {
      const notification = this.notifications.find(
        (n) => n.id === notificationId
      );
      if (notification) {
        notification.read = true;
      }
    },

    markAllAsRead() {
      this.notifications.forEach((n) => (n.read = true));
      this.showNotifications = false;
    },

    async logout() {
      try {
        console.log("Starting logout process...");
        console.log(
          "Before logout - isAuthenticated:",
          authService.isAuthenticated()
        );
        console.log("Before logout - token:", authService.getToken());
        console.log("Before logout - user:", authService.getCurrentUser());

        // Close any open dropdowns
        this.showProfile = false;
        this.showNotifications = false;
        this.showMobileMenu = false;

        // Call the auth service logout method
        await authService.logout();

        console.log(
          "After logout - isAuthenticated:",
          authService.isAuthenticated()
        );
        console.log("After logout - token:", authService.getToken());
        console.log("After logout - user:", authService.getCurrentUser());

        // Redirect to login page
        this.$router.push("/login");
      } catch (error) {
        console.error("Logout error:", error);
        // Even if there's an error, clear local data and redirect
        authService.clearAuthData();
        this.$router.push("/login");
      }
    },
  },
};
</script>

<style scoped>
.student-navbar {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.navbar-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  height: 70px;
}

/* Brand */
.navbar-brand {
  flex-shrink: 0;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  color: inherit;
}

.brand-logo {
  font-size: 2rem;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border-radius: 12px;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-name {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

.brand-subtitle {
  font-size: 0.8rem;
  color: #64748b;
}

/* Navigation */
.navbar-nav {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  justify-content: center;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 8px;
  text-decoration: none;
  color: #64748b;
  font-weight: 500;
  transition: all 0.2s;
  position: relative;
}

.nav-item:hover {
  background: #f1f5f9;
  color: #3b82f6;
}

.nav-item.active {
  background: #eff6ff;
  color: #3b82f6;
}

.nav-icon {
  font-size: 1.2rem;
}

.nav-text {
  font-size: 0.9rem;
  white-space: nowrap;
}

/* Dropdown */
.nav-dropdown {
  position: relative;
}

.dropdown-trigger {
  cursor: pointer;
}

.dropdown-arrow {
  font-size: 0.7rem;
  transition: transform 0.2s;
}

.nav-dropdown:hover .dropdown-arrow {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  min-width: 280px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.2s;
  z-index: 200;
}

.dropdown-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  text-decoration: none;
  color: #1e293b;
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.2s;
}

.dropdown-item:hover {
  background: #f8fafc;
}

.dropdown-item:last-child {
  border-bottom: none;
}

.course-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.course-code {
  font-weight: 700;
  font-size: 0.9rem;
}

.course-name {
  font-size: 0.8rem;
  color: #64748b;
}

.course-grade {
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
}

.course-grade.grade-a-plus,
.course-grade.grade-a {
  background: #dcfce7;
  color: #059669;
}

.course-grade.grade-a-minus,
.course-grade.grade-b-plus {
  background: #dbeafe;
  color: #3b82f6;
}

.course-grade.grade-b {
  background: #fef3c7;
  color: #d97706;
}

.course-grade.grade-b-minus,
.course-grade.grade-c-plus,
.course-grade.grade-c {
  background: #fee2e2;
  color: #dc2626;
}

/* Right Side */
.navbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

/* Notifications */
.notification-dropdown {
  position: relative;
}

.notification-button {
  position: relative;
  padding: 8px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
}

.notification-button:hover {
  background: #f1f5f9;
}

.notification-icon {
  font-size: 1.3rem;
  color: #64748b;
}

.notification-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  background: #ef4444;
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  width: 350px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.2s;
  z-index: 200;
}

.notification-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.notification-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid #e2e8f0;
}

.notification-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.mark-all-read {
  background: none;
  border: none;
  color: #3b82f6;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
}

.mark-all-read:hover {
  text-decoration: underline;
}

.notification-list {
  max-height: 300px;
  overflow-y: auto;
}

.notification-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 16px;
  border-bottom: 1px solid #f1f5f9;
  cursor: pointer;
  transition: background 0.2s;
}

.notification-item:hover {
  background: #f8fafc;
}

.notification-item.unread {
  background: #eff6ff;
}

.notification-icon-type {
  font-size: 1.2rem;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.notification-icon-type.grade {
  background: #dbeafe;
}

.notification-icon-type.reminder {
  background: #fef3c7;
}

.notification-icon-type.info {
  background: #f1f5f9;
}

.notification-content {
  flex: 1;
}

.notification-title {
  margin: 0 0 4px 0;
  font-weight: 600;
  font-size: 0.9rem;
  color: #1e293b;
}

.notification-message {
  margin: 0 0 4px 0;
  font-size: 0.8rem;
  color: #64748b;
  line-height: 1.4;
}

.notification-time {
  font-size: 0.7rem;
  color: #94a3b8;
}

.notification-footer {
  padding: 12px 16px;
  border-top: 1px solid #e2e8f0;
  text-align: center;
}

.view-all-notifications {
  color: #3b82f6;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 600;
}

.view-all-notifications:hover {
  text-decoration: underline;
}

/* Profile */
.profile-dropdown {
  position: relative;
}

.profile-button {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 4px 8px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
}

.profile-button:hover {
  background: #f1f5f9;
}

.profile-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1rem;
}

.profile-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.profile-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1e293b;
}

.profile-id {
  font-size: 0.8rem;
  color: #64748b;
}

.profile-arrow {
  font-size: 0.7rem;
  color: #64748b;
  transition: transform 0.2s;
}

.profile-dropdown:hover .profile-arrow {
  transform: rotate(180deg);
}

.profile-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  width: 280px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.2s;
  z-index: 200;
}

.profile-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px 16px;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}

.profile-avatar-large {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.2rem;
}

.profile-details h4 {
  margin: 0 0 4px 0;
  font-weight: 700;
  color: #1e293b;
}

.profile-details p {
  margin: 0;
  font-size: 0.8rem;
  color: #64748b;
}

.profile-links {
  padding: 8px 0;
}

.profile-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  text-decoration: none;
  color: #1e293b;
  transition: background 0.2s;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.profile-link:hover {
  background: #f8fafc;
}

.profile-link.logout {
  color: #dc2626;
}

.profile-link.logout:hover {
  background: #fef2f2;
}

.link-icon {
  font-size: 1.1rem;
  width: 20px;
  text-align: center;
}

.profile-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 8px 0;
}

/* Mobile */
.mobile-menu-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  transition: background 0.2s;
}

.mobile-menu-toggle:hover {
  background: #f1f5f9;
}

.hamburger {
  width: 24px;
  height: 18px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.hamburger span {
  width: 100%;
  height: 2px;
  background: #64748b;
  border-radius: 1px;
  transition: all 0.3s;
}

.hamburger.active span:nth-child(1) {
  transform: rotate(45deg) translate(6px, 6px);
}

.hamburger.active span:nth-child(2) {
  opacity: 0;
}

.hamburger.active span:nth-child(3) {
  transform: rotate(-45deg) translate(6px, -6px);
}

.mobile-nav {
  position: fixed;
  top: 70px;
  right: 0;
  width: 300px;
  height: calc(100vh - 70px);
  background: white;
  border-left: 1px solid #e2e8f0;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
  transform: translateX(100%);
  transition: transform 0.3s ease;
  overflow-y: auto;
  z-index: 150;
}

.mobile-nav.show {
  transform: translateX(0);
}

.mobile-nav-header {
  padding: 20px;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}

.mobile-profile {
  display: flex;
  align-items: center;
  gap: 16px;
}

.mobile-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.2rem;
}

.mobile-profile-info h4 {
  margin: 0 0 4px 0;
  font-weight: 700;
  color: #1e293b;
}

.mobile-profile-info p {
  margin: 0;
  font-size: 0.9rem;
  color: #64748b;
}

.mobile-nav-links {
  padding: 16px 0;
}

.mobile-nav-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 20px;
  text-decoration: none;
  color: #1e293b;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: background 0.2s;
}

.mobile-nav-item:hover {
  background: #f8fafc;
}

.mobile-nav-item.logout {
  color: #dc2626;
}

.mobile-nav-item.logout:hover {
  background: #fef2f2;
}

.mobile-nav-icon {
  font-size: 1.3rem;
  width: 24px;
  text-align: center;
}

.mobile-nav-section {
  margin: 16px 0;
}

.mobile-nav-section h5 {
  margin: 0 0 8px 0;
  padding: 0 20px;
  font-size: 0.8rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.mobile-nav-item.course-item {
  flex-direction: column;
  align-items: flex-start;
  gap: 8px;
}

.mobile-course-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  width: 100%;
}

.mobile-course-code {
  font-weight: 700;
  font-size: 0.9rem;
}

.mobile-course-name {
  font-size: 0.8rem;
  color: #64748b;
}

.mobile-course-grade {
  align-self: flex-end;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
}

.mobile-nav-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 16px 20px;
}

.mobile-overlay {
  position: fixed;
  top: 70px;
  left: 0;
  width: 100%;
  height: calc(100vh - 70px);
  background: rgba(0, 0, 0, 0.5);
  z-index: 140;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .navbar-nav {
    gap: 4px;
  }

  .nav-text {
    font-size: 0.8rem;
  }

  .profile-info {
    display: none;
  }
}

@media (max-width: 768px) {
  .navbar-container {
    padding: 0 16px;
  }

  .navbar-nav {
    display: none;
  }

  .notification-dropdown,
  .profile-dropdown {
    display: none;
  }

  .mobile-menu-toggle {
    display: block;
  }

  .brand-text {
    display: none;
  }

  .brand-logo {
    width: 40px;
    height: 40px;
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .navbar-container {
    height: 60px;
    padding: 0 12px;
  }

  .mobile-nav {
    width: 100%;
    top: 60px;
    height: calc(100vh - 60px);
    transform: translateX(100%);
  }

  .mobile-overlay {
    top: 60px;
    height: calc(100vh - 60px);
  }

  .notification-menu,
  .profile-menu {
    width: calc(100vw - 32px);
    left: 16px;
    right: 16px;
  }
}

/* Utility Classes */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

/* Smooth Scrolling */
.notification-list {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f8fafc;
}

.notification-list::-webkit-scrollbar {
  width: 6px;
}

.notification-list::-webkit-scrollbar-track {
  background: #f8fafc;
}

.notification-list::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}

.mobile-nav {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f8fafc;
}

.mobile-nav::-webkit-scrollbar {
  width: 6px;
}

.mobile-nav::-webkit-scrollbar-track {
  background: #f8fafc;
}

.mobile-nav::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 3px;
}

.mobile-nav::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}

/* Animation Classes */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-menu.show,
.notification-menu.show,
.profile-menu.show {
  animation: slideDown 0.2s ease;
}

/* Focus States for Accessibility */
.nav-item:focus,
.notification-button:focus,
.profile-button:focus,
.mobile-menu-toggle:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

.dropdown-item:focus,
.notification-item:focus,
.profile-link:focus,
.mobile-nav-item:focus {
  background: #eff6ff;
  outline: none;
}

/* Loading States */
.nav-item.loading {
  opacity: 0.6;
  pointer-events: none;
}

/* Custom Properties for Theming */
:root {
  --navbar-height: 70px;
  --navbar-mobile-height: 60px;
  --primary-color: #3b82f6;
  --primary-hover: #2563eb;
  --text-primary: #1e293b;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --background-primary: #ffffff;
  --background-secondary: #f8fafc;
  --background-hover: #f1f5f9;
  --border-color: #e2e8f0;
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
  --border-radius: 8px;
  --border-radius-lg: 12px;
}

/* Dark mode support (if needed in future) */
@media (prefers-color-scheme: dark) {
  .student-navbar {
    /* Dark mode styles can be added here */
  }
}
</style>
