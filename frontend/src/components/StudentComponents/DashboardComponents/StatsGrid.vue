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
  background: #101010;
  padding: 40px 0;
}

.stat-card {
  background: linear-gradient(135deg, #0f5132 0%, #000 100%);
  border: 2px solid #198754;
  border-radius: 20px;
  box-shadow: 0 8px 32px 0 rgba(25, 135, 84, 0.25);
  width: 300px;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 32px 20px 24px 20px;
  position: relative;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: scale(1.04) translateY(-4px);
  box-shadow: 0 12px 40px 0 rgba(25, 135, 84, 0.35);
  border-color: #20c997;
}

.stat-icon {
  width: 56px;
  height: 56px;
  color: #20c997;
  background: #000;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 18px;
  border: 2px solid #198754;
  box-shadow: 0 2px 8px rgba(32, 201, 151, 0.15);
  font-size: 2.2rem;
}

.stat-content {
  text-align: center;
  width: 100%;
}

.stat-content h3 {
  margin: 0 0 10px 0;
  font-size: 1.05rem;
  color: #20c997;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.stat-value {
  font-size: 2.2rem;
  font-weight: 800;
  color: #fff;
  margin: 0 0 8px 0;
  letter-spacing: 1px;
  text-shadow: 0 2px 8px #19875444;
}

.stat-change {
  font-size: 0.95rem;
  font-weight: 700;
  padding: 4px 14px;
  border-radius: 16px;
  margin-top: 6px;
  display: inline-block;
  letter-spacing: 0.5px;
}

.stat-change.positive {
  color: #fff;
  background: linear-gradient(90deg, #198754 60%, #20c997 100%);
  box-shadow: 0 2px 8px #19875433;
}

.stat-change.negative {
  color: #fff;
  background: linear-gradient(90deg, #212529 60%, #dc3545 100%);
  box-shadow: 0 2px 8px #dc354533;
}

.stat-change.neutral {
  color: #adb5bd;
  background: #222;
  border: 1px solid #198754;
}

.stat-label {
  font-size: 0.95rem;
  color: #20c997;
  margin-top: 4px;
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
