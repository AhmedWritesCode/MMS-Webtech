<template>
  <div class="simulator-header">
    <div class="header-content">
      <div class="breadcrumb">
        <router-link to="/student/dashboard" class="breadcrumb-link"
          >Dashboard</router-link
        >
        <span class="breadcrumb-separator">›</span>
        <span class="breadcrumb-current">What-If Simulator</span>
      </div>
      <h1 class="page-title">Grade Prediction Simulator</h1>
      <p class="page-subtitle">
        Experiment with different score scenarios to predict your final grade
        and plan your study strategy.
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: "SimulatorHeader",
};
</script>

<style scoped>
.simulator-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 30px;
  margin-bottom: 30px;
  color: white;
}

.breadcrumb {
  margin-bottom: 16px;
  font-size: 0.9rem;
  opacity: 0.9;
}

.breadcrumb-link {
  color: white;
  text-decoration: none;
}

.breadcrumb-link:hover {
  text-decoration: underline;
}

.breadcrumb-separator {
  margin: 0 8px;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 12px 0;
}

.page-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  margin: 0;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }
}
</style>
