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
              stroke="#222"
              stroke-width="10"
            />
            <circle
              cx="50"
              cy="50"
              r="45"
              fill="none"
              stroke="#22c55e"
              stroke-width="10"
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
  background: #111;
  padding: 40px 0;
}

.progress-card {
  background: #181818;
  border-radius: 20px;
  padding: 36px;
  box-shadow: 0 4px 24px rgba(34, 197, 94, 0.08);
  border: 2px solid #22c55e;
  max-width: 600px;
  margin: 0 auto;
}

.progress-card h3 {
  margin: 0 0 28px 0;
  font-size: 1.5rem;
  font-weight: 800;
  color: #22c55e;
  letter-spacing: 1px;
  text-align: center;
}

.progress-visual {
  display: flex;
  align-items: stretch;
  gap: 48px;
  justify-content: center;
}

.circular-progress {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #111;
  border-radius: 50%;
  box-shadow: 0 0 0 6px #22c55e33;
  width: 140px;
  height: 140px;
}

.progress-circle {
  transition: stroke-dashoffset 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  filter: drop-shadow(0 0 8px #22c55e88);
}

.progress-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}

.progress-percentage {
  display: block;
  font-size: 2.2rem;
  font-weight: 900;
  color: #22c55e;
  text-shadow: 0 2px 8px #111;
  letter-spacing: 2px;
}

.progress-label {
  font-size: 1rem;
  color: #fff;
  opacity: 0.7;
  font-weight: 600;
  letter-spacing: 1px;
}

.progress-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #111;
  border-radius: 14px;
  padding: 18px 24px;
  box-shadow: 0 2px 12px rgba(34, 197, 94, 0.06);
  min-width: 200px;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0;
  border-bottom: 1px solid #222;
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-label {
  color: #a3e635;
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: 0.5px;
}

.detail-value {
  font-weight: 900;
  color: #fff;
  background: #22c55e;
  padding: 4px 14px;
  border-radius: 8px;
  font-size: 1.1rem;
  box-shadow: 0 1px 4px #22c55e33;
}

@media (max-width: 768px) {
  .progress-visual {
    flex-direction: column;
    gap: 28px;
    text-align: center;
  }
  .progress-card {
    padding: 18px;
  }
  .progress-details {
    padding: 12px 8px;
    min-width: unset;
  }
}
</style>
