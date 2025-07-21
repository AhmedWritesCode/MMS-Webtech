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
        <div class="percentile-bar-bg">
          <div
            class="percentile-bar-fill"
            :style="{ width: yourPercentile + '%' }"
          ></div>
          <div
            class="percentile-marker"
            :style="{ left: yourPercentile + '%' }"
          >
            <div class="marker-dot"></div>
            <div class="marker-label">
              <span class="marker-text">You</span>
              <span class="marker-value">{{ yourPercentile }}%</span>
            </div>
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

      <div class="percentile-breakdown">
        <div class="breakdown-item below">
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

        <div class="breakdown-item above">
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
  background: #101010;
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 4px 24px rgba(0, 255, 64, 0.08);
  color: #e6ffe6;
}

.section-header h3 {
  margin: 0;
  font-size: 2rem;
  font-weight: 800;
  color: #00ff66;
  letter-spacing: 1px;
}

.section-subtitle {
  margin: 6px 0 28px 0;
  color: #b6ffcc;
  font-size: 1rem;
  font-weight: 500;
}

.percentile-content {
  display: flex;
  flex-direction: column;
  gap: 36px;
}

.percentile-chart {
  background: #181f18;
  border-radius: 14px;
  padding: 36px 20px 20px 20px;
  box-shadow: 0 2px 12px rgba(0, 255, 64, 0.04);
}

.percentile-bar-bg {
  position: relative;
  height: 22px;
  background: #222;
  border-radius: 11px;
  overflow: hidden;
  margin-bottom: 30px;
  border: 2px solid #00ff66;
}

.percentile-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #00ff66 0%, #009944 100%);
  border-radius: 11px 0 0 11px;
  transition: width 0.4s cubic-bezier(0.4,0,0.2,1);
}

.percentile-marker {
  position: absolute;
  top: -32px;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  z-index: 2;
}

.marker-dot {
  width: 24px;
  height: 24px;
  background: #00ff66;
  border: 4px solid #101010;
  border-radius: 50%;
  margin-bottom: 6px;
  box-shadow: 0 0 12px #00ff66, 0 2px 8px rgba(0,255,64,0.2);
}

.marker-label {
  background: #101010;
  color: #00ff66;
  padding: 7px 14px;
  border-radius: 8px;
  text-align: center;
  font-size: 0.9rem;
  font-weight: 700;
  border: 1.5px solid #00ff66;
  box-shadow: 0 2px 8px rgba(0,255,64,0.08);
}

.marker-text {
  display: block;
  font-weight: 800;
  letter-spacing: 0.5px;
}

.marker-value {
  display: block;
  font-size: 0.8rem;
  opacity: 0.85;
}

.percentile-scale {
  position: relative;
  width: 100%;
  margin-top: 8px;
  height: 18px;
}

.scale-mark {
  position: absolute;
  top: 0;
  transform: translateX(-50%);
  font-size: 0.85rem;
  color: #00ff66;
  font-weight: 700;
  opacity: 0.8;
}
.scale-mark[style*="0%"] { left: 0%; }
.scale-mark[style*="25%"] { left: 25%; }
.scale-mark[style*="50%"] { left: 50%; }
.scale-mark[style*="75%"] { left: 75%; }
.scale-mark[style*="100%"] { left: 100%; }

.percentile-breakdown {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.breakdown-item {
  background: #181f18;
  border-radius: 12px;
  padding: 22px;
  border-left: 6px solid #00ff66;
  box-shadow: 0 2px 8px rgba(0,255,64,0.04);
}

.breakdown-item.above {
  border-left: 6px solid #009944;
}

.breakdown-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}

.breakdown-header h4 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 800;
  color: #00ff66;
  letter-spacing: 0.5px;
}

.breakdown-item.above .breakdown-header h4 {
  color: #009944;
}

.breakdown-count {
  font-size: 1rem;
  color: #b6ffcc;
  font-weight: 700;
}

.breakdown-bar {
  height: 12px;
  background: #222;
  border-radius: 6px;
  overflow: hidden;
  border: 1.5px solid #00ff66;
}

.breakdown-item.above .breakdown-bar {
  border-color: #009944;
}

.breakdown-fill {
  height: 100%;
  border-radius: 6px;
  transition: width 0.3s cubic-bezier(0.4,0,0.2,1);
}

.breakdown-fill.below {
  background: linear-gradient(90deg, #00ff66 0%, #009944 100%);
}

.breakdown-fill.above {
  background: linear-gradient(90deg, #009944 0%, #00ff66 100%);
}
</style>
