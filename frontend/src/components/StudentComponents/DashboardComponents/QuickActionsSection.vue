<template>
  <div class="quick-actions-section">
    <div class="quick-actions-grid">
      <div class="quick-action-card" @click="$emit('action-clicked', 'simulator')">
        <Target class="quick-action-icon" />
        <div class="quick-action-content">
          <h4>What-If Simulator</h4>
          <p>Predict your final grades</p>
        </div>
      </div>
      <div class="quick-action-card" @click="$emit('action-clicked', 'compare')">
        <BarChart2 class="quick-action-icon" />
        <div class="quick-action-content">
          <h4>Compare Performance</h4>
          <p>See how you rank</p>
        </div>
      </div>
      <div class="quick-action-card" @click="$emit('action-clicked', 'remark')">
        <FileText class="quick-action-icon" />
        <div class="quick-action-content">
          <h4>Request Remark</h4>
          <p>Appeal assessment marks</p>
        </div>
      </div>
      <div class="quick-action-card" @click="$emit('action-clicked', 'trends')">
        <LineChart class="quick-action-icon" />
        <div class="quick-action-content">
          <h4>Performance Trends</h4>
          <p>Track your progress</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Target, BarChart2, FileText, LineChart } from 'lucide-vue-next';
export default {
  name: "QuickActionsSection",
  components: { Target, BarChart2, FileText, LineChart },
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
