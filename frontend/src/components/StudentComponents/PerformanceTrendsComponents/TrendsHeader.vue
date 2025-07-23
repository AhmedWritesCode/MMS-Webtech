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
  background: #fff;
  border-radius: 2rem;
  padding: 36px 32px 32px 32px;
  margin-bottom: 30px;
  color: #23272f;
  box-shadow: 0 4px 24px 0 rgba(124, 152, 133, 0.13);
  backdrop-filter: blur(6px);
  position: relative;
}

.breadcrumb {
  margin-bottom: 16px;
  font-size: 0.95rem;
  opacity: 0.95;
  color: #23272f;
}

.breadcrumb-link {
  color: #23272f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}

.breadcrumb-link:hover {
  text-decoration: underline;
  color: #7C9885;
}

.breadcrumb-separator {
  margin: 0 8px;
}

.breadcrumb-current {
  color: #7C9885;
  font-weight: 700;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 800;
  margin: 0 0 12px 0;
  color: #23272f;
  letter-spacing: -1px;
  text-shadow: 0 2px 8px #B5B68233;
}

.page-subtitle {
  font-size: 1.15rem;
  opacity: 0.95;
  margin: 0;
  line-height: 1.5;
  color: #7C9885;
}

@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }
  .trends-header {
    padding: 18px 10px 16px 10px;
    border-radius: 1.2rem;
  }
}
</style>
