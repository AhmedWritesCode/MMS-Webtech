<template>
  <div class="trends-header">
    <div class="header-content">
      <div class="breadcrumb">
        <router-link to="/student/dashboard" class="breadcrumb-link"
          >Dashboard</router-link
        >
        <span class="breadcrumb-separator">›</span>
        <span class="breadcrumb-current">Performance Trends</span>
      </div>
      <h1 class="page-title">Performance Trends & Analytics</h1>
      <p class="page-subtitle">
        Track your academic progress over time and identify patterns in your
        performance.
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: "TrendsHeader",
};
</script>

<style scoped>
.trends-header {
  background: white;
  border-radius: 1px;
  border-radius: 16px;
  padding: 30px;
  margin-bottom: 30px;
  color: rgb(0, 0, 0);
}

.breadcrumb {
  margin-bottom: 16px;
  font-size: 0.9rem;
  opacity: 0.9;
}

.breadcrumb-link {
  color: rgb(0, 0, 0);
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
