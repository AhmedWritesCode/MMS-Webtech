<template>
  <div class="student-notifications">
    <div class="page-header">
      <h1>Notifications</h1>
      <p>
        Stay updated with your academic progress and important announcements.
      </p>
    </div>

    <div class="notifications-container">
      <div class="notification-filters">
        <button class="filter-btn active">All</button>
        <button class="filter-btn">Unread</button>
        <button class="filter-btn">Grades</button>
        <button class="filter-btn">Reminders</button>
      </div>

      <div class="notifications-list">
        <div class="notification-item unread">
          <div class="notification-icon grade">📊</div>
          <div class="notification-content">
            <h3>New Grade Posted</h3>
            <p>Your Assignment 2 grade for Web Technology has been posted</p>
            <span class="notification-time">2 hours ago</span>
          </div>
        </div>

        <div class="notification-item unread">
          <div class="notification-icon reminder">⏰</div>
          <div class="notification-content">
            <h3>Assignment Due Soon</h3>
            <p>Database Systems Lab Exercise 3 is due in 2 days</p>
            <span class="notification-time">4 hours ago</span>
          </div>
        </div>

        <div class="notification-item">
          <div class="notification-icon info">ℹ️</div>
          <div class="notification-content">
            <h3>Schedule Update</h3>
            <p>Software Engineering class has been moved to Room 301</p>
            <span class="notification-time">6 hours ago</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "StudentNotifications",
};
</script>

<style scoped>
.student-notifications {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.page-header {
  margin-bottom: 30px;
}

.page-header h1 {
  color: #1e293b;
  margin-bottom: 8px;
}

.page-header p {
  color: #64748b;
  margin: 0;
}

.notification-filters {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.filter-btn {
  padding: 8px 16px;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-btn.active,
.filter-btn:hover {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.notification-item {
  display: flex;
  gap: 16px;
  padding: 20px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  transition: all 0.2s;
}

.notification-item:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.notification-item.unread {
  border-left: 4px solid #3b82f6;
  background: #f8fafc;
}

.notification-icon {
  font-size: 1.5rem;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.notification-icon.grade {
  background: #dbeafe;
  color: #3b82f6;
}

.notification-icon.reminder {
  background: #fef3c7;
  color: #d97706;
}

.notification-icon.info {
  background: #e0e7ff;
  color: #6366f1;
}

.notification-content h3 {
  margin: 0 0 8px 0;
  color: #1e293b;
  font-size: 1rem;
}

.notification-content p {
  margin: 0 0 8px 0;
  color: #64748b;
  line-height: 1.5;
}

.notification-time {
  font-size: 0.8rem;
  color: #94a3b8;
}
</style>
