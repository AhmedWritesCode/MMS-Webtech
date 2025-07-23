<template>
  <div class="comparison-header">
    <div class="header-content">
      <div class="breadcrumb">
        <router-link to="/student/dashboard" class="breadcrumb-link"
          >Dashboard</router-link
        >
        <span class="breadcrumb-separator">›</span>
        <span class="breadcrumb-current">Performance Comparison</span>
      </div>
      <h1 class="page-title">Anonymous Performance Comparison</h1>
      <p class="page-subtitle">
        Compare your performance with classmates anonymously. All data is
        displayed without identifying individual students.
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: "ComparisonHeader",
};
</script>

<style scoped>
.comparison-header {
  background: rgba(255,255,255,0.92);
  border-radius: 1.5rem;
  padding: 2.5rem 2rem 2rem 2rem;
  margin-bottom: 2.5rem;
  color: #7C9885;
  box-shadow: 0 4px 24px 0 rgba(181, 182, 130, 0.10);
  border: 2px solid #B5B682;
}

.breadcrumb {
  margin-bottom: 16px;
  font-size: 0.9rem;
  opacity: 0.9;
}

.breadcrumb-link {
  color: #7C9885;
  text-decoration: none;
}

.breadcrumb-link:hover {
  text-decoration: underline;
  color: #B5B682;
}

.breadcrumb-separator {
  margin: 0 8px;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #7C9885;
}

.page-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  margin: 0;
  line-height: 1.5;
  color: #B5B682;
}

@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }
}
</style>
