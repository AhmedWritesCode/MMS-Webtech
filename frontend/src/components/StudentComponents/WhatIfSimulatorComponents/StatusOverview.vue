<template>
  <div class="status-overview">
    <div class="overview-card current-status">
      <h3>Current Status</h3>
      <div class="status-metrics">
        <div class="metric">
          <span class="metric-label">Current Grade</span>
          <span
            class="metric-value grade"
            :class="getGradeClass(course.currentGrade)"
          >
            {{ course.currentGrade }}
          </span>
        </div>
        <div class="metric">
          <span class="metric-label">Current Average</span>
          <span class="metric-value">{{ course.currentMarks }}%</span>
        </div>
        <div class="metric">
          <span class="metric-label">Completed</span>
          <span class="metric-value">{{ course.completedWeight }}%</span>
        </div>
        <div class="metric">
          <span class="metric-label">Remaining</span>
          <span class="metric-value">{{ 100 - course.completedWeight }}%</span>
        </div>
      </div>
    </div>

    <div class="overview-card projected-grade">
      <h3>Projected Final Grade</h3>
      <div class="projection-display">
        <div
          class="projected-grade-large"
          :class="getGradeClass(projectedGrade.letter)"
        >
          {{ projectedGrade.letter }}
        </div>
        <div class="projection-details">
          <div class="projected-percentage">
            {{ projectedGrade.percentage }}%
          </div>
          <div class="projection-status" :class="projectedGrade.trend">
            {{ projectedGrade.status }}
          </div>
        </div>
      </div>
    </div>

    <div class="overview-card grade-goals">
      <h3>Grade Goals</h3>
      <div class="goals-list">
        <div
          v-for="goal in gradeGoals"
          :key="goal.grade"
          :class="[
            'goal-item',
            {
              achievable: goal.achievable,
              selected: selectedGoal === goal.grade,
            },
          ]"
          @click="selectGoal(goal.grade)"
        >
          <span class="goal-grade">{{ goal.grade }}</span>
          <span class="goal-requirement">{{ goal.required }}%</span>
          <span class="goal-status">{{ goal.achievable ? "✅" : "❌" }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "StatusOverview",
  props: {
    course: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    projectedGrade: {
      type: Object,
      required: true,
      default: () => ({
        letter: "N/A",
        percentage: 0,
        status: "",
        trend: "",
      }),
    },
    gradeGoals: {
      type: Array,
      required: true,
      default: () => [],
    },
    selectedGoal: {
      type: String,
      default: null,
    },
  },
  emits: ["goal-selected"],
  methods: {
    getGradeClass(grade) {
      const gradeMap = {
        "A+": "grade-a-plus",
        A: "grade-a",
        "A-": "grade-a-minus",
        "B+": "grade-b-plus",
        B: "grade-b",
        "B-": "grade-b-minus",
        "C+": "grade-c-plus",
        C: "grade-c",
      };
      return gradeMap[grade] || "grade-default";
    },

    selectGoal(grade) {
      this.$emit("goal-selected", grade);
    },
  },
};
</script>

<style scoped>
.status-overview {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.overview-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.overview-card h3 {
  margin: 0 0 20px 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

/* Current Status */
.status-metrics {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.metric {
  text-align: center;
}

.metric-label {
  display: block;
  font-size: 0.9rem;
  color: #64748b;
  margin-bottom: 8px;
}

.metric-value {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.metric-value.grade {
  font-size: 2rem;
}

.metric-value.grade.grade-a-plus,
.metric-value.grade.grade-a {
  color: #059669;
}
.metric-value.grade.grade-a-minus,
.metric-value.grade.grade-b-plus {
  color: #3b82f6;
}
.metric-value.grade.grade-b {
  color: #f59e0b;
}
.metric-value.grade.grade-b-minus,
.metric-value.grade.grade-c-plus,
.metric-value.grade.grade-c {
  color: #ef4444;
}

/* Projected Grade */
.projection-display {
  display: flex;
  align-items: center;
  gap: 20px;
}

.projected-grade-large {
  font-size: 3rem;
  font-weight: 700;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f1f5f9;
}

.projected-grade-large.grade-a-plus,
.projected-grade-large.grade-a {
  color: #059669;
  background: #dcfce7;
}
.projected-grade-large.grade-a-minus,
.projected-grade-large.grade-b-plus {
  color: #3b82f6;
  background: #dbeafe;
}
.projected-grade-large.grade-b {
  color: #f59e0b;
  background: #fef3c7;
}
.projected-grade-large.grade-b-minus,
.projected-grade-large.grade-c-plus,
.projected-grade-large.grade-c {
  color: #ef4444;
  background: #fee2e2;
}

.projection-details {
  flex: 1;
}

.projected-percentage {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.projection-status {
  font-size: 1rem;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 12px;
}

.projection-status.positive {
  color: #059669;
  background: #dcfce7;
}

.projection-status.negative {
  color: #dc2626;
  background: #fee2e2;
}

.projection-status.neutral {
  color: #64748b;
  background: #f1f5f9;
}

/* Grade Goals */
.goals-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.goal-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  border: 1px solid #e2e8f0;
}

.goal-item:hover {
  background: #f8fafc;
}

.goal-item.selected {
  border-color: #3b82f6;
  background: #eff6ff;
}

.goal-item.achievable {
  background: #f0fdf4;
  border-color: #22c55e;
}

.goal-item:not(.achievable) {
  background: #fef2f2;
  border-color: #ef4444;
  opacity: 0.7;
}

.goal-grade {
  font-weight: 700;
  font-size: 1.1rem;
}

.goal-requirement {
  color: #64748b;
  font-weight: 600;
}

.goal-status {
  font-size: 1.2rem;
}

@media (max-width: 768px) {
  .status-overview {
    grid-template-columns: 1fr;
  }

  .projection-display {
    flex-direction: column;
    text-align: center;
  }
}
</style>
