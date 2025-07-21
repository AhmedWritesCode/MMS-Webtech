<template>
  <div class="overview-summary">
    <div class="summary-cards">
      <div class="summary-card trend-card">
        <div class="card-icon">📈</div>
        <div class="card-content">
          <h3>Overall Trend</h3>
          <div
            class="trend-value"
            :class="getTrendClass(overallTrend.direction)"
          >
            {{
              overallTrend.direction === "up"
                ? "↗"
                : overallTrend.direction === "down"
                ? "↘"
                : "→"
            }}
            {{ Math.abs(overallTrend.percentage) }}%
          </div>
          <p class="trend-description">{{ overallTrend.description }}</p>
        </div>
      </div>

      <div class="summary-card average-card">
        <div class="card-icon">🎯</div>
        <div class="card-content">
          <h3>Current Average</h3>
          <div class="average-value">{{ currentAverage }}%</div>
          <p class="average-description">Across selected courses</p>
        </div>
      </div>

      <div class="summary-card best-card">
        <div class="card-icon">⭐</div>
        <div class="card-content">
          <h3>Best Performance</h3>
          <div class="best-value">{{ bestPerformance.score }}%</div>
          <p class="best-description">{{ bestPerformance.assessment }}</p>
        </div>
      </div>

      <div class="summary-card improvement-card">
        <div class="card-icon">🚀</div>
        <div class="card-content">
          <h3>Biggest Improvement</h3>
          <div class="improvement-value">
            +{{ biggestImprovement.percentage }}%
          </div>
          <p class="improvement-description">
            {{ biggestImprovement.period }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "OverviewSummary",
  props: {
    overallTrend: {
      type: Object,
      required: true,
      default: () => ({
        direction: "stable",
        percentage: 0,
        description: "Stable performance",
      }),
    },
    currentAverage: {
      type: [String, Number],
      required: true,
      default: 0,
    },
    bestPerformance: {
      type: Object,
      required: true,
      default: () => ({
        score: 0,
        assessment: "No data",
      }),
    },
    biggestImprovement: {
      type: Object,
      required: true,
      default: () => ({
        percentage: 0,
        period: "No improvement data",
      }),
    },
  },
  methods: {
    getTrendClass(direction) {
      return `trend-${direction}`;
    },
  },
};
</script>

<style scoped>
.overview-summary {
  margin-bottom: 30px;
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.summary-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 16px;
  transition: transform 0.2s;
}

.summary-card:hover {
  transform: translateY(-2px);
}

.card-icon {
  font-size: 2.5rem;
  width: 70px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
}

.trend-card .card-icon {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.average-card .card-icon {
  background: linear-gradient(135deg, #10b981, #059669);
}

.best-card .card-icon {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.improvement-card .card-icon {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.card-content h3 {
  margin: 0 0 8px 0;
  font-size: 1rem;
  color: #64748b;
  font-weight: 600;
}

.trend-value,
.average-value,
.best-value,
.improvement-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.trend-value.trend-up {
  color: #10b981;
}

.trend-value.trend-down {
  color: #ef4444;
}

.trend-value.trend-stable {
  color: #64748b;
}

.trend-description,
.average-description,
.best-description,
.improvement-description {
  margin: 0;
  font-size: 0.9rem;
  color: #64748b;
}

@media (max-width: 768px) {
  .summary-cards {
    grid-template-columns: 1fr;
  }

  .summary-card {
    flex-direction: column;
    text-align: center;
  }
}
</style>
