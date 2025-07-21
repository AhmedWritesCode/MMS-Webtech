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
  background: #101710;
  padding: 2rem 0;
  border-radius: 1.5rem;
  box-shadow: 0 0 0 2px #1db954 inset;
}

.quick-actions-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  justify-content: center;
}

.quick-action-card {
  background: linear-gradient(135deg, #1db954 0%, #101710 100%);
  border-radius: 1.5rem;
  box-shadow: 0 6px 24px 0 rgba(30, 185, 84, 0.18);
  padding: 2rem 2rem 1.5rem 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  min-width: 200px;
  min-height: 180px;
  border: 2px solid #1db954;
  transition: 
    box-shadow 0.2s,
    transform 0.2s,
    border-color 0.2s,
    background 0.2s;
  position: relative;
  overflow: hidden;
}

.quick-action-card:hover {
  box-shadow: 0 12px 36px 0 rgba(30, 185, 84, 0.28);
  transform: translateY(-4px) scale(1.04);
  border-color: #0e331a;
  background: linear-gradient(135deg, #101710 0%, #1db954 100%);
}

.quick-action-icon {
  font-size: 2.8rem;
  color: #1db954;
  margin-bottom: 1.3rem;
  filter: drop-shadow(0 0 8px #1db95488);
  background: #101710;
  border-radius: 50%;
  padding: 0.7rem;
  border: 2px solid #1db954;
  transition: background 0.2s, color 0.2s;
}

.quick-action-card:hover .quick-action-icon {
  background: #1db954;
  color: #101710;
  border-color: #101710;
}

.quick-action-content {
  text-align: center;
  color: #fff;
}

.quick-action-content h4 {
  font-size: 1.15rem;
  font-weight: 800;
  margin-bottom: 0.4rem;
  color: #1db954;
  letter-spacing: 0.03em;
  text-shadow: 0 2px 8px #10171088;
}

.quick-action-content p {
  font-size: 1rem;
  color: #e0ffe0;
  margin: 0;
  opacity: 0.85;
}

@media (max-width: 900px) {
  .quick-actions-grid {
    flex-direction: column;
    gap: 1.2rem;
    align-items: center;
  }
  .quick-action-card {
    min-width: 0;
    width: 90vw;
    max-width: 400px;
  }
}
</style>
