<template>
  <div class="comparison-controls">
    <div class="control-group">
      <label>Comparison Type:</label>
      <div class="control-tabs">
        <button
          v-for="type in comparisonTypes"
          :key="type.key"
          :class="[
            'control-tab',
            { active: selectedComparisonType === type.key },
          ]"
          @click="updateComparisonType(type.key)"
        >
          {{ type.label }}
        </button>
      </div>
    </div>

    <div class="control-group">
      <label>View Type:</label>
      <div class="control-tabs">
        <button
          v-for="view in viewTypes"
          :key="view.key"
          :class="['control-tab', { active: selectedViewType === view.key }]"
          @click="updateViewType(view.key)"
        >
          {{ view.icon }} {{ view.label }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ComparisonControls",
  props: {
    selectedComparisonType: {
      type: String,
      required: true,
      default: "individual",
    },
    selectedViewType: {
      type: String,
      required: true,
      default: "assessments",
    },
  },
  emits: ["update-comparison-type", "update-view-type"],
  data() {
    return {
      comparisonTypes: [
        { key: "individual", label: "Individual Assessments" },
        { key: "category", label: "By Category" },
        { key: "weighted", label: "Weighted Performance" },
      ],
      viewTypes: [
        { key: "assessments", label: "Assessment Bars", icon: "📊" },
        { key: "distribution", label: "Grade Distribution", icon: "📈" },
        { key: "percentiles", label: "Percentile Ranking", icon: "🎯" },
      ],
    };
  },
  methods: {
    updateComparisonType(type) {
      this.$emit("update-comparison-type", type);
    },

    updateViewType(type) {
      this.$emit("update-view-type", type);
    },
  },
};
</script>

<style scoped>
.comparison-controls {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 40px;
  flex-wrap: wrap;
}

.control-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.control-group label {
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9rem;
}

.control-tabs {
  display: flex;
  gap: 8px;
}

.control-tab {
  padding: 8px 16px;
  border: 2px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 20px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
}

.control-tab.active,
.control-tab:hover {
  border-color: #3b82f6;
  color: #3b82f6;
  background: #eff6ff;
}

@media (max-width: 768px) {
  .comparison-controls {
    flex-direction: column;
    gap: 20px;
  }

  .control-tabs {
    flex-wrap: wrap;
  }
}
</style>
