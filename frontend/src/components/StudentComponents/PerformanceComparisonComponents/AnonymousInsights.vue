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
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.anonymous-insights h3 {
  margin: 0 0 24px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.insights-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.insight-card {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-radius: 12px;
  padding: 24px;
  border-left: 4px solid #3b82f6;
}

.insight-icon {
  font-size: 2rem;
  margin-bottom: 16px;
}

.insight-content h4 {
  margin: 0 0 12px 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.insight-content p {
  margin: 0 0 8px 0;
  font-size: 1rem;
  color: #1e293b;
  font-weight: 600;
}

.insight-detail {
  font-size: 0.8rem;
  color: #64748b;
  font-style: italic;
}

@media (max-width: 768px) {
  .insights-grid {
    grid-template-columns: 1fr;
  }
}
</style>
