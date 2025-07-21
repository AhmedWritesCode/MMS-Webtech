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

.distribution-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 40px;
}

.chart-container {
  background: #f8fafc;
  border-radius: 12px;
  padding: 24px;
}

.chart-bars {
  display: flex;
  align-items: end;
  justify-content: space-around;
  height: 200px;
  margin-bottom: 20px;
  gap: 8px;
}

.grade-bar {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  max-width: 60px;
}

.grade-fill {
  width: 100%;
  background: #94a3b8;
  border-radius: 4px 4px 0 0;
  min-height: 4px;
  transition: all 0.3s ease;
}

.grade-bar.your-grade .grade-fill {
  background: #3b82f6;
  box-shadow: 0 0 0 2px #1e40af;
}

.grade-label {
  margin-top: 8px;
  font-weight: 700;
  color: #1e293b;
  font-size: 0.9rem;
}

.grade-bar.your-grade .grade-label {
  color: #3b82f6;
}

.grade-count {
  margin-top: 4px;
  font-size: 0.8rem;
  color: #64748b;
}

.distribution-stats {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.stat-card {
  background: #f8fafc;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
}

.stat-card h4 {
  margin: 0 0 12px 0;
  font-size: 1rem;
  color: #64748b;
  font-weight: 600;
}

.grade-highlight {
  font-size: 2rem;
  font-weight: 700;
  color: #3b82f6;
  margin-bottom: 8px;
}

.stat-card p {
  margin: 0;
  font-size: 0.9rem;
  color: #64748b;
}

@media (max-width: 1200px) {
  .distribution-content {
    grid-template-columns: 1fr;
  }
}
</style>
