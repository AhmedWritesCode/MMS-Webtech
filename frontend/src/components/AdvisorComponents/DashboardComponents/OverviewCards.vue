<template>
  <div class="overview-cards">
    <div class="overview-card performance-summary">
      <div class="card-header">
        <h3>Performance Overview</h3>
        <div class="performance-icon">📊</div>
      </div>
      <div class="card-content">
        <div class="performance-stats">
          <div class="stat-group">
            <div class="stat-circle excellent">
              <span class="circle-number">{{
                performanceStats.excellent
              }}</span>
              <span class="circle-label">Excellent</span>
            </div>
            <div class="stat-description">GPA ≥ 3.5</div>
          </div>
          <div class="stat-group">
            <div class="stat-circle good">
              <span class="circle-number">{{ performanceStats.good }}</span>
              <span class="circle-label">Good</span>
            </div>
            <div class="stat-description">GPA 3.0 - 3.49</div>
          </div>
          <div class="stat-group">
            <div class="stat-circle average">
              <span class="circle-number">{{ performanceStats.average }}</span>
              <span class="circle-label">Average</span>
            </div>
            <div class="stat-description">GPA 2.5 - 2.99</div>
          </div>
          <div class="stat-group">
            <div class="stat-circle at-risk">
              <span class="circle-number">{{ performanceStats.atRisk }}</span>
              <span class="circle-label">At Risk</span>
            </div>
            <div class="stat-description">GPA 2.5</div>
          </div>
        </div>
      </div>
    </div>

    <div class="overview-card recent-activity">
      <div class="card-header">
        <h3>Recent Activity</h3>
        <div class="activity-icon">🔔</div>
      </div>
      <div class="card-content">
        <div class="activity-list">
          <div
            v-for="activity in recentActivities.slice(0, 4)"
            :key="activity.id"
            class="activity-item"
          >
            <div class="activity-indicator" :class="activity.type"></div>
            <div class="activity-details">
              <p class="activity-text">{{ activity.description }}</p>
              <span class="activity-time">{{
                formatTimeAgo(activity.timestamp)
              }}</span>
            </div>
          </div>
        </div>
        <button class="view-all-activity-btn" @click="viewAllActivity">
          View All Activity
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "OverviewCards",
  props: {
    performanceStats: {
      type: Object,
      required: true,
      default: () => ({
        excellent: 0,
        good: 0,
        average: 0,
        atRisk: 0,
      }),
    },
    recentActivities: {
      type: Array,
      required: true,
      default: () => [],
    },
    upcomingMeetings: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: ["view-all-activity", "view-meeting", "schedule-meeting"],
  methods: {
    formatTimeAgo(timestamp) {
      const now = new Date();
      const diff = now - timestamp;
      const hours = Math.floor(diff / (1000 * 60 * 60));
      const days = Math.floor(hours / 24);

      if (days > 0) return `${days} day${days > 1 ? "s" : ""} ago`;
      if (hours > 0) return `${hours} hour${hours > 1 ? "s" : ""} ago`;
      return "Just now";
    },

    formatMeetingDate(dateTime) {
      return dateTime.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
      });
    },

    formatMeetingTime(dateTime) {
      return dateTime.toLocaleTimeString("en-US", {
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
      });
    },

    viewAllActivity() {
      this.$emit("view-all-activity");
    },

    viewMeeting(meetingId) {
      this.$emit("view-meeting", meetingId);
    },

    scheduleMeeting() {
      this.$emit("schedule-meeting");
    },
  },
};
</script>

<style scoped>
.overview-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.overview-card {
  background: white;
  border-radius: 1.5rem;
  padding: 24px;
  box-shadow: 0 6px 24px 0 rgba(124, 152, 133, 0.18);
  border: 1px solid #B5B682;
  transition: transform 0.2s;
}

.overview-card:hover {
  transform: translateY(-2px);
  border-color: #7C9885;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h3 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #7C9885;
}

.performance-icon,
.activity-icon,
.meetings-icon {
  font-size: 1.5rem;
  opacity: 0.7;
}

/* Performance Summary */
.performance-stats {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.stat-group {
  text-align: center;
}

.stat-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin: 0 auto 8px auto;
  position: relative;
}

.stat-circle.excellent {
  background: #7C9885;
  color: white;
}

.stat-circle.good {
  background: #B5B682;
  color: #23272f;
}

.stat-circle.average {
  background: #f59e0b;
  color: white;
}

.stat-circle.at-risk {
  background: #e74c3c;
  color: white;
}

.circle-number {
  font-size: 1.5rem;
  font-weight: 700;
}

.circle-label {
  font-size: 0.7rem;
  font-weight: 600;
  opacity: 0.9;
}

.stat-description {
  font-size: 0.8rem;
  color: #64748b;
  margin-top: 4px;
}

/* Recent Activity */
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: rgba(181, 182, 130, 0.1);
  border-radius: 8px;
  border: 1px solid #B5B682;
}

.activity-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.activity-indicator.grade_alert {
  background: #ef4444;
}

.activity-indicator.meeting_completed {
  background: #10b981;
}

.activity-indicator.note_added {
  background: #3b82f6;
}

.activity-indicator.progress_report {
  background: #f59e0b;
}

.activity-details {
  flex: 1;
}

.activity-text {
  margin: 0 0 4px 0;
  font-weight: 500;
  color: #1e293b;
  font-size: 0.9rem;
}

.activity-time {
  font-size: 0.8rem;
  color: #64748b;
}

.view-all-activity-btn {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #B5B682;
  padding: 8px 16px;
  border-radius: 0.7rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  width: 100%;
}

.view-all-activity-btn:hover {
  background: #B5B682;
  color: #23272f;
}

/* Upcoming Meetings */
.meetings-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.meeting-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: rgba(181, 182, 130, 0.1);
  border-radius: 8px;
  border: 1px solid #B5B682;
}

.meeting-time {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 60px;
}

.meeting-date {
  font-weight: 700;
  color: #1e293b;
  font-size: 0.9rem;
}

.meeting-hour {
  font-size: 0.8rem;
  color: #64748b;
}

.meeting-details {
  flex: 1;
}

.meeting-details .student-name {
  margin: 0 0 4px 0;
  font-weight: 600;
  color: #1e293b;
}

.meeting-purpose {
  margin: 0;
  font-size: 0.9rem;
  color: #64748b;
}

.meeting-action-btn {
  background: #7C9885;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 0.7rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.meeting-action-btn:hover {
  background: #B5B682;
  color: #23272f;
}

.no-meetings {
  text-align: center;
  padding: 20px;
  color: #64748b;
}

.no-meetings p {
  margin: 0 0 16px 0;
}

.schedule-meeting-btn {
  background: #7C9885;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 0.7rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.schedule-meeting-btn:hover {
  background: #B5B682;
  color: #23272f;
}

@media (max-width: 1200px) {
  .overview-cards {
    grid-template-columns: 1fr;
  }

  .performance-stats {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 768px) {
  .performance-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .performance-stats {
    grid-template-columns: 1fr;
  }
}
</style>
