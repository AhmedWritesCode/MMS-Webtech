<template>
  <div class="controls-section">
    <div class="controls-card">
      <div class="control-group">
        <label>Select Course:</label>
        <select
          :value="selectedCourseId"
          @change="updateCourse($event.target.value)"
          class="course-select"
        >
          <option value="all">All Courses</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">
            {{ course.code }} - {{ course.name }}
          </option>
        </select>
      </div>

      <div class="control-group">
        <label>Time Range:</label>
        <div class="time-range-tabs">
          <button
            v-for="range in timeRanges"
            :key="range.key"
            :class="['time-tab', { active: selectedTimeRange === range.key }]"
            @click="updateTimeRange(range.key)"
          >
            {{ range.label }}
          </button>
        </div>
      </div>

      <div class="control-group">
        <label>Chart Type:</label>
        <div class="chart-type-tabs">
          <button
            v-for="type in chartTypes"
            :key="type.key"
            :class="['chart-tab', { active: selectedChartType === type.key }]"
            @click="updateChartType(type.key)"
          >
            {{ type.icon }} {{ type.label }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TrendsControls",
  props: {
    courses: {
      type: Array,
      required: true,
      default: () => [],
    },
    selectedCourseId: {
      type: [String, Number],
      required: true,
      default: "all",
    },
    selectedTimeRange: {
      type: String,
      required: true,
      default: "semester",
    },
    selectedChartType: {
      type: String,
      required: true,
      default: "line",
    },
  },
  emits: ["update-course", "update-time-range", "update-chart-type"],
  data() {
    return {
      timeRanges: [
        { key: "month", label: "This Month" },
        { key: "semester", label: "This Semester" },
        { key: "year", label: "This Year" },
        { key: "all", label: "All Time" },
      ],
      chartTypes: [
        { key: "line", label: "Line Chart", icon: "📈" },
        { key: "bar", label: "Bar Chart", icon: "📊" },
        { key: "area", label: "Area Chart", icon: "📉" },
      ],
    };
  },
  methods: {
    updateCourse(courseId) {
      this.$emit("update-course", courseId);
    },

    updateTimeRange(timeRange) {
      this.$emit("update-time-range", timeRange);
    },

    updateChartType(chartType) {
      this.$emit("update-chart-type", chartType);
    },
  },
};
</script>

<style scoped>
.controls-section {
  margin-bottom: 30px;
}

.controls-card {
  background: rgba(255,255,255,0.92);
  border-radius: 1.5rem;
  padding: 2rem 1.5rem;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
  backdrop-filter: blur(6px);
}

.control-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-width: 200px;
}

.control-group label {
  font-weight: 700;
  color: #7C9885;
  font-size: 1rem;
  margin-bottom: 2px;
}

.course-select {
  padding: 10px 14px;
  border: 2px solid #B5B682;
  border-radius: 10px;
  background: #fff;
  font-weight: 600;
  color: #23272f;
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-size: 1rem;
  box-shadow: 0 1px 4px rgba(124, 152, 133, 0.08);
}

.course-select:focus {
  outline: none;
  border-color: #7C9885;
  box-shadow: 0 0 0 2px #B5B68233;
}

.time-range-tabs,
.chart-type-tabs {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.time-tab,
.chart-tab {
  padding: 10px 20px;
  border: 2px solid #B5B682;
  background: rgba(181, 182, 130, 0.10);
  border-radius: 22px;
  font-weight: 700;
  color: #7C9885;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 1rem;
  box-shadow: 0 1px 4px rgba(124, 152, 133, 0.08);
}

.time-tab.active,
.time-tab:hover,
.chart-tab.active,
.chart-tab:hover {
  border-color: #7C9885;
  color: #fff;
  background: linear-gradient(90deg, #B5B682 0%, #7C9885 100%);
  box-shadow: 0 2px 8px #B5B68222;
}

@media (max-width: 768px) {
  .controls-card {
    flex-direction: column;
    gap: 20px;
    padding: 1rem 0.5rem;
    border-radius: 1rem;
  }
  .time-range-tabs,
  .chart-type-tabs {
    flex-wrap: wrap;
  }
}
</style>
