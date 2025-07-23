<template>
  <div class="grade-distribution">
    <div class="section-header">
      <h3>Grade Distribution</h3>
      <p class="section-subtitle">
        See how grades are distributed across the class
      </p>
    </div>

    <div class="distribution-content">
      <div class="distribution-chart">
        <div class="chart-container">
          <div class="chart-bars">
            <div
              v-for="grade in gradeDistribution"
              :key="grade.grade"
              class="grade-bar"
              :class="{
                'your-grade': grade.grade === selectedCourse.yourGrade,
              }"
            >
              <div
                class="grade-fill"
                :style="{
                  height:
                    (grade.count /
                      gradeDistribution.reduce(
                        (max, g) => Math.max(max, g.count),
                        0
                      )) *
                      100 +
                    '%',
                }"
              ></div>
              <div class="grade-label">{{ grade.grade }}</div>
              <div class="grade-count">{{ grade.count }}</div>
            </div>
          </div>
        </div>

        <div class="distribution-stats">
          <div class="stat-card">
            <h4>Your Grade</h4>
            <div class="grade-highlight">
              {{ selectedCourse.yourGrade }}
            </div>
            <p>
              {{ getGradePercentage(selectedCourse.yourGrade) }}% of class has
              this grade or lower
            </p>
          </div>

          <div class="stat-card">
            <h4>Most Common Grade</h4>
            <div class="grade-highlight">{{ getMostCommonGrade() }}</div>
            <p>
              {{ getMostCommonGradeCount() }} students ({{
                getMostCommonGradePercentage()
              }}%)
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "GradeDistribution",
  props: {
    gradeDistribution: {
      type: Array,
      required: true,
      default: () => [],
    },
    selectedCourse: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  methods: {
    getGradePercentage(grade) {
      const gradeOrder = ["A+", "A", "A-", "B+", "B", "B-", "C+", "C", "C-"];
      const gradeIndex = gradeOrder.indexOf(grade);
      const totalBelow = this.gradeDistribution
        .slice(gradeIndex + 1)
        .reduce((sum, g) => sum + g.count, 0);
      return Math.round((totalBelow / this.selectedCourse.totalStudents) * 100);
    },

    getMostCommonGrade() {
      return this.gradeDistribution.reduce((max, grade) =>
        grade.count > max.count ? grade : max
      ).grade;
    },

    getMostCommonGradeCount() {
      return this.gradeDistribution.reduce(
        (max, grade) => Math.max(max, grade.count),
        0
      );
    },

    getMostCommonGradePercentage() {
      return Math.round(
        (this.getMostCommonGradeCount() / this.selectedCourse.totalStudents) *
          100
      );
    },
  },
};
</script>

<style scoped>
.grade-distribution {
  background: rgba(255,255,255,0.97);
  border-radius: 1.2rem;
  padding: 2.5rem 2rem;
  margin-bottom: 2.5rem;
  box-shadow: 0 2px 16px rgba(181, 182, 130, 0.10);
  border: 2px solid #B5B682;
}

.section-header h3 {
  margin: 0;
  font-size: 2rem;
  font-weight: 800;
  color: #7C9885;
  letter-spacing: 1px;
  text-shadow: 0 2px 8px #1db95444;
}

.section-subtitle {
  margin: 6px 0 28px 0;
  color: #B5B682;
  font-size: 1.1rem;
  font-weight: 500;
}

.distribution-content {
  display: flex;
  flex-direction: row;
  gap: 48px;
}

.chart-container {
  background: #f9f9e0;
  border-radius: 1.2rem;
  padding: 2rem;
  border: 1.5px solid #B5B682;
  box-shadow: 0 2px 12px #B5B68222;
}

.chart-bars {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 240px;
  margin-bottom: 24px;
  gap: 16px;
}

.grade-bar {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  max-width: 64px;
  min-width: 36px;
}

.grade-fill {
  width: 100%;
  background: linear-gradient(180deg, #1db954 80%, #0a3d1e 100%);
  border-radius: 8px 8px 0 0;
  min-height: 6px;
  transition: all 0.3s cubic-bezier(.4,2,.6,1);
  box-shadow: 0 2px 8px #1db95444;
  border: 2px solid #1db954;
}

.grade-bar.your-grade .grade-fill {
  background: linear-gradient(180deg, #00ff88 70%, #1db954 100%);
  box-shadow: 0 0 0 4px #00ff8855, 0 4px 16px #1db95477;
  border: 2.5px solid #00ff88;
}

.grade-label {
  margin-top: 10px;
  font-weight: 900;
  color: #1db954;
  font-size: 1.1rem;
  letter-spacing: 0.5px;
  text-shadow: 0 1px 4px #1db95433;
}

.grade-bar.your-grade .grade-label {
  color: #00ff88;
  text-shadow: 0 2px 8px #00ff8844;
}

.grade-count {
  margin-top: 4px;
  font-size: 0.95rem;
  color: #b9ffb7;
  font-weight: 600;
}

.distribution-stats {
  display: flex;
  flex-direction: column;
  gap: 32px;
  justify-content: center;
}

.stat-card {
  background: #f9f9e0;
  border-radius: 1.2rem;
  padding: 2rem 1.5rem;
  text-align: center;
  border: 1.5px solid #B5B682;
  box-shadow: 0 2px 12px #B5B68222;
}

.stat-card h4 {
  margin: 0 0 14px 0;
  font-size: 1.1rem;
  color: #7C9885;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.grade-highlight {
  font-size: 2.4rem;
  font-weight: 900;
  color: #B5B682;
  margin-bottom: 10px;
  letter-spacing: 1px;
  text-shadow: none;
}

.stat-card p {
  margin: 0;
  font-size: 1rem;
  color: #23272f;
  font-weight: 600;
  opacity: 0.85;
}

@media (max-width: 1200px) {
  .distribution-content {
    flex-direction: column;
    gap: 32px;
  }
  .chart-container {
    padding: 20px;
  }
  .stat-card {
    padding: 18px 10px;
  }
}
</style>
