<template>
  <button class="action-card" @click="$emit('action-clicked', action)">
    <div class="action-icon">{{ action.icon }}</div>
    <div class="action-content">
      <h3>{{ action.title }}</h3>
      <p>{{ action.description }}</p>
    </div>
  </button>
</template>

<script>
export default {
  name: "ActionCard",
  props: {
    action: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  emits: ["action-clicked"],
};
</script>

<style scoped>
.action-card {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
  width: 100%;
}

.action-card:hover {
  border-color: #3b82f6;
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15);
}

.action-icon {
  font-size: 2rem;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f1f5f9;
  border-radius: 10px;
  flex-shrink: 0;
}

.action-content h3 {
  margin: 0 0 4px 0;
  font-weight: 600;
  color: #1e293b;
}

.action-content p {
  margin: 0;
  color: #64748b;
  font-size: 0.9rem;
}
</style>
