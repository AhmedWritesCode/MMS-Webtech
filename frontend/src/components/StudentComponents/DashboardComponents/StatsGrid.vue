<template>
  <div class="stats-grid">
    <div class="stat-card">
      <BarChart2 class="stat-icon" />
      <div class="stat-content">
        <h3>Overall GPA</h3>
        <p class="stat-value">{{ overallGPA }}</p>
        <span class="stat-change" :class="gpaChangeClass">
          {{ formattedGpaChange }}
        </span>
      </div>
    </div>

    <div class="stat-card">
      <Book class="stat-icon" />
      <div class="stat-content">
        <h3>Enrolled Courses</h3>
        <p class="stat-value">{{ enrolledCoursesCount }}</p>
        <span class="stat-label">Active</span>
      </div>
    </div>

    <div class="stat-card">
      <Award class="stat-icon" />
      <div class="stat-content">
        <h3>Class Rank</h3>
        <p class="stat-value">#{{ classRank }}</p>
        <span class="stat-label">of {{ totalStudents }}</span>
      </div>
    </div>

    <div class="stat-card">
      <TrendingUp class="stat-icon" />
      <div class="stat-content">
        <h3>Percentile</h3>
        <p class="stat-value">{{ percentile }}%</p>
        <span class="stat-label">Above average</span>
      </div>
    </div>
  </div>
</template>

<script>
import { BarChart2, Book, Award, TrendingUp } from 'lucide-vue-next';
export default {
  name: "StatsGrid",
  components: { BarChart2, Book, Award, TrendingUp },
  props: {
    overallGPA: {
      type: [String, Number],
      required: true,
      default: "0.00",
    },
    gpaChange: {
      type: String,
      required: true,
      default: "+0.00",
    },
    enrolledCoursesCount: {
      type: Number,
      required: true,
      default: 0,
    },
    classRank: {
      type: Number,
      required: true,
      default: 0,
    },
    totalStudents: {
      type: Number,
      required: true,
      default: 0,
    },
    percentile: {
      type: Number,
      required: true,
      default: 0,
    },
  },
  computed: {
    gpaChangeClass() {
      const change = String(this.gpaChange);
      if (change.startsWith("+")) {
        return "positive";
      } else if (change.startsWith("-")) {
        return "negative";
      }
      return "neutral";
    },
    formattedGpaChange() {
      const change = this.gpaChange;
      if (typeof change === "number") {
        return change >= 0 ? `+${change.toFixed(2)}` : `${change.toFixed(2)}`;
      }
      return change;
    },
  },
};
</script>

<style scoped>
.stats-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 32px;
  justify-content: center;
  background: transparent;
  padding: 40px 0;
}

.stat-card {
  background: rgba(181, 182, 130, 0.18);
  border: 2px solid #B5B682;
  border-radius: 2rem;
  box-shadow: 0 8px 32px 0 rgba(124, 152, 133, 0.10), 0 1.5px 8px 0 rgba(0,0,0,0.04);
  width: 300px;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 40px 24px 28px 24px;
  position: relative;
  transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
  backdrop-filter: blur(8px);
}

.stat-card:hover {
  transform: scale(1.045) translateY(-6px);
  box-shadow: 0 16px 48px 0 rgba(124, 152, 133, 0.18), 0 2px 12px 0 rgba(0,0,0,0.06);
  background: rgba(181, 182, 130, 0.28);
  border-color: #7C9885;
}

.stat-icon {
  width: 60px;
  height: 60px;
  color: #7C9885;
  background: rgba(255,255,255,0.7);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 22px;
  border: 2.5px solid #B5B682;
  box-shadow: 0 2px 12px rgba(124, 152, 133, 0.10);
  font-size: 2.4rem;
  transition: background 0.2s, color 0.2s;
}

.stat-card:hover .stat-icon {
  background: #7C9885;
  color: #fff;
  border-color: #B5B682;
}

.stat-content {
  text-align: center;
  width: 100%;
}

.stat-content h3 {
  margin: 0 0 12px 0;
  font-size: 1.15rem;
  color: #7C9885;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.stat-value {
  font-size: 2.5rem;
  font-weight: 900;
  color: #23272f;
  margin: 0 0 10px 0;
  letter-spacing: 1.5px;
  text-shadow: 0 2px 8px #B5B68244;
}

.stat-change {
  font-size: 1.05rem;
  font-weight: 700;
  padding: 6px 18px;
  border-radius: 18px;
  margin-top: 8px;
  display: inline-block;
  letter-spacing: 0.5px;
}

.stat-change.positive {
  color: #23272f;
  background: linear-gradient(90deg, #B5B682 60%, #7C9885 100%);
  box-shadow: 0 2px 8px #B5B68233;
}

.stat-change.negative {
  color: #fff;
  background: linear-gradient(90deg, #23272f 60%, #e74c3c 100%);
  box-shadow: 0 2px 8px #e74c3c33;
}

.stat-change.neutral {
  color: #7C9885;
  background: rgba(255,255,255,0.7);
  border: 1.5px solid #B5B682;
}

.stat-label {
  font-size: 1.05rem;
  color: #7C9885;
  margin-top: 6px;
  letter-spacing: 0.5px;
  font-weight: 600;
}

/* Responsive Design */
@media (max-width: 900px) {
  .stats-grid {
    flex-direction: column;
    align-items: center;
    gap: 24px;
    padding: 24px 0;
  }
  .stat-card {
    width: 90vw;
    min-width: 0;
    max-width: 400px;
  }
}
</style>
