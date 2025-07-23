<template>
  <div class="main-chart-section">
    <div class="chart-header">
      <h2>Performance Trends Over Time</h2>
      <div class="chart-legend">
        <div class="legend-item">
          <div class="legend-color your-performance"></div>
          <span>Your Performance</span>
        </div>
        <div class="legend-item" v-if="selectedCourseId === 'all'">
          <div class="legend-color class-average"></div>
          <span>Class Average</span>
        </div>
        <div class="legend-item">
          <div class="legend-color trend-line"></div>
          <span>Trend Line</span>
        </div>
      </div>
    </div>

    <div class="chart-container">
      <!-- Line Chart View -->
      <div v-if="chartType === 'line'" class="line-chart">
        <div class="chart-canvas">
          <svg width="100%" height="400" viewBox="0 0 800 400">
            <!-- Grid Lines -->
            <defs>
              <pattern
                id="grid"
                width="80"
                height="40"
                patternUnits="userSpaceOnUse"
              >
                <path
                  d="M 80 0 L 0 0 0 40"
                  fill="none"
                  stroke="#e2e8f0"
                  stroke-width="1"
                />
              </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />

            <!-- Y-axis labels -->
            <g class="y-axis">
              <text x="30" y="50" class="axis-label">100%</text>
              <text x="30" y="130" class="axis-label">80%</text>
              <text x="30" y="210" class="axis-label">60%</text>
              <text x="30" y="290" class="axis-label">40%</text>
              <text x="30" y="370" class="axis-label">20%</text>
            </g>

            <!-- Performance Line -->
            <polyline
              :points="getLinePoints(performanceData)"
              fill="none"
              stroke="#3b82f6"
              stroke-width="3"
              class="performance-line"
            />

            <!-- Performance Points -->
            <g class="performance-points">
              <circle
                v-for="(point, index) in performanceData"
                :key="index"
                :cx="50 + index * 70"
                :cy="370 - point.percentage * 3.2"
                r="6"
                fill="#3b82f6"
                class="data-point"
                @mouseover="showTooltip($event, point)"
                @mouseout="hideTooltip"
              />
            </g>

            <!-- Class Average Line (if showing all courses) -->
            <polyline
              v-if="selectedCourseId === 'all' && classAverageData.length > 0"
              :points="getLinePoints(classAverageData)"
              fill="none"
              stroke="#10b981"
              stroke-width="2"
              stroke-dasharray="5,5"
              class="class-average-line"
            />

            <!-- X-axis labels -->
            <g class="x-axis">
              <text
                v-for="(point, index) in performanceData"
                :key="index"
                :x="50 + index * 70"
                y="395"
                class="axis-label"
                text-anchor="middle"
              >
                {{ point.label }}
              </text>
            </g>
          </svg>
        </div>
      </div>

      <!-- Bar Chart View -->
      <div v-if="chartType === 'bar'" class="bar-chart">
        <div class="chart-bars">
          <div
            v-for="(point, index) in performanceData"
            :key="index"
            class="bar-group"
          >
            <div class="bar-container">
              <div
                class="performance-bar"
                :style="{ height: point.percentage + '%' }"
                @mouseover="showTooltip($event, point)"
                @mouseout="hideTooltip"
              ></div>
              <div
                v-if="selectedCourseId === 'all' && classAverageData[index]"
                class="average-bar"
                :style="{
                  height: (classAverageData[index]?.percentage || 0) + '%',
                }"
              ></div>
            </div>
            <div class="bar-label">{{ point.label }}</div>
          </div>
        </div>
      </div>

      <!-- Area Chart View -->
      <div v-if="chartType === 'area'" class="area-chart">
        <div class="chart-canvas">
          <svg width="100%" height="400" viewBox="0 0 800 400">
            <!-- Grid -->
            <rect width="100%" height="100%" fill="url(#grid)" />

            <!-- Performance Area -->
            <polygon
              :points="getAreaPoints(performanceData)"
              fill="rgba(59, 130, 246, 0.2)"
              stroke="#3b82f6"
              stroke-width="2"
              class="performance-area"
            />

            <!-- Y-axis labels -->
            <g class="y-axis">
              <text x="30" y="50" class="axis-label">100%</text>
              <text x="30" y="130" class="axis-label">80%</text>
              <text x="30" y="210" class="axis-label">60%</text>
              <text x="30" y="290" class="axis-label">40%</text>
              <text x="30" y="370" class="axis-label">20%</text>
            </g>

            <!-- Data Points -->
            <g class="performance-points">
              <circle
                v-for="(point, index) in performanceData"
                :key="index"
                :cx="50 + index * 70"
                :cy="370 - point.percentage * 3.2"
                r="5"
                fill="#3b82f6"
                class="data-point"
                @mouseover="showTooltip($event, point)"
                @mouseout="hideTooltip"
              />
            </g>

            <!-- X-axis labels -->
            <g class="x-axis">
              <text
                v-for="(point, index) in performanceData"
                :key="index"
                :x="50 + index * 70"
                y="395"
                class="axis-label"
                text-anchor="middle"
              >
                {{ point.label }}
              </text>
            </g>
          </svg>
        </div>
      </div>
    </div>

    <!-- Tooltip -->
    <div
      v-if="tooltip.show"
      class="chart-tooltip"
      :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }"
    >
      <div class="tooltip-content">
        <h4>{{ tooltip.data.name }}</h4>
        <p><strong>Score:</strong> {{ tooltip.data.score }}%</p>
        <p><strong>Date:</strong> {{ tooltip.data.date }}</p>
        <p v-if="tooltip.data.type">
          <strong>Type:</strong> {{ tooltip.data.type }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TrendsChart",
  props: {
    performanceData: {
      type: Array,
      required: true,
      default: () => [],
    },
    classAverageData: {
      type: Array,
      required: true,
      default: () => [],
    },
    chartType: {
      type: String,
      required: true,
      default: "line",
    },
    selectedCourseId: {
      type: [String, Number],
      required: true,
      default: "all",
    },
  },
  data() {
    return {
      tooltip: {
        show: false,
        x: 0,
        y: 0,
        data: {},
      },
    };
  },
  methods: {
    getLinePoints(data) {
      return data
        .map((point, index) => {
          const x = 50 + index * 70;
          const y = 370 - point.percentage * 3.2;
          return `${x},${y}`;
        })
        .join(" ");
    },

    getAreaPoints(data) {
      const points = data.map((point, index) => {
        const x = 50 + index * 70;
        const y = 370 - point.percentage * 3.2;
        return `${x},${y}`;
      });

      // Close the area by adding bottom points
      const firstX = 50;
      const lastX = 50 + (data.length - 1) * 70;
      points.push(`${lastX},370`);
      points.push(`${firstX},370`);

      return points.join(" ");
    },

    showTooltip(event, data) {
      const rect = event.target.getBoundingClientRect();
      this.tooltip = {
        show: true,
        x: rect.left + window.scrollX,
        y: rect.top + window.scrollY - 10,
        data,
      };
    },

    hideTooltip() {
      this.tooltip.show = false;
    },
  },
};
</script>

<style scoped>
.main-chart-section {
  background: rgba(255,255,255,0.92);
  border-radius: 1.5rem;
  padding: 2.5rem 2rem 2rem 2rem;
  margin-bottom: 30px;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  backdrop-filter: blur(6px);
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.chart-header h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 800;
  color: #7C9885;
}

.chart-legend {
  display: flex;
  gap: 20px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.98rem;
  color: #7C9885;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

.legend-color.your-performance {
  background: #7C9885;
}

.legend-color.class-average {
  background: #B5B682;
}

.legend-color.trend-line {
  background: #e6c972;
}

.chart-container {
  position: relative;
  background: rgba(181, 182, 130, 0.10);
  border-radius: 1.2rem;
  padding: 20px;
  min-height: 400px;
}

/* Line Chart Styles */
.line-chart {
  width: 100%;
  height: 400px;
}

.chart-canvas {
  width: 100%;
  height: 100%;
}

.axis-label {
  font-size: 12px;
  fill: #7C9885;
  font-weight: 600;
}

.performance-line {
  filter: drop-shadow(0 2px 4px #7C988544);
  stroke: #7C9885 !important;
}

.class-average-line {
  filter: drop-shadow(0 1px 2px #B5B68244);
  stroke: #B5B682 !important;
}

.data-point {
  cursor: pointer;
  transition: r 0.2s;
  fill: #7C9885 !important;
}

.data-point:hover {
  r: 8;
}

/* Bar Chart Styles */
.bar-chart {
  height: 400px;
  padding: 20px;
}

.chart-bars {
  display: flex;
  align-items: end;
  justify-content: space-around;
  height: 350px;
  gap: 10px;
}

.bar-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  max-width: 80px;
}

.bar-container {
  position: relative;
  height: 300px;
  width: 100%;
  display: flex;
  align-items: end;
  justify-content: center;
  gap: 4px;
}

.performance-bar,
.average-bar {
  width: 20px;
  border-radius: 4px 4px 0 0;
  transition: all 0.3s ease;
  cursor: pointer;
  min-height: 4px;
}

.performance-bar {
  background: #7C9885;
}

.average-bar {
  background: #B5B682;
  opacity: 0.7;
}

.performance-bar:hover {
  filter: brightness(1.1);
  transform: scaleY(1.05);
}

.bar-label {
  margin-top: 8px;
  font-size: 0.98rem;
  font-weight: 700;
  color: #7C9885;
}

/* Area Chart Styles */
.area-chart {
  width: 100%;
  height: 400px;
}

.performance-area {
  filter: drop-shadow(0 2px 8px #7C988544);
  fill: #7C988533 !important;
  stroke: #7C9885 !important;
}

/* Tooltip */
.chart-tooltip {
  position: absolute;
  background: #7C9885;
  color: #fff;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 0.98rem;
  box-shadow: 0 4px 12px rgba(124, 152, 133, 0.15);
  z-index: 1000;
  pointer-events: none;
  transform: translateX(-50%) translateY(-100%);
}

.tooltip-content h4 {
  margin: 0 0 8px 0;
  font-weight: 800;
}

.tooltip-content p {
  margin: 4px 0;
  font-size: 0.9rem;
  opacity: 0.95;
}

@media (max-width: 768px) {
  .chart-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }
  .chart-legend {
    flex-wrap: wrap;
  }
  .chart-bars {
    flex-wrap: wrap;
  }
}
</style>
