<template>
  <div class="assessments-section">
    <div class="section-header">
      <h2>Assessment Components</h2>
      <div class="filter-tabs">
        <button
          v-for="filter in filters"
          :key="filter.key"
          :class="['filter-tab', { active: activeFilter === filter.key }]"
          @click="updateFilter(filter.key)"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <div class="assessments-grid">
      <div
        v-for="assessment in filteredAssessments"
        :key="assessment.id"
        class="assessment-card"
        :class="getAssessmentStatusClass(assessment)"
      >
        <div class="assessment-header">
          <div class="assessment-title">
            <h3>{{ assessment.name }}</h3>
            <span class="assessment-type">{{
              formatAssessmentType(assessment.type)
            }}</span>
          </div>
          <div class="assessment-weight">{{ assessment.weight }}%</div>
        </div>

        <div class="assessment-content">
          <div class="marks-section">
            <div class="marks-display">
              <span
                class="marks-obtained"
                v-if="assessment.marksObtained !== null"
              >
                {{ assessment.marksObtained }}
              </span>
              <span class="marks-pending" v-else> -- </span>
              <span class="marks-total">/ {{ assessment.maxMarks }}</span>
            </div>

            <div
              class="percentage-display"
              v-if="assessment.marksObtained !== null"
            >
              {{
                Math.round(
                  (assessment.marksObtained / assessment.maxMarks) * 100
                )
              }}%
            </div>
          </div>

          <div class="assessment-details">
            <div class="detail-row" v-if="assessment.dueDate">
              <span class="detail-icon">📅</span>
              <span class="detail-text">
                Due: {{ formatDate(assessment.dueDate) }}
              </span>
            </div>

            <div class="detail-row" v-if="assessment.gradedAt">
              <span class="detail-icon">✅</span>
              <span class="detail-text">
                Graded: {{ formatDate(assessment.gradedAt) }}
              </span>
            </div>

            <div class="detail-row" v-if="assessment.description">
              <span class="detail-icon">📝</span>
              <span class="detail-text">{{ assessment.description }}</span>
            </div>
          </div>

          <!-- Lecturer Feedback -->
          <div class="feedback-section" v-if="assessment.remarks">
            <div class="feedback-header">
              <span class="feedback-icon">💬</span>
              <span class="feedback-title">Lecturer Feedback</span>
            </div>
            <p class="feedback-text">{{ assessment.remarks }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="assessment-actions">
            <button
              v-if="
                assessment.marksObtained !== null &&
                canRequestRemark(assessment)
              "
              class="action-btn remark-btn"
              @click="requestRemark(assessment.id)"
            >
              Request Remark
            </button>

            <button
              v-if="assessment.marksObtained !== null"
              class="action-btn details-btn"
              @click="viewAssessmentDetails(assessment.id)"
            >
              View Details
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AssessmentsSection",
  props: {
    assessments: {
      type: Array,
      required: true,
      default: () => [],
    },
    activeFilter: {
      type: String,
      required: true,
      default: "all",
    },
  },
  emits: ["update-filter", "request-remark", "view-assessment-details"],
  data() {
    return {
      filters: [
        { key: "all", label: "All" },
        { key: "completed", label: "Completed" },
        { key: "pending", label: "Pending" },
        { key: "quiz", label: "Quizzes" },
        { key: "assignment", label: "Assignments" },
      ],
    };
  },
  computed: {
    filteredAssessments() {
      if (this.activeFilter === "all") return this.assessments;
      if (this.activeFilter === "completed")
        return this.assessments.filter((a) => a.status === "completed");
      if (this.activeFilter === "pending")
        return this.assessments.filter((a) => a.status === "pending");
      return this.assessments.filter((a) => a.type === this.activeFilter);
    },
  },
  methods: {
    updateFilter(filterKey) {
      this.$emit("update-filter", filterKey);
    },

    getAssessmentStatusClass(assessment) {
      return `status-${assessment.status}`;
    },

    formatAssessmentType(type) {
      return type.replace("_", " ").toUpperCase();
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },

    canRequestRemark(assessment) {
      // Can request remark within 7 days of grading
      if (!assessment.gradedAt) return false;
      const gradedDate = new Date(assessment.gradedAt);
      const now = new Date();
      const daysDiff = (now - gradedDate) / (1000 * 60 * 60 * 24);
      return daysDiff <= 7;
    },

    requestRemark(assessmentId) {
      this.$emit("request-remark", assessmentId);
    },

    viewAssessmentDetails(assessmentId) {
      this.$emit("view-assessment-details", assessmentId);
    },
  },
};
</script>

<style scoped>
.assessments-section {
  margin-bottom: 30px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.section-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.filter-tabs {
  display: flex;
  gap: 8px;
}

.filter-tab {
  padding: 8px 16px;
  border: 2px solid #e2e8f0;
  background: white;
  border-radius: 20px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-tab.active,
.filter-tab:hover {
  border-color: #3b82f6;
  color: #3b82f6;
  background: #eff6ff;
}

.assessments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 20px;
}

.assessment-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #e2e8f0;
  transition: transform 0.2s, box-shadow 0.2s;
}

.assessment-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.assessment-card.status-completed {
  border-left-color: #10b981;
}

.assessment-card.status-pending {
  border-left-color: #f59e0b;
}

.assessment-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}

.assessment-title h3 {
  margin: 0 0 4px 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
}

.assessment-type {
  background: #f1f5f9;
  color: #64748b;
  padding: 2px 8px;
  border-radius: 6px;
  font-size: 0.7rem;
  font-weight: 600;
}

.assessment-weight {
  background: #3b82f6;
  color: white;
  padding: 6px 12px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.9rem;
}

.marks-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding: 16px;
  background: #f8fafc;
  border-radius: 8px;
}

.marks-display {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.marks-obtained {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1e293b;
}

.marks-pending {
  font-size: 1.8rem;
  font-weight: 700;
  color: #94a3b8;
}

.marks-total {
  font-size: 1.2rem;
  color: #64748b;
}

.percentage-display {
  font-size: 1.1rem;
  font-weight: 700;
  color: #3b82f6;
}

.assessment-details {
  margin-bottom: 16px;
}

.detail-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  font-size: 0.9rem;
  color: #64748b;
}

.detail-icon {
  font-size: 1rem;
}

.feedback-section {
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 16px;
}

.feedback-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.feedback-title {
  font-weight: 600;
  color: #0c4a6e;
}

.feedback-text {
  margin: 0;
  color: #164e63;
  font-style: italic;
}

.assessment-actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
}

.remark-btn {
  background: #fef3c7;
  color: #92400e;
}

.remark-btn:hover {
  background: #fde68a;
}

.details-btn {
  background: #e0e7ff;
  color: #3730a3;
}

.details-btn:hover {
  background: #c7d2fe;
}

@media (max-width: 768px) {
  .assessments-grid {
    grid-template-columns: 1fr;
  }

  .filter-tabs {
    flex-wrap: wrap;
  }

  .assessment-actions {
    flex-direction: column;
  }
}
</style>
