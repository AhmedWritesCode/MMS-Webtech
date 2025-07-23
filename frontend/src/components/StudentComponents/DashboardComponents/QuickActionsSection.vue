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
.quick-actions-section {
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
}
.quick-actions-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
  justify-items: center;
}
.quick-action-card {
  background: #fff;
  border-radius: 1.2rem;
  border: 2px solid #7C9885;
  box-shadow: 0 4px 16px 0 rgba(124, 152, 133, 0.08);
  padding: 2rem 1.5rem 1.5rem 1.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  transition: box-shadow 0.2s, transform 0.2s;
  min-width: 180px;
  min-height: 170px;
}
.quick-action-card:hover {
  box-shadow: 0 8px 32px 0 rgba(124, 152, 133, 0.16);
  transform: translateY(-2px) scale(1.03);
  border-color: #B5B682;
}
.quick-action-icon {
  font-size: 2.5rem;
  color: #7C9885;
  margin-bottom: 1.2rem;
}
.quick-action-content {
  text-align: center;
}
.quick-action-content h4 {
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 0.3rem;
  color: #222;
}
.quick-action-content p {
  font-size: 0.95rem;
  color: #444;
  margin: 0;
}
@media (max-width: 900px) {
  .quick-actions-grid {
    grid-template-columns: 1fr;
    gap: 1.2rem;
  }
  .quick-action-card {
    min-width: 0;
    width: 100%;
  }
}
</style>
