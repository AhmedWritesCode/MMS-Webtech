<template>
  <nav class="admin-navbar">
    <div class="navbar-container">
      <!-- Logo and Brand -->
      <div class="navbar-brand">
        <router-link to="/admin/dashboard" class="brand-link">
          <div class="brand-logo">🛡️</div>
          <div class="brand-text">
            <span class="brand-name">StudentMarks</span>
            <span class="brand-subtitle">Admin Portal</span>
          </div>
        </router-link>
      </div>

      <!-- Desktop Navigation Links -->
      <div class="navbar-nav">
        <router-link to="/admin/dashboard" class="nav-item" :class="{ active: $route.path === '/admin/dashboard' }">
          <div class="nav-icon">🏠</div>
          <span class="nav-text">Dashboard</span>
        </router-link>
        <router-link to="/admin/users" class="nav-item" :class="{ active: $route.path.startsWith('/admin/users') }">
          <div class="nav-icon">👥</div>
          <span class="nav-text">User Management</span>
        </router-link>
        <router-link to="/admin/courses" class="nav-item" :class="{ active: $route.path.startsWith('/admin/courses') }">
          <div class="nav-icon">📚</div>
          <span class="nav-text">Course Management</span>
        </router-link>
        <router-link to="/admin/assign-lecturers" class="nav-item" :class="{ active: $route.path.startsWith('/admin/assign-lecturers') }">
          <div class="nav-icon">👨‍🏫</div>
          <span class="nav-text">Assign Lecturers</span>
        </router-link>
        <router-link to="/admin/remark-requests" class="nav-item" :class="{ active: $route.path.startsWith('/admin/remark-requests') }">
          <div class="nav-icon">📝</div>
          <span class="nav-text">Remark Requests</span>
        </router-link>
        <router-link to="/admin/system-logs" class="nav-item" :class="{ active: $route.path.startsWith('/admin/system-logs') }">
          <div class="nav-icon">🗂️</div>
          <span class="nav-text">System Logs</span>
        </router-link>
        <router-link to="/admin/notifications" class="nav-item" :class="{ active: $route.path.startsWith('/admin/notifications') }">
          <div class="nav-icon">🔔</div>
          <span class="nav-text">Notifications</span>
        </router-link>
      </div>

      <!-- Right Side - Profile Dropdown -->
      <div class="navbar-right">
        <div class="profile-dropdown" @click.stop="toggleProfile">
          <div class="profile-button">
            <div class="profile-avatar">{{ getAdminInitials(adminName) }}</div>
            <div class="profile-info">
              <span class="profile-name">{{ adminName }}</span>
              <span class="profile-role">Admin</span>
            </div>
            <div class="profile-arrow">▼</div>
          </div>
          <div class="profile-menu" :class="{ show: showProfile }">
            <div class="profile-header">
              <div class="profile-avatar-large">{{ getAdminInitials(adminName) }}</div>
              <div class="profile-details">
                <h4>{{ adminName }}</h4>
                <p>Admin</p>
              </div>
            </div>
            <div class="profile-links">
              <router-link to="/admin/profile" class="profile-link">
                <div class="link-icon">👤</div>
                <span>My Profile</span>
              </router-link>
              <router-link to="/admin/settings" class="profile-link">
                <div class="link-icon">⚙️</div>
                <span>Settings</span>
              </router-link>
              <div class="profile-divider"></div>
              <button class="profile-link logout" @click="handleLogout">
                <div class="link-icon">🚪</div>
                <span>Logout</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { authAPI } from '@/services/api';
import authService from '@/services/auth';

export default {
  data() {
    return {
      showProfile: false,
      adminName: 'Admin User',
    };
  },
  mounted() {
    document.addEventListener('click', this.closeProfileDropdown);
    const user = authService.getCurrentUser();
    if (user) {
      this.adminName = user.full_name || user.username;
    }
  },
  beforeUnmount() {
    document.removeEventListener('click', this.closeProfileDropdown);
  },
  methods: {
    toggleProfile(event) {
      event.stopPropagation();
      this.showProfile = !this.showProfile;
      console.log('toggleProfile called, showProfile:', this.showProfile);
    },
    closeProfileDropdown(event) {
      if (!event.target.closest('.profile-dropdown')) {
        this.showProfile = false;
      }
    },
    async handleLogout() {
      try {
        await authAPI.logout();
      } catch (error) {
        console.error('Error during server logout:', error);
      } finally {
        authService.clearAuthData();
        this.$router.push('/login');
      }
    },
    getAdminInitials(name) {
      if (!name) return 'A';
      return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase();
    },
  },
};
</script>

<style scoped>
.admin-navbar {
  background: #2c3e50;
  color: #fff;
  box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.07);
  padding: clamp(0.5rem, 1vw, 1rem) 0;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  font-size: clamp(14px, 1vw, 16px);
}

.navbar-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: min(95vw, 1400px);
  margin: 0 auto;
  padding: 0 clamp(1rem, 2vw, 2rem);
  overflow-x: auto;
  -ms-overflow-style: none;
  scrollbar-width: none;
  gap: clamp(1rem, 2vw, 2rem);
}

.navbar-container::-webkit-scrollbar {
  display: none;
}

.navbar-brand {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.brand-link {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: inherit;
  white-space: nowrap;
  gap: clamp(0.5rem, 1vw, 0.75rem);
}

.brand-logo {
  font-size: clamp(1.5rem, 2vw, 2rem);
  flex-shrink: 0;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-name {
  font-weight: bold;
  font-size: clamp(1rem, 1.2vw, 1.2rem);
}

.brand-subtitle {
  font-size: clamp(0.75rem, 0.9vw, 0.9rem);
  color: #b0bec5;
}

.navbar-nav {
  display: flex;
  align-items: center;
  gap: clamp(0.5rem, 1vw, 1rem);
  margin: 0 clamp(1rem, 2vw, 2rem);
  overflow-x: auto;
  -ms-overflow-style: none;
  scrollbar-width: none;
  flex-grow: 1;
  justify-content: center;
}

.navbar-nav::-webkit-scrollbar {
  display: none;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: clamp(0.25rem, 0.5vw, 0.5rem);
  color: #fff;
  text-decoration: none;
  font-size: clamp(0.8rem, 0.9vw, 1rem);
  padding: clamp(0.4rem, 0.6vw, 0.6rem) clamp(0.6rem, 0.8vw, 1rem);
  border-radius: 6px;
  transition: all 0.2s ease;
  white-space: nowrap;
  flex-shrink: 0;
  background: transparent;
}

.nav-item.active {
  background: #34495e;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.nav-item:hover {
  background: #34495e;
  transform: translateY(-1px);
}

.nav-icon {
  font-size: clamp(1rem, 1.2vw, 1.2rem);
  flex-shrink: 0;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: clamp(1rem, 1.5vw, 1.5rem);
  flex-shrink: 0;
}

.profile-dropdown {
  position: relative;
  cursor: pointer;
}

.profile-button {
  display: flex;
  align-items: center;
  gap: clamp(0.5rem, 0.75vw, 0.75rem);
  padding: clamp(0.4rem, 0.5vw, 0.5rem);
  border-radius: 6px;
  transition: all 0.2s ease;
}

.profile-button:hover {
  background: #34495e;
  transform: translateY(-1px);
}

.profile-avatar {
  background: #607d8b;
  color: #fff;
  border-radius: 50%;
  width: clamp(2rem, 2.2vw, 2.4rem);
  height: clamp(2rem, 2.2vw, 2.4rem);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  flex-shrink: 0;
  font-size: clamp(0.9rem, 1vw, 1.1rem);
}

.profile-info {
  display: none;
}

@media (min-width: 640px) {
  .profile-info {
    display: flex;
    flex-direction: column;
  }
}

@media (max-width: 1024px) {
  .nav-text {
    display: none;
  }
  
  .nav-item {
    padding: clamp(0.4rem, 0.6vw, 0.6rem);
  }
  
  .nav-icon {
    font-size: clamp(1.2rem, 1.4vw, 1.4rem);
  }
}

@media (max-width: 768px) {
  .brand-subtitle {
    display: none;
  }
}

@media (max-width: 480px) {
  .navbar-container {
    padding: 0 0.5rem;
  }
  
  .brand-text {
    display: none;
  }
}

.profile-name {
  font-weight: 600;
  font-size: clamp(0.8rem, 0.9vw, 0.9rem);
  white-space: nowrap;
}

.profile-role {
  font-size: clamp(0.7rem, 0.8vw, 0.8rem);
  color: #b0bec5;
}

.profile-arrow {
  font-size: clamp(0.7rem, 0.8vw, 0.8rem);
  color: #b0bec5;
}

.profile-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.15);
  min-width: clamp(200px, 25vw, 280px);
  margin-top: 0.5rem;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px) scale(0.95);
  transition: all 0.2s ease;
  transform-origin: top right;
  display: block;
  pointer-events: none;
  border: 1px solid #ccc;
}

.profile-menu.show {
  opacity: 1 !important;
  visibility: visible !important;
  pointer-events: auto !important;
  display: block !important;
  position: fixed !important;
  top: 80px !important;
  right: 40px !important;
  left: auto !important;
  z-index: 99999 !important;
  background: #fff !important;
  color: #000 !important;
  border: 3px solid red !important;
  min-width: 300px !important;
  min-height: 200px !important;
}

.profile-header {
  padding: clamp(0.8rem, 1vw, 1rem);
  border-bottom: 1px solid #eee;
  display: flex;
  align-items: center;
  gap: clamp(0.8rem, 1vw, 1rem);
}

.profile-avatar-large {
  background: #607d8b;
  color: #fff;
  border-radius: 50%;
  width: clamp(2.5rem, 3vw, 3rem);
  height: clamp(2.5rem, 3vw, 3rem);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: clamp(1rem, 1.2vw, 1.2rem);
}

.profile-details h4 {
  color: #2c3e50;
  margin: 0;
  font-size: clamp(0.9rem, 1vw, 1rem);
}

.profile-details p {
  color: #7f8c8d;
  margin: 0;
  font-size: clamp(0.8rem, 0.9vw, 0.9rem);
}

.profile-links {
  padding: clamp(0.4rem, 0.5vw, 0.5rem);
}

.profile-link {
  display: flex;
  align-items: center;
  gap: clamp(0.6rem, 0.75vw, 0.75rem);
  padding: clamp(0.6rem, 0.75vw, 0.75rem);
  color: #2c3e50;
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.2s ease;
  font-size: clamp(0.85rem, 0.95vw, 0.95rem);
}

.profile-link:hover {
  background: #f5f6fa;
  transform: translateX(2px);
}

.profile-divider {
  height: 1px;
  background: #eee;
  margin: clamp(0.4rem, 0.5vw, 0.5rem) 0;
}

.link-icon {
  font-size: clamp(1.1rem, 1.2vw, 1.2rem);
}

button.profile-link {
  width: 100%;
  border: none;
  background: none;
  font: inherit;
  cursor: pointer;
  text-align: left;
}

button.profile-link.logout {
  color: #e74c3c;
}

button.profile-link.logout:hover {
  background: #fde8e7;
}

/* Add margin to main content to account for fixed navbar */
:global(#app) {
  padding-top: clamp(3.5rem, 4vw, 4rem);
}

/* Smooth scaling animation for nav items on hover */
.nav-item, .profile-button {
  transform-origin: center;
  transition: transform 0.2s ease, background-color 0.2s ease;
}

.nav-item:hover, .profile-button:hover {
  transform: scale(1.05);
}

/* Add subtle animation to icons */
.nav-icon, .link-icon {
  transition: transform 0.2s ease;
}

.nav-item:hover .nav-icon,
.profile-link:hover .link-icon {
  transform: scale(1.1);
}
</style> 