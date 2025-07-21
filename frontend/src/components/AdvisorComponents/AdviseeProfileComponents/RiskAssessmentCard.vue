<template>
  <div class="section-card risk-assessment">
    <div class="section-header">
      <h3>Risk Assessment</h3>
      <div class="risk-level" :class="getRiskLevelClass(riskLevel)">
        {{ riskLevel }}
      </div>
    </div>
    <div class="risk-factors">
      <div
        v-for="factor in riskFactors"
        :key="factor.name"
        class="risk-factor"
        :class="factor.level"
      >
        <div class="factor-header">
          <span class="factor-name">{{ factor.name }}</span>
          <span class="factor-score">{{ factor.score }}/10</span>
        </div>
        <div class="factor-bar">
          <div
            class="factor-fill"
            :style="{ width: (factor.score / 10) * 100 + '%' }"
            :class="factor.level"
          ></div>
        </div>
        <p class="factor-description">{{ factor.description }}</p>
      </div>
    </div>
    <div class="risk-recommendations">
      <h4>Recommendations:</h4>
      <ul>
        <li v-for="recommendation in recommendations" :key="recommendation">
          {{ recommendation }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: "RiskAssessmentCard",
  props: {
    riskLevel: {
      type: String,
      required: true,
      default: "Low Risk",
    },
    riskFactors: {
      type: Array,
      required: true,
      default: () => [],
    },
    recommendations: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  methods: {
    getRiskLevelClass(level) {
      const levelMap = {
        "Low Risk": "low-risk",
        "Medium Risk": "medium-risk",
        "High Risk": "high-risk",
        "Critical Risk": "critical-risk",
      };
      return levelMap[level] || "low-risk";
    },
  },
};
</script>

<style scoped>
.section-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
}

.risk-level {
  font-size: 0.8rem;
  padding: 6px 12px;
  border-radius: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.risk-level.low-risk {
  background: #dcfce7;
  color: #059669;
}

.risk-level.medium-risk {
  background: #fef3c7;
  color: #d97706;
}

.risk-level.high-risk {
  background: #fee2e2;
  color: #dc2626;
}

.risk-level.critical-risk {
  background: #fef2f2;
  color: #ef4444;
}

.risk-factors {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 20px;
}

.risk-factor {
  padding: 12px;
  border-radius: 8px;
  background: #f8fafc;
}

.factor-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.factor-name {
  font-weight: 600;
  color: #1e293b;
}

.factor-score {
  font-weight: 700;
  color: #64748b;
}

.factor-bar {
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.factor-fill {
  height: 100%;
  border-radius: 3px;
  transition: width 0.3s ease;
}

.factor-fill.excellent {
  background: #059669;
}

.factor-fill.good {
  background: #3b82f6;
}

.factor-fill.average {
  background: #f59e0b;
}

.factor-description {
  margin: 0;
  font-size: 0.8rem;
  color: #64748b;
  font-style: italic;
}

.risk-recommendations h4 {
  margin: 0 0 8px 0;
  font-weight: 600;
  color: #1e293b;
}

.risk-recommendations ul {
  margin: 0;
  padding-left: 16px;
}

.risk-recommendations li {
  color: #64748b;
  font-size: 0.9rem;
  margin-bottom: 4px;
}
</style>
