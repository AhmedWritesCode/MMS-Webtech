<template>
  <div class="anonymous-insights">
    <h3>Anonymous Class Insights</h3>
    <div class="insights-grid">
      <div class="insight-card">
        <Lightbulb class="insight-icon" />
        <div class="insight-content">
          <h4>Most Challenging Assessment</h4>
          <p>{{ getMostChallengingAssessment() }}</p>
          <span class="insight-detail">Based on class average scores</span>
        </div>
      </div>

      <div class="insight-card">
        <Star class="insight-icon" />
        <div class="insight-content">
          <h4>Best Class Performance</h4>
          <p>{{ getBestClassPerformance() }}</p>
          <span class="insight-detail">Highest average score achieved</span>
        </div>
      </div>

      <div class="insight-card">
        <Book class="insight-icon" />
        <div class="insight-content">
          <h4>Improvement Opportunity</h4>
          <p>{{ getImprovementOpportunity() }}</p>
          <span class="insight-detail">Area where you can gain the most</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Lightbulb, Star, Book } from 'lucide-vue-next';
export default {
  name: "AnonymousInsights",
  components: {
    Lightbulb,
    Star,
    Book,
  },
  props: {
    selectedCourse: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    comparisonData: {
      type: Object,
      required: false,
      default: () => ({}),
    },
  },
  methods: {
    getMostChallengingAssessment() {
      if (
        !this.comparisonData.comparisons ||
        this.comparisonData.comparisons.length === 0
      ) {
        return "No assessments available";
      }

      return this.comparisonData.comparisons.reduce((min, assessment) => {
        const minPercentage = min.class_average_percentage || 0;
        const currentPercentage = assessment.class_average_percentage || 0;
        return currentPercentage < minPercentage ? assessment : min;
      }).component_name;
    },

    getBestClassPerformance() {
      if (
        !this.comparisonData.comparisons ||
        this.comparisonData.comparisons.length === 0
      ) {
        return "No assessments available";
      }

      return this.comparisonData.comparisons.reduce((max, assessment) => {
        const maxPercentage = max.class_average_percentage || 0;
        const currentPercentage = assessment.class_average_percentage || 0;
        return currentPercentage > maxPercentage ? assessment : max;
      }).component_name;
    },

    getImprovementOpportunity() {
      if (
        !this.comparisonData.comparisons ||
        this.comparisonData.comparisons.length === 0
      ) {
        return "No assessments available";
      }

      return this.comparisonData.comparisons.reduce((min, assessment) => {
        const yourPercentage = assessment.my_percentage || 0;
        const classPercentage = assessment.class_average_percentage || 0;
        const gap = classPercentage - yourPercentage;

        const minYourPercentage = min.my_percentage || 0;
        const minClassPercentage = min.class_average_percentage || 0;
        const minGap = minClassPercentage - minYourPercentage;

        return gap > minGap ? assessment : min;
      }).component_name;
    },
  },
};
</script>

<style scoped>
.anonymous-insights {
  background: #111;
  border-radius: 18px;
  padding: 36px 24px;
  margin-bottom: 32px;
  box-shadow: 0 4px 24px rgba(0, 128, 0, 0.15);
  border: 2px solid #22c55e;
  transition: box-shadow 0.2s;
}

.anonymous-insights h3 {
  margin: 0 0 32px 0;
  font-size: 2rem;
  font-weight: 800;
  color: #22c55e;
  letter-spacing: 2px;
  text-align: center;
  text-shadow: 0 2px 8px #0f0;
}

.insights-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 28px;
  justify-content: center;
}

.insight-card {
  background: linear-gradient(135deg, #1a2e1a 0%, #111 100%);
  border-radius: 16px;
  padding: 32px 24px;
  border: 2px solid #22c55e;
  min-width: 260px;
  max-width: 340px;
  flex: 1 1 300px;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: 0 2px 16px rgba(34, 197, 94, 0.15);
  transition: transform 0.15s, box-shadow 0.15s;
}
.insight-card:hover {
  transform: translateY(-6px) scale(1.03);
  box-shadow: 0 8px 32px rgba(34, 197, 94, 0.25);
  border-color: #16a34a;
}

.insight-icon {
  font-size: 2.5rem;
  color: #22c55e;
  margin-bottom: 18px;
  filter: drop-shadow(0 0 8px #22c55e88);
}

.insight-content h4 {
  margin: 0 0 14px 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #22c55e;
  letter-spacing: 1px;
  text-align: center;
}

.insight-content p {
  margin: 0 0 10px 0;
  font-size: 1.05rem;
  color: #fff;
  font-weight: 600;
  text-align: center;
  background: rgba(34, 197, 94, 0.08);
  border-radius: 6px;
  padding: 6px 0;
}

.insight-detail {
  font-size: 0.85rem;
  color: #a7f3d0;
  font-style: italic;
  text-align: center;
  display: block;
  margin-top: 4px;
}

@media (max-width: 900px) {
  .insights-grid {
    flex-direction: column;
    gap: 20px;
    align-items: stretch;
  }
  .insight-card {
    max-width: 100%;
    min-width: 0;
  }
}
</style>
