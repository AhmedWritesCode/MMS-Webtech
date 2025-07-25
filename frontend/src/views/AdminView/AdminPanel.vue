<template>
  <div class="admin-dashboard">
    <h2>🎓 Admin Dashboard</h2>

    <!-- Overview Cards -->
    <div class="overview-cards">
      <div class="card">
        <div class="card-icon">👥</div>
        <div class="card-content">
          <h3>Total Users</h3>
          <div class="card-value">{{ stats.totalUsers }}</div>
          <div class="card-breakdown">
            <span>Students: {{ stats.studentCount }}</span>
            <span>Lecturers: {{ stats.lecturerCount }}</span>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-icon">📚</div>
        <div class="card-content">
          <h3>Active Courses</h3>
          <div class="card-value">{{ stats.activeCourses }}</div>
          <div class="card-breakdown">
            <span>This Semester: {{ stats.currentSemesterCourses }}</span>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-icon">📊</div>
        <div class="card-content">
          <h3>System Health</h3>
          <div class="card-value">{{ systemHealth.status }}</div>
          <div class="card-breakdown">
            <span>Last Check: {{ systemHealth.lastCheck }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <h3>Quick Actions</h3>
      <div class="action-buttons">
        <router-link to="/admin/users" class="action-btn">
          <span class="icon">👥</span>
          Manage Users
        </router-link>
        <router-link to="/admin/courses" class="action-btn">
          <span class="icon">📚</span>
          Manage Courses
        </router-link>
        <router-link to="/admin/enrollments" class="action-btn">
          <span class="icon">📝</span>
          Course Enrollments
        </router-link>
        <router-link to="/admin/logs" class="action-btn">
          <span class="icon">📋</span>
          System Logs
        </router-link>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
      <h3>Recent Activity</h3>
      <div class="activity-list">
        <div v-for="activity in recentActivities" :key="activity.id" class="activity-item">
          <div class="activity-icon" :class="activity.type">{{ activity.icon }}</div>
          <div class="activity-details">
            <div class="activity-message">{{ activity.message }}</div>
            <div class="activity-time">{{ activity.time }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- System Notifications -->
    <div class="system-notifications">
      <h3>System Notifications</h3>
      <div class="notification-list">
        <div v-for="notification in notifications" :key="notification.id" 
             class="notification-item" :class="notification.priority">
          <div class="notification-content">
            <div class="notification-title">{{ notification.title }}</div>
            <div class="notification-message">{{ notification.message }}</div>
          </div>
          <button @click="dismissNotification(notification.id)" class="dismiss-btn">✕</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/services/api";

export default {
  data() {
    return {
      stats: {
        totalUsers: 0,
        studentCount: 0,
        lecturerCount: 0,
        activeCourses: 0,
        currentSemesterCourses: 0
      },
      systemHealth: {
        status: 'Healthy',
        lastCheck: new Date().toLocaleString()
      },
      recentActivities: [],
      notifications: []
    };
  },
  methods: {
    async fetchStats() {
      try {
        const response = await api.get('/admin/stats');
        this.stats = response.data;
      } catch (error) {
        console.error('Failed to fetch stats:', error);
      }
    },
    async fetchRecentActivities() {
      try {
        const response = await api.get('/admin/activities');
        this.recentActivities = response.data.map(activity => ({
          ...activity,
          icon: this.getActivityIcon(activity.type)
        }));
      } catch (error) {
        console.error('Failed to fetch activities:', error);
      }
    },
    async fetchNotifications() {
      try {
        const response = await api.get('/admin/notifications');
        this.notifications = response.data;
      } catch (error) {
        console.error('Failed to fetch notifications:', error);
      }
    },
    getActivityIcon(type) {
      const icons = {
        user: '👤',
        course: '📚',
        enrollment: '📝',
        grade: '📊',
        system: '⚙️'
      };
      return icons[type] || '📌';
    },
    async dismissNotification(id) {
      try {
        await api.post(`/admin/notifications/${id}/dismiss`);
        this.notifications = this.notifications.filter(n => n.id !== id);
      } catch (error) {
        console.error('Failed to dismiss notification:', error);
      }
    }
  },
  mounted() {
    this.fetchStats();
    this.fetchRecentActivities();
    this.fetchNotifications();

    // Refresh data periodically
    setInterval(this.fetchStats, 300000); // every 5 minutes
    setInterval(this.fetchRecentActivities, 60000); // every minute
    setInterval(this.fetchNotifications, 60000); // every minute
  }
};
</script>

<style scoped>
.admin-dashboard {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

h2 {
  font-size: 2rem;
  margin-bottom: 2rem;
  color: #7C9885;
}

h3 {
  font-size: 1.25rem;
  margin-bottom: 1rem;
  color: #7C9885;
}

.overview-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.card {
  background: #f8f8f4;
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 10px 40px 0 rgba(124, 152, 133, 0.15), 0 2px 8px 0 rgba(0,0,0,0.12);
  display: flex;
  align-items: center;
  gap: 1rem;
  border: 1.5px solid #7C9885;
}

.card-icon {
  font-size: 2rem;
  background: #e6e8d8;
  padding: 1rem;
  border-radius: 10px;
  color: #7C9885;
}

.card-content {
  flex: 1;
}

.card-value {
  font-size: 1.5rem;
  font-weight: bold;
  color: #7C9885;
  margin: 0.5rem 0;
}

.card-breakdown {
  font-size: 0.875rem;
  color: #B5B682;
  display: flex;
  gap: 1rem;
}

.quick-actions {
  background: #f8f8f4;
  border-radius: 20px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 10px 40px 0 rgba(124, 152, 133, 0.15), 0 2px 8px 0 rgba(0,0,0,0.12);
  border: 1.5px solid #7C9885;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  background: linear-gradient(90deg, #7C9885 60%, #B5B682 100%);
  border-radius: 8px;
  text-decoration: none;
  color: white;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(124, 152, 133, 0.15);
}

.action-btn:hover {
  background: linear-gradient(90deg, #B5B682 60%, #7C9885 100%);
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 4px 16px rgba(124, 152, 133, 0.33);
}

.recent-activity, .system-notifications {
  background: #f8f8f4;
  border-radius: 20px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 10px 40px 0 rgba(124, 152, 133, 0.15), 0 2px 8px 0 rgba(0,0,0,0.12);
  border: 1.5px solid #7C9885;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid #e6e8d8;
}

.activity-icon {
  font-size: 1.25rem;
  padding: 0.5rem;
  border-radius: 8px;
  background: #e6e8d8;
  color: #7C9885;
}

.activity-details {
  flex: 1;
}

.activity-time {
  font-size: 0.875rem;
  color: #B5B682;
}

.notification-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 0.5rem;
  background: #e6e8d8;
}

.notification-item.high {
  background: #fff5f5;
  border-left: 4px solid #f56565;
}

.notification-item.medium {
  background: #fffaf0;
  border-left: 4px solid #ed8936;
}

.notification-content {
  flex: 1;
}

.notification-title {
  font-weight: 600;
  margin-bottom: 0.25rem;
  color: #7C9885;
}

.notification-message {
  font-size: 0.875rem;
  color: #B5B682;
}

.dismiss-btn {
  padding: 0.25rem 0.5rem;
  border: none;
  background: none;
  color: #7C9885;
  cursor: pointer;
  font-size: 1rem;
  transition: color 0.3s ease;
}

.dismiss-btn:hover {
  color: #B5B682;
}
</style>
