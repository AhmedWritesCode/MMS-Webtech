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
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
}

.control-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-width: 200px;
}

.control-group label {
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9rem;
}

.course-select {
  padding: 8px 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  transition: border-color 0.2s;
}

.course-select:focus {
  outline: none;
  border-color: #3b82f6;
}

.time-range-tabs,
.chart-type-tabs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.time-tab,
.chart-tab {
  padding: 8px 16px;
  border: 2px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 20px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
}

.time-tab.active,
.time-tab:hover,
.chart-tab.active,
.chart-tab:hover {
  border-color: #3b82f6;
  color: #3b82f6;
  background: #eff6ff;
}

@media (max-width: 768px) {
  .controls-card {
    flex-direction: column;
    gap: 20px;
  }

  .time-range-tabs,
  .chart-type-tabs {
    flex-wrap: wrap;
  }
}
</style>
