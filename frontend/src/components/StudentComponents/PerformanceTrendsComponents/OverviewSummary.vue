<template>
  <div class="overview-summary">
    <div class="summary-cards">
      <div class="summary-card trend-card">
        <div class="card-icon lucide-icon-bg"><TrendingUp class="lucide-icon" /></div>
        <div class="card-content">
          <h3>Overall Trend</h3>
          <div
            class="trend-value"
            :class="getTrendClass(overallTrend.direction)"
          >
            {{
              overallTrend.direction === "up"
                ? "\u2197"
                : overallTrend.direction === "down"
                ? "\u2198"
                : "\u2192"
            }}
            {{ Math.abs(overallTrend.percentage) }}%
          </div>
          <p class="trend-description">{{ overallTrend.description }}</p>
        </div>
      </div>

      <div class="summary-card average-card">
        <div class="card-icon lucide-icon-bg"><Target class="lucide-icon" /></div>
        <div class="card-content">
          <h3>Current Average</h3>
          <div class="average-value">{{ currentAverage }}%</div>
          <p class="average-description">Across selected courses</p>
        </div>
      </div>

      <div class="summary-card best-card">
        <div class="card-icon lucide-icon-bg"><Star class="lucide-icon" /></div>
        <div class="card-content">
          <h3>Best Performance</h3>
          <div class="best-value">{{ bestPerformance.score }}%</div>
          <p class="best-description">{{ bestPerformance.assessment }}</p>
        </div>
      </div>

      <div class="summary-card improvement-card">
        <div class="card-icon lucide-icon-bg"><Rocket class="lucide-icon" /></div>
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
import { TrendingUp, Target, Star, Rocket } from 'lucide-vue-next';
export default {
  name: "OverviewSummary",
  components: { TrendingUp, Target, Star, Rocket },
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
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}

.summary-card {
  background: rgba(255,255,255,0.92);
  border-radius: 1.5rem;
  padding: 2.2rem 1.5rem 1.5rem 1.5rem;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  display: flex;
  align-items: center;
  gap: 18px;
  transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
  backdrop-filter: blur(6px);
}

.summary-card:hover {
  transform: translateY(-4px) scale(1.025);
  box-shadow: 0 8px 32px rgba(124, 152, 133, 0.13);
  background: rgba(255,255,255,0.98);
}

.card-icon {
  font-size: 2.5rem;
  width: 70px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 1.2rem;
  color: #fff;
  box-shadow: 0 2px 8px #B5B68222;
}

.trend-card .card-icon {
  background: linear-gradient(135deg, #B5B682 0%, #7C9885 100%);
}

.average-card .card-icon {
  background: linear-gradient(135deg, #7C9885 0%, #B5B682 100%);
}

.best-card .card-icon {
  background: linear-gradient(135deg, #B5B682 60%, #7C9885 100%);
}

.improvement-card .card-icon {
  background: linear-gradient(135deg, #7C9885 60%, #B5B682 100%);
}

.card-content h3 {
  margin: 0 0 8px 0;
  font-size: 1.1rem;
  color: #7C9885;
  font-weight: 700;
}

.trend-value,
.average-value,
.best-value,
.improvement-value {
  font-size: 2.1rem;
  font-weight: 800;
  color: #23272f;
  margin-bottom: 4px;
  letter-spacing: 1px;
}

.trend-value.trend-up {
  color: #7C9885;
}

.trend-value.trend-down {
  color: #e74c3c;
}

.trend-value.trend-stable {
  color: #B5B682;
}

.trend-description,
.average-description,
.best-description,
.improvement-description {
  margin: 0;
  font-size: 0.98rem;
  color: #7C9885;
}

.lucide-icon-bg {
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.7);
  border-radius: 1.2rem;
  border: 2px solid #B5B682;
  box-shadow: 0 2px 8px #B5B68222;
  width: 70px;
  height: 70px;
}
.lucide-icon {
  width: 2.1rem;
  height: 2.1rem;
  color: #fff;
}
.trend-card .lucide-icon-bg {
  background: linear-gradient(135deg, #B5B682 0%, #7C9885 100%);
}
.average-card .lucide-icon-bg {
  background: linear-gradient(135deg, #7C9885 0%, #B5B682 100%);
}
.best-card .lucide-icon-bg {
  background: linear-gradient(135deg, #B5B682 60%, #7C9885 100%);
}
.improvement-card .lucide-icon-bg {
  background: linear-gradient(135deg, #7C9885 60%, #B5B682 100%);
}

@media (max-width: 900px) {
  .summary-cards {
    grid-template-columns: 1fr;
  }
  .summary-card {
    flex-direction: column;
    text-align: center;
  }
}
</style>
