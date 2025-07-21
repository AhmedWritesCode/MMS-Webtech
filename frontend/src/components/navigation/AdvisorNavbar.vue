<template>
  <nav class="advisor-navbar">
    <div class="navbar-container">
      <!-- Logo and Brand -->
      <div class="navbar-brand">
        <router-link to="/advisor/dashboard" class="brand-link">
          <div class="brand-logo">🎓</div>
          <div class="brand-text">
            <span class="brand-name">StudentMarks</span>
            <span class="brand-subtitle">Advisor Portal</span>
          </div>
        </router-link>
      </div>

      <!-- Desktop Navigation Links -->
      <div class="navbar-nav">
        <router-link
          to="/advisor"
          class="nav-item"
          :class="{ active: $route.path === '/advisor/dashboard' }"
        >
          <div class="nav-icon">🏠</div>
          <span class="nav-text">Dashboard</span>
        </router-link>

        <div
          class="nav-dropdown"
          @mouseover="showAdviseesDropdown = true"
          @mouseleave="showAdviseesDropdown = false"
        >
          <div
            class="nav-item dropdown-trigger"
            :class="{ active: isAdviseesActive }"
          >
            <div class="nav-icon">👥</div>
            <span class="nav-text">My Advisees</span>
            <span v-if="atRiskCount > 0" class="risk-badge">{{
              atRiskCount
            }}</span>
            <div class="dropdown-arrow">▼</div>
          </div>
          <div class="dropdown-menu" :class="{ show: showAdviseesDropdown }">
            <router-link to="/advisor/advisees" class="dropdown-item">
              <div class="dropdown-icon">📋</div>
              <span>All Advisees</span>
              <span class="advisee-count">{{ totalAdvisees }}</span>
            </router-link>
            <router-link
              to="/advisor/advisees?filter=at-risk"
              class="dropdown-item at-risk"
            >
              <div class="dropdown-icon">⚠️</div>
              <span>At-Risk Students</span>
              <span class="risk-count">{{ atRiskCount }}</span>
            </router-link>
            <div class="dropdown-divider"></div>
            <div class="dropdown-section-title">Recent Students</div>
            <router-link
              v-for="student in recentStudents"
              :key="student.id"
              :to="`/advisor/advisee/${student.id}`"
              class="dropdown-item student-item"
            >
              <div class="student-avatar">
                {{ getStudentInitials(student.name) }}
              </div>
              <div class="student-info">
                <span class="student-name">{{ student.name }}</span>
                <span
                  class="student-status"
                  :class="getStatusClass(student.gpa)"
                >
                  GPA: {{ student.gpa }}
                </span>
              </div>
            </router-link>
          </div>
        </div>

        <router-link
          to="/advisor/analytics"
          class="nav-item"
          :class="{ active: $route.path === '/advisor/analytics' }"
        >
          <div class="nav-icon">📊</div>
          <span class="nav-text">Analytics</span>
        </router-link>

        <div
          class="nav-dropdown"
          @mouseover="showReportsDropdown = true"
          @mouseleave="showReportsDropdown = false"
        >
          <div
            class="nav-item dropdown-trigger"
            :class="{ active: isReportsActive }"
          >
            <div class="nav-icon">📈</div>
            <span class="nav-text">Reports</span>
            <div class="dropdown-arrow">▼</div>
          </div>
          <div class="dropdown-menu" :class="{ show: showReportsDropdown }">
            <router-link to="/advisor/reports/progress" class="dropdown-item">
              <div class="dropdown-icon">📊</div>
              <span>Progress Reports</span>
            </router-link>
            <router-link
              to="/advisor/reports/consultation"
              class="dropdown-item"
            >
              <div class="dropdown-icon">📝</div>
              <span>Consultation Reports</span>
            </router-link>
            <router-link to="/advisor/reports/semester" class="dropdown-item">
              <div class="dropdown-icon">📅</div>
              <span>Semester Summary</span>
            </router-link>
            <router-link to="/advisor/reports/export" class="dropdown-item">
              <div class="dropdown-icon">📤</div>
              <span>Export Data</span>
            </router-link>
          </div>
        </div>
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
                :class="{
                  unread: !notification.read,
                  [notification.priority]: true,
                }"
                @click="handleNotification(notification)"
              >
                <div class="notification-icon-type" :class="notification.type">
                  {{ getNotificationIcon(notification.type) }}
                </div>
                <div class="notification-content">
                  <p class="notification-title">{{ notification.title }}</p>
                  <p class="notification-message">{{ notification.message }}</p>
                  <div class="notification-meta">
                    <span class="notification-time">{{
                      formatTimeAgo(notification.timestamp)
                    }}</span>
                    <span
                      v-if="notification.studentName"
                      class="notification-student"
                    >
                      {{ notification.studentName }}
                    </span>
                  </div>
                </div>
                <div
                  v-if="notification.priority === 'urgent'"
                  class="urgent-indicator"
                >
                  !
                </div>
              </div>
            </div>
            <div class="notification-footer">
              <router-link
                to="/advisor/notifications"
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
              {{ getAdvisorInitials(advisorName) }}
            </div>
            <div class="profile-info">
              <span class="profile-name">{{ advisorName }}</span>
              <span class="profile-role">{{ advisorRole }}</span>
            </div>
            <div class="profile-arrow">▼</div>
          </div>
          <div class="profile-menu" :class="{ show: showProfile }">
            <div class="profile-header">
              <div class="profile-avatar-large">
                {{ getAdvisorInitials(advisorName) }}
              </div>
              <div class="profile-details">
                <h4>{{ advisorName }}</h4>
                <p>{{ advisorId }}</p>
                <p>{{ department }}</p>
                <div class="advisor-stats">
                  <span>{{ totalAdvisees }} Advisees</span>
                </div>
              </div>
            </div>
            <div class="profile-links">
              <router-link to="/advisor/profile" class="profile-link">
                <div class="link-icon">👤</div>
                <span>My Profile</span>
              </router-link>
              <router-link to="/advisor/settings" class="profile-link">
                <div class="link-icon">⚙️</div>
                <span>Settings</span>
              </router-link>
              <router-link to="/advisor/resources" class="profile-link">
                <div class="link-icon">📚</div>
                <span>Resources</span>
              </router-link>
              <router-link to="/advisor/help" class="profile-link">
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
            {{ getAdvisorInitials(advisorName) }}
          </div>
          <div class="mobile-profile-info">
            <h4>{{ advisorName }}</h4>
            <p>{{ advisorRole }}</p>
            <div class="mobile-stats">
              <span>{{ totalAdvisees }} Advisees</span>
              <span v-if="atRiskCount > 0" class="mobile-risk"
                >{{ atRiskCount }} At Risk</span
              >
            </div>
          </div>
        </div>
      </div>

      <div class="mobile-nav-links">
        <router-link
          to="/advisor/dashboard"
          class="mobile-nav-item"
          @click="closeMobileMenu"
        >
          <div class="mobile-nav-icon">🏠</div>
          <span>Dashboard</span>
        </router-link>

        <div class="mobile-nav-section">
          <h5>My Advisees</h5>
          <router-link
            to="/advisor/advisees"
            class="mobile-nav-item"
            @click="closeMobileMenu"
          >
            <div class="mobile-nav-icon">👥</div>
            <span>All Advisees ({{ totalAdvisees }})</span>
          </router-link>

          <router-link
            to="/advisor/advisees?filter=at-risk"
            class="mobile-nav-item at-risk"
            @click="closeMobileMenu"
          >
            <div class="mobile-nav-icon">⚠️</div>
            <span>At-Risk Students</span>
            <span v-if="atRiskCount > 0" class="mobile-risk-badge">{{
              atRiskCount
            }}</span>
          </router-link>
        </div>

        <router-link
          to="/advisor/analytics"
          class="mobile-nav-item"
          @click="closeMobileMenu"
        >
          <div class="mobile-nav-icon">📊</div>
          <span>Analytics</span>
        </router-link>

        <div class="mobile-nav-section">
          <h5>Reports</h5>
          <router-link
            to="/advisor/reports/progress"
            class="mobile-nav-item"
            @click="closeMobileMenu"
          >
            <div class="mobile-nav-icon">📊</div>
            <span>Progress Reports</span>
          </router-link>

          <router-link
            to="/advisor/reports/consultation"
            class="mobile-nav-item"
            @click="closeMobileMenu"
          >
            <div class="mobile-nav-icon">📝</div>
            <span>Consultation Reports</span>
          </router-link>
        </div>

        <div class="mobile-nav-divider"></div>

        <router-link
          to="/advisor/profile"
          class="mobile-nav-item"
          @click="closeMobileMenu"
        >
          <div class="mobile-nav-icon">👤</div>
          <span>My Profile</span>
        </router-link>

        <router-link
          to="/advisor/settings"
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
import { advisorAPI, utilityAPI } from "@/services/api";

export default {
  name: "AdvisorNavbar",
  data() {
    return {
      showAdviseesDropdown: false,
      showReportsDropdown: false,
      showNotifications: false,
      showProfile: false,
      showMobileMenu: false,
      advisorName: "",
      advisorId: "",
      advisorRole: "Academic Advisor",
      department: "",
      totalAdvisees: 0,
      atRiskCount: 0,
      recentStudents: [],
      notifications: [],
    };
  },
  computed: {
    unreadNotifications() {
      return this.notifications.filter((n) => !n.read).length;
    },

    isAdviseesActive() {
      return this.$route.path.includes("/advisor/advisee");
    },

    isReportsActive() {
      return this.$route.path.includes("/advisor/reports");
    },
  },
  methods: {
    getAdvisorInitials(name) {
      return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
    },

    getStudentInitials(name) {
      return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
    },

    getStatusClass(gpa) {
      if (gpa >= 3.0) return "status-good";
      if (gpa >= 2.0) return "status-warning";
      return "status-danger";
    },

    getNotificationIcon(type) {
      const icons = {
        alert: "⚠️",
        meeting: "📅",
        info: "ℹ️",
        grade: "📊",
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

    toggleAdviseesDropdown() {
      this.showAdviseesDropdown = !this.showAdviseesDropdown;
      this.showReportsDropdown = false;
    },

    toggleReportsDropdown() {
      this.showReportsDropdown = !this.showReportsDropdown;
      this.showAdviseesDropdown = false;
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

    handleNotification(notification) {
      this.markAsRead(notification.id);

      // Handle different notification actions
      switch (notification.action) {
        case "view_student":
          if (notification.studentName) {
            // Find student ID and navigate
            const student = this.recentStudents.find(
              (s) => s.name === notification.studentName
            );
            if (student) {
              this.$router.push(`/advisor/advisee/${student.id}`);
            }
          }
          break;
        case "view_reports":
          this.$router.push("/advisor/reports");
          break;
      }

      this.showNotifications = false;
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
        // Close any open dropdowns
        this.showProfile = false;
        this.showNotifications = false;
        this.showMobileMenu = false;
        this.showAdviseesDropdown = false;
        this.showReportsDropdown = false;

        // Call the auth service logout method
        await authService.logout();

        // Redirect to login page
        this.$router.push("/login");
      } catch (error) {
        console.error("Logout error:", error);
        // Even if there's an error, clear local data and redirect
        authService.clearAuthData();
        this.$router.push("/login");
      }
    },

    async loadAdvisorProfile() {
      try {
        const response = await utilityAPI.getUserProfile();
        if (response.success && response.data.role === "advisor") {
          this.advisorId = response.data.id;
          this.advisorName = response.data.full_name || "Advisor";
          this.department = response.data.department || "";
        }
      } catch (error) {
        console.error("Error loading advisor profile:", error);
      }
    },

    async loadAdvisees() {
      try {
        if (!this.advisorId) return;
        const response = await advisorAPI.getAdvisees(this.advisorId);
        if (response.success && response.data && response.data.advisees) {
          const advisees = response.data.advisees;
          this.totalAdvisees = advisees.length;
          this.atRiskCount = advisees.filter(
            (a) => a.risk_level === "High" || a.risk_level === "Critical"
          ).length;
          // Show up to 3 most recent advisees (sorted by assigned_date desc if available)
          this.recentStudents = advisees.slice(0, 3).map((a) => ({
            id: a.id,
            name: a.full_name,
            gpa: parseFloat(a.current_gpa) || 0.0,
          }));
        }
      } catch (error) {
        console.error("Error loading advisees in navbar:", error);
      }
    },
  },
  async mounted() {
    await this.loadAdvisorProfile();
    await this.loadAdvisees();
    // Close dropdowns when clicking outside
    document.addEventListener("click", (event) => {
      if (!this.$el.contains(event.target)) {
        this.showNotifications = false;
        this.showProfile = false;
        this.showAdviseesDropdown = false;
        this.showReportsDropdown = false;
      }
    });
  },
};
</script>

<style scoped>
.advisor-navbar {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.navbar-container {
  max-width: 1400px;
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
  background: linear-gradient(135deg, #059669, #047857);
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
  color: #059669;
  font-weight: 600;
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
  background: #f0fdf4;
  color: #059669;
}

.nav-item.active {
  background: #dcfce7;
  color: #059669;
}

.nav-icon {
  font-size: 1.2rem;
}

.nav-text {
  font-size: 0.9rem;
  white-space: nowrap;
}

/* Badges */
.risk-badge,
.meeting-badge {
  font-size: 0.7rem;
  padding: 2px 6px;
  border-radius: 10px;
  font-weight: 700;
  color: white;
  min-width: 16px;
  text-align: center;
}

.risk-badge {
  background: #ef4444;
}

.meeting-badge {
  background: #3b82f6;
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
  min-width: 300px;
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
  gap: 12px;
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

.dropdown-item.at-risk {
  background: #fef2f2;
  border-left: 3px solid #ef4444;
}

.dropdown-item.at-risk:hover {
  background: #fee2e2;
}

.dropdown-icon {
  font-size: 1.1rem;
  width: 20px;
  text-align: center;
}

.advisee-count,
.risk-count {
  margin-left: auto;
  background: #f1f5f9;
  color: #64748b;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.risk-count {
  background: #fee2e2;
  color: #dc2626;
}

.dropdown-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 8px 0;
}

.dropdown-section-title {
  padding: 8px 16px 4px 16px;
  font-size: 0.8rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.student-item {
  align-items: center;
  gap: 12px;
}

.student-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.8rem;
  flex-shrink: 0;
}

.student-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}

.student-name {
  font-weight: 600;
  font-size: 0.9rem;
}

.student-status {
  font-size: 0.8rem;
  font-weight: 600;
}

.student-status.status-good {
  color: #059669;
}

.student-status.status-warning {
  color: #f59e0b;
}

.student-status.status-danger {
  color: #ef4444;
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
  width: 380px;
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
  color: #059669;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
}

.mark-all-read:hover {
  text-decoration: underline;
}

.notification-list {
  max-height: 350px;
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
  position: relative;
}

.notification-item:hover {
  background: #f8fafc;
}

.notification-item.unread {
  background: #f0fdf4;
  border-left: 3px solid #059669;
}

.notification-item.urgent {
  background: #fef2f2;
  border-left: 3px solid #ef4444;
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

.notification-icon-type.alert {
  background: #fee2e2;
}

.notification-icon-type.meeting {
  background: #dbeafe;
}

.notification-icon-type.grade {
  background: #dcfce7;
}

.notification-icon-type.system {
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
  margin: 0 0 6px 0;
  font-size: 0.8rem;
  color: #64748b;
  line-height: 1.4;
}

.notification-meta {
  display: flex;
  gap: 8px;
  align-items: center;
}

.notification-time {
  font-size: 0.7rem;
  color: #94a3b8;
}

.notification-student {
  font-size: 0.7rem;
  color: #059669;
  font-weight: 600;
}

.urgent-indicator {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #ef4444;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  font-weight: 700;
}

.notification-footer {
  padding: 12px 16px;
  border-top: 1px solid #e2e8f0;
  text-align: center;
}

.view-all-notifications {
  color: #059669;
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
  background: linear-gradient(135deg, #059669, #047857);
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

.profile-role {
  font-size: 0.8rem;
  color: #059669;
  font-weight: 500;
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
  width: 300px;
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
  background: linear-gradient(135deg, #059669, #047857);
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

.advisor-stats {
  margin-top: 4px;
}

.advisor-stats span {
  font-size: 0.8rem;
  color: #059669;
  font-weight: 600;
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
  width: 320px;
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
  background: linear-gradient(135deg, #059669, #047857);
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
  margin: 0 0 4px 0;
  font-size: 0.9rem;
  color: #64748b;
}

.mobile-stats {
  display: flex;
  gap: 12px;
  font-size: 0.8rem;
}

.mobile-stats span {
  color: #059669;
  font-weight: 600;
}

.mobile-risk {
  color: #ef4444;
  font-weight: 700;
}

.mobile-nav-links {
  padding: 16px 0;
}

.mobile-nav-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
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

.mobile-nav-item.at-risk {
  background: #fef2f2;
  border-left: 3px solid #ef4444;
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
  flex-shrink: 0;
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

.mobile-risk-badge,
.mobile-meeting-badge {
  font-size: 0.7rem;
  padding: 2px 6px;
  border-radius: 10px;
  font-weight: 700;
  color: white;
  min-width: 16px;
  text-align: center;
}

.mobile-risk-badge {
  background: #ef4444;
}

.mobile-meeting-badge {
  background: #3b82f6;
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
@media (max-width: 1200px) {
  .navbar-nav {
    gap: 4px;
  }

  .nav-text {
    font-size: 0.8rem;
  }

  .quick-actions {
    display: none;
  }
}

@media (max-width: 1024px) {
  .profile-info {
    display: none;
  }

  .dropdown-menu {
    min-width: 250px;
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

/* Scrollbar Styles */
.notification-list,
.mobile-nav {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f8fafc;
}

.notification-list::-webkit-scrollbar,
.mobile-nav::-webkit-scrollbar {
  width: 6px;
}

.notification-list::-webkit-scrollbar-track,
.mobile-nav::-webkit-scrollbar-track {
  background: #f8fafc;
}

.notification-list::-webkit-scrollbar-thumb,
.mobile-nav::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb:hover,
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

/* Focus States */
.nav-item:focus,
.notification-button:focus,
.profile-button:focus,
.mobile-menu-toggle:focus,
.quick-action-btn:focus {
  outline: 2px solid #059669;
  outline-offset: 2px;
}

.dropdown-item:focus,
.notification-item:focus,
.profile-link:focus,
.mobile-nav-item:focus {
  background: #f0fdf4;
  outline: none;
}

/* Custom Properties */
:root {
  --advisor-primary: #059669;
  --advisor-primary-hover: #047857;
  --advisor-secondary: #10b981;
  --advisor-background: #f0fdf4;
  --advisor-border: #dcfce7;
}
</style>
