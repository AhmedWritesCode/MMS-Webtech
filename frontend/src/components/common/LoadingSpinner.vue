<template>
  <div class="loading-spinner" :class="'loading-spinner--' + size">
    <div class="spinner" :style="{ borderTopColor: color }"></div>
    <span v-if="text" class="loading-text">{{ text }}</span>
  </div>
</template>

<script>
export default {
  name: "LoadingSpinner",
  props: {
    size: {
      type: String,
      default: "medium",
      validator: (value) => ["small", "medium", "large"].includes(value),
    },
    color: {
      type: String,
      default: "#4f46e5",
    },
    text: {
      type: String,
      default: "Loading...",
    },
  },
};
</script>

<style scoped>
.loading-spinner {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.spinner {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #4f46e5;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.loading-spinner--small .spinner {
  width: 20px;
  height: 20px;
  border-width: 2px;
}

.loading-spinner--medium .spinner {
  width: 40px;
  height: 40px;
}

.loading-spinner--large .spinner {
  width: 60px;
  height: 60px;
  border-width: 4px;
}

.loading-text {
  font-size: 14px;
  color: #666;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
