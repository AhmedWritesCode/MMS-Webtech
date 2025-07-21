export const loadingStateMixin = {
  data() {
    return {
      loading: false,
      loadingStates: {},
    };
  },
  methods: {
    setLoading(isLoading, key = "default") {
      if (key === "default") {
        this.loading = isLoading;
      } else {
        this.$set(this.loadingStates, key, isLoading);
      }
    },

    isLoading(key = "default") {
      if (key === "default") {
        return this.loading;
      }
      return !!this.loadingStates[key];
    },

    async withLoading(asyncFunction, loadingKey = "default") {
      this.setLoading(true, loadingKey);
      try {
        const result = await asyncFunction();
        return result;
      } finally {
        this.setLoading(false, loadingKey);
      }
    },
  },
};
