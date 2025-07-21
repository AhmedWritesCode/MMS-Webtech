<template>
  <div class="section-card meeting-history">
    <div class="section-header">
      <h3>Meeting History</h3>
      <span class="meeting-count">{{ meetingHistory.length }} meetings</span>
    </div>
    <div class="meetings-list">
      <div
        v-for="meeting in meetingHistory"
        :key="meeting.id"
        class="meeting-item"
      >
        <div class="meeting-header">
          <div class="meeting-date">
            <span class="date">{{ formatDate(meeting.date) }}</span>
            <span class="time">{{ formatTime(meeting.date) }}</span>
          </div>
          <div class="meeting-type" :class="meeting.type">
            {{ meeting.type }}
          </div>
        </div>
        <div class="meeting-content">
          <h4>{{ meeting.title }}</h4>
          <p>{{ meeting.summary }}</p>
        </div>
        <div class="meeting-outcomes" v-if="meeting.outcomes">
          <h5>Outcomes:</h5>
          <ul>
            <li v-for="outcome in meeting.outcomes" :key="outcome">
              {{ outcome }}
            </li>
          </ul>
        </div>
        <div class="meeting-actions">
          <button
            class="meeting-action view"
            @click="$emit('view-meeting-details', meeting.id)"
          >
            View Details
          </button>
        </div>
      </div>
    </div>
    <div v-if="meetingHistory.length === 0" class="no-meetings">
      <p>No meetings recorded yet.</p>
      <button
        class="schedule-first-meeting-btn"
        @click="$emit('schedule-meeting')"
      >
        Schedule First Meeting
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "MeetingHistoryCard",
  props: {
    meetingHistory: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: ["view-meeting-details", "schedule-meeting"],
  methods: {
    formatDate(date) {
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },

    formatTime(date) {
      return date.toLocaleTimeString("en-US", {
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
      });
    },
  },
};
</script>

<style scoped>
.section-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
}

.meeting-count {
  background: #f1f5f9;
  color: #475569;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.meetings-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.meeting-item {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
  background: #f8fafc;
}

.meeting-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.meeting-date {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.meeting-date .date {
  font-weight: 600;
  color: #1e293b;
}

.meeting-date .time {
  font-size: 0.8rem;
  color: #64748b;
}

.meeting-type {
  font-size: 0.7rem;
  padding: 4px 8px;
  border-radius: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.meeting-type.scheduled {
  background: #dbeafe;
  color: #3b82f6;
}

.meeting-type.urgent {
  background: #fee2e2;
  color: #dc2626;
}

.meeting-content h4 {
  margin: 0 0 8px 0;
  font-weight: 700;
  color: #1e293b;
}

.meeting-content p {
  margin: 0 0 12px 0;
  color: #64748b;
  line-height: 1.5;
}

.meeting-outcomes h5 {
  margin: 0 0 8px 0;
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9rem;
}

.meeting-outcomes ul {
  margin: 0 0 12px 0;
  padding-left: 16px;
}

.meeting-outcomes li {
  color: #64748b;
  font-size: 0.9rem;
  margin-bottom: 4px;
}

.meeting-actions {
  display: flex;
  gap: 8px;
}

.meeting-action {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.meeting-action.view {
  background: #3b82f6;
  color: white;
}

.meeting-action.view:hover {
  background: #2563eb;
}

.no-meetings {
  text-align: center;
  padding: 20px;
  color: #64748b;
}

.schedule-first-meeting-btn {
  background: #059669;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  margin-top: 12px;
}

.schedule-first-meeting-btn:hover {
  background: #047857;
}

@media (max-width: 480px) {
  .meeting-actions {
    flex-direction: column;
  }
}
</style>
