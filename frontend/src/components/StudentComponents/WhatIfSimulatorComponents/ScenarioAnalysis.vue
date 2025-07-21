<template>
  <div class="scenario-analysis">
    <h2>Scenario Analysis</h2>
    <div class="scenarios-grid">
      <div class="scenario-card best-case">
        <div class="scenario-header">
          <h3>🌟 Best Case Scenario</h3>
          <div
            class="scenario-grade"
            :class="getGradeClass(scenarios.bestCase.grade)"
          >
            {{ scenarios.bestCase.grade }}
          </div>
        </div>
        <div class="scenario-content">
          <div class="scenario-percentage">
            {{ scenarios.bestCase.percentage }}%
          </div>
          <p class="scenario-description">
            {{ scenarios.bestCase.description }}
          </p>
          <div class="scenario-requirements">
            <h4>Requirements:</h4>
            <ul>
              <li v-for="req in scenarios.bestCase.requirements" :key="req">
                {{ req }}
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="scenario-card realistic-case">
        <div class="scenario-header">
          <h3>🎯 Realistic Scenario</h3>
          <div
            class="scenario-grade"
            :class="getGradeClass(scenarios.realistic.grade)"
          >
            {{ scenarios.realistic.grade }}
          </div>
        </div>
        <div class="scenario-content">
          <div class="scenario-percentage">
            {{ scenarios.realistic.percentage }}%
          </div>
          <p class="scenario-description">
            {{ scenarios.realistic.description }}
          </p>
          <div class="scenario-requirements">
            <h4>Requirements:</h4>
            <ul>
              <li v-for="req in scenarios.realistic.requirements" :key="req">
                {{ req }}
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="scenario-card worst-case">
        <div class="scenario-header">
          <h3>⚠️ Worst Case Scenario</h3>
          <div
            class="scenario-grade"
            :class="getGradeClass(scenarios.worstCase.grade)"
          >
            {{ scenarios.worstCase.grade }}
          </div>
        </div>
        <div class="scenario-content">
          <div class="scenario-percentage">
            {{ scenarios.worstCase.percentage }}%
          </div>
          <p class="scenario-description">
            {{ scenarios.worstCase.description }}
          </p>
          <div class="scenario-requirements">
            <h4>To avoid this:</h4>
            <ul>
              <li v-for="req in scenarios.worstCase.requirements" :key="req">
                {{ req }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ScenarioAnalysis",
  props: {
    scenarios: {
      type: Object,
      required: true,
      default: () => ({
        bestCase: {},
        realistic: {},
        worstCase: {},
      }),
    },
  },
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
        F: "grade-f",
      };
      return gradeMap[grade] || "grade-default";
    },
  },
};
</script>

<style scoped>
.scenario-analysis {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.scenario-analysis h2 {
  margin: 0 0 24px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.scenarios-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.scenario-card {
  border-radius: 12px;
  padding: 20px;
  border-left: 4px solid;
}

.scenario-card.best-case {
  background: #f0fdf4;
  border-left-color: #22c55e;
}

.scenario-card.realistic-case {
  background: #eff6ff;
  border-left-color: #3b82f6;
}

.scenario-card.worst-case {
  background: #fef2f2;
  border-left-color: #ef4444;
}

.scenario-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.scenario-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.scenario-grade {
  font-size: 1.5rem;
  font-weight: 700;
  padding: 8px 12px;
  border-radius: 8px;
  background: white;
}

.scenario-grade.grade-a-plus,
.scenario-grade.grade-a {
  color: #059669;
}
.scenario-grade.grade-a-minus,
.scenario-grade.grade-b-plus {
  color: #3b82f6;
}
.scenario-grade.grade-b {
  color: #f59e0b;
}
.scenario-grade.grade-b-minus,
.scenario-grade.grade-c-plus,
.scenario-grade.grade-c {
  color: #ef4444;
}
.scenario-grade.grade-f {
  color: #dc2626;
}

.scenario-percentage {
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 8px;
}

.scenario-description {
  color: #64748b;
  margin-bottom: 16px;
  font-size: 0.9rem;
}

.scenario-requirements h4 {
  margin: 0 0 8px 0;
  font-size: 0.9rem;
  font-weight: 700;
  color: #1e293b;
}

.scenario-requirements ul {
  margin: 0;
  padding-left: 16px;
  font-size: 0.8rem;
  color: #64748b;
}

.scenario-requirements li {
  margin-bottom: 4px;
}

@media (max-width: 768px) {
  .scenarios-grid {
    grid-template-columns: 1fr;
  }
}
</style>
