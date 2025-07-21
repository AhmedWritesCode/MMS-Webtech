<template>
  <div class="no-course-selected">
    <div class="empty-state">
      <div class="empty-icon">
        <svg width="60" height="60" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="#22c55e" stroke-width="2" fill="#111827"/>
          <path d="M7 13l3 3 7-7" stroke="#22c55e" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <h3>No Course Selected</h3>
      <p>Please pick a course above to compare performance.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "NoCourseSelected",
};
</script>

<style scoped>
.no-course-selected {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  background: #111827;
}

.empty-state {
  background: #1a1a1a;
  border-radius: 18px;
  padding: 40px 36px;
  box-shadow: 0 4px 24px rgba(34, 197, 94, 0.15);
  text-align: center;
  color: #22c55e;
  border: 2px solid #22c55e;
  max-width: 340px;
}

.empty-icon {
  margin-bottom: 20px;
  display: flex;
  justify-content: center;
}

.empty-state h3 {
  margin: 0 0 12px 0;
  font-size: 1.7rem;
  font-weight: 800;
  color: #22c55e;
  letter-spacing: 1px;
}

.empty-state p {
  margin: 0;
  font-size: 1.05rem;
  color: #d1fae5;
  font-weight: 500;
}
</style>
