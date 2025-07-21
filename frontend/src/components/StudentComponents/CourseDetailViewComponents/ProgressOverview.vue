<template>
  <div class="progress-overview">
    <div class="progress-card">
      <h3>Course Progress</h3>
      <div class="progress-visual">
        <div class="circular-progress">
          <svg width="120" height="120" viewBox="0 0 100 100">
            <circle
              cx="50"
              cy="50"
              r="45"
              fill="none"
              stroke="#e2e8f0"
              stroke-width="8"
            />
            <circle
              cx="50"
              cy="50"
              r="45"
              fill="none"
              stroke="#3b82f6"
              stroke-width="8"
              stroke-linecap="round"
              :stroke-dasharray="circumference"
              :stroke-dashoffset="progressOffset"
              transform="rotate(-90 50 50)"
              class="progress-circle"
            />
          </svg>
          <div class="progress-text">
            <span class="progress-percentage">
              {{ Math.round(course.progressPercentage) }}%
            </span>
            <span class="progress-label">Complete</span>
          </div>
        </div>

        <div class="progress-details">
          <div class="detail-item">
            <span class="detail-label">Completed Assessments</span>
            <span class="detail-value">
              {{ course.completedAssessments }}/{{ course.totalAssessments }}
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Current Marks</span>
            <span class="detail-value">
              {{ course.currentMarks }}/{{ course.totalMarks }}
            </span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Class Average</span>
            <span class="detail-value">{{ course.classAverage }}%</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProgressOverview",
  props: {
    course: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  data() {
    return {
      circumference: 2 * Math.PI * 45,
    };
  },
  computed: {
    progressOffset() {
      return (
        this.circumference -
        (this.course.progressPercentage / 100) * this.circumference
      );
    },
  },
};
</script>

<style scoped>
.progress-overview {
  margin-bottom: 30px;
}

.progress-card {
  background: white;
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.progress-card h3 {
  margin: 0 0 24px 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
}

.progress-visual {
  display: flex;
  align-items: center;
  gap: 40px;
}

.circular-progress {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.progress-circle {
  transition: stroke-dashoffset 0.5s ease;
}

.progress-text {
  position: absolute;
  text-align: center;
}

.progress-percentage {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.progress-label {
  font-size: 0.8rem;
  color: #64748b;
}

.progress-details {
  flex: 1;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid #e2e8f0;
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-label {
  color: #64748b;
  font-weight: 500;
}

.detail-value {
  font-weight: 700;
  color: #1e293b;
}

@media (max-width: 768px) {
  .progress-visual {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }
}
</style>
