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
}

.recent-activity-section h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 20px 0;
}

.activity-list {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}
</style>
