<template>
  <div class="recent-activity-section">
    <h2>Recent Activity</h2>
    <div class="activity-list">
      <ActivityItem
        v-for="activity in activities"
        :key="activity.id"
        :activity="activity"
        @activity-action="handleActivityAction"
      />
    </div>
  </div>
</template>

<script>
import ActivityItem from "./ActivityItem.vue";

export default {
  name: "RecentActivitySection",
  components: {
    ActivityItem,
  },
  props: {
    activities: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: ["activity-action"],
  methods: {
    handleActivityAction(activity) {
      this.$emit("activity-action", activity);
    },
  },
};
</script>

<style scoped>
.recent-activity-section {
  margin-bottom: 30px;
  background: #111;
  border-radius: 18px;
  padding: 32px 24px;
  box-shadow: 0 4px 24px rgba(0, 128, 0, 0.15);
  border: 2px solid #16a34a;
}

.recent-activity-section h2 {
  font-size: 2rem;
  font-weight: 900;
  color: #16a34a;
  margin: 0 0 28px 0;
  letter-spacing: 2px;
  text-transform: uppercase;
  text-shadow: 0 2px 8px #000, 0 0 2px #16a34a;
}

.activity-list {
  background: linear-gradient(135deg, #022c22 0%, #111 100%);
  border-radius: 14px;
  box-shadow: 0 2px 18px rgba(22, 163, 74, 0.18);
  overflow: hidden;
  border: 1.5px solid #16a34a;
  padding: 18px 10px;
}
</style>
