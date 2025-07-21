<template>
  <div class="percentile-ranking">
    <div class="section-header">
      <h3>Percentile Ranking</h3>
      <p class="section-subtitle">
        Your position relative to class performance
      </p>
    </div>

    <div class="percentile-content">
      <div class="percentile-chart">
        <div class="percentile-line">
          <div
            class="percentile-marker your-position"
            :style="{ left: yourPercentile + '%' }"
          >
            <div class="marker-dot"></div>
            <div class="marker-label">
              <span class="marker-text">You</span>
              <span class="marker-value">{{ yourPercentile }}%</span>
            </div>
          </div>

          <div class="percentile-scale">
            <div class="scale-mark" style="left: 0%">0%</div>
            <div class="scale-mark" style="left: 25%">25%</div>
            <div class="scale-mark" style="left: 50%">50%</div>
            <div class="scale-mark" style="left: 75%">75%</div>
            <div class="scale-mark" style="left: 100%">100%</div>
          </div>
        </div>
      </div>

      <div class="percentile-breakdown">
        <div class="breakdown-item">
          <div class="breakdown-header">
            <h4>Students Below You</h4>
            <span class="breakdown-count">
              {{ studentsBelowCount }} students
            </span>
          </div>
          <div class="breakdown-bar">
            <div
              class="breakdown-fill below"
              :style="{ width: yourPercentile + '%' }"
            ></div>
          </div>
        </div>

        <div class="breakdown-item">
          <div class="breakdown-header">
            <h4>Students Above You</h4>
            <span class="breakdown-count">
              {{ studentsAboveCount }} students
            </span>
          </div>
          <div class="breakdown-bar">
            <div
              class="breakdown-fill above"
              :style="{ width: 100 - yourPercentile + '%' }"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PercentileRanking",
  props: {
    selectedCourse: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    rankData: {
      type: Object,
      required: false,
      default: () => ({}),
    },
  },
  computed: {
    // Use real data from API instead of static course data
    yourPercentile() {
      return this.rankData?.percentile || 0;
    },
    totalStudents() {
      return this.rankData?.total_students || 0;
    },
    studentsBelowCount() {
      return Math.floor((this.yourPercentile / 100) * this.totalStudents);
    },
    studentsAboveCount() {
      return this.totalStudents - this.studentsBelowCount - 1;
    },
  },
  methods: {
    getStudentsBelowCount() {
      return this.studentsBelowCount;
    },

    getStudentsAboveCount() {
      return this.studentsAboveCount;
    },
  },
};
</script>

<style scoped>
.percentile-ranking {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.section-header h3 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.section-subtitle {
  margin: 4px 0 24px 0;
  color: #64748b;
  font-size: 0.9rem;
}

.percentile-content {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.percentile-chart {
  background: #f8fafc;
  border-radius: 12px;
  padding: 40px 24px 24px 24px;
}

.percentile-line {
  position: relative;
  height: 60px;
  background: linear-gradient(
    to right,
    #ef4444 0%,
    #f59e0b 25%,
    #10b981 75%,
    #059669 100%
  );
  border-radius: 30px;
  margin-bottom: 20px;
}

.percentile-marker {
  position: absolute;
  top: -20px;
  transform: translateX(-50%);
}

.marker-dot {
  width: 20px;
  height: 20px;
  background: #1e293b;
  border: 4px solid white;
  border-radius: 50%;
  margin: 0 auto 8px auto;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.marker-label {
  background: #1e293b;
  color: white;
  padding: 8px 12px;
  border-radius: 8px;
  text-align: center;
  white-space: nowrap;
  font-size: 0.8rem;
}

.marker-text {
  display: block;
  font-weight: 700;
}

.marker-value {
  display: block;
  font-size: 0.7rem;
  opacity: 0.8;
}

.percentile-scale {
  display: flex;
  justify-content: space-between;
  position: relative;
}

.scale-mark {
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 600;
}

.percentile-breakdown {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.breakdown-item {
  background: #f8fafc;
  border-radius: 12px;
  padding: 20px;
}

.breakdown-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.breakdown-header h4 {
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
  color: #1e293b;
}

.breakdown-count {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 600;
}

.breakdown-bar {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.breakdown-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.breakdown-fill.below {
  background: #3b82f6;
}

.breakdown-fill.above {
  background: #f59e0b;
}
</style>
