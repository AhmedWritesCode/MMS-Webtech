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
  background: #101010;
  border-radius: 18px;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 4px 24px rgba(0, 128, 0, 0.15);
  display: flex;
  gap: 48px;
  flex-wrap: wrap;
  border: 2px solid #22c55e;
}

.control-group {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.control-group label {
  font-weight: 700;
  color: #22c55e;
  font-size: 1.1rem;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.control-tabs {
  display: flex;
  gap: 14px;
}

.control-tab {
  padding: 8px 16px;
  border: 2px solid #7C9885;
  background: #f8fafc;
  border-radius: 20px;
  font-weight: 600;
  color: #7C9885;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 1rem;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(34, 197, 94, 0.08);
  outline: none;
  position: relative;
  overflow: hidden;
}

.control-tab.active,
.control-tab:hover {
  border-color: #B5B682;
  color: #fff;
  background: #7C9885;
}

@media (max-width: 768px) {
  .comparison-controls {
    flex-direction: column;
    gap: 24px;
    padding: 18px;
  }

  .control-tabs {
    flex-wrap: wrap;
    gap: 10px;
  }
}
</style>
