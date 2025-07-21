<template>
  <div class="quick-actions-section">
    <h2>Quick Actions</h2>
    <div class="action-buttons">
      <ActionCard
        v-for="action in actions"
        :key="action.id"
        :action="action"
        @action-clicked="handleActionClick"
      />
    </div>
  </div>
</template>

<script>
import ActionCard from "./ActionCard.vue";

export default {
  name: "QuickActionsSection",
  components: {
    ActionCard,
  },
  data() {
    return {
      actions: [
        {
          id: "what-if",
          icon: "🎯",
          title: "What-If Simulator",
          description: "Predict your final grades",
          route: "/what-if-simulator",
        },
        {
          id: "compare",
          icon: "📊",
          title: "Compare Performance",
          description: "See how you rank",
          route: "/performance-comparison",
        },
        {
          id: "remark",
          icon: "📝",
          title: "Request Remark",
          description: "Appeal assessment marks",
          route: "/remark-request",
        },
        {
          id: "trends",
          icon: "📈",
          title: "Performance Trends",
          description: "Track your progress",
          route: "/performance-trends",
        },
      ],
    };
  },
  emits: ["action-clicked"],
  methods: {
    handleActionClick(action) {
      this.$emit("action-clicked", action);
    },
  },
};
</script>

<style scoped>
.quick-actions-section h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 20px 0;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .action-buttons {
    grid-template-columns: 1fr;
  }
}
</style>
