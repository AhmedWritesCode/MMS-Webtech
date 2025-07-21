import { errorHandlingMixin } from "./errorHandling";
import { loadingStateMixin } from "./loadingState";

export const apiMixin = {
  mixins: [errorHandlingMixin, loadingStateMixin],

  methods: {
    async apiCall(
      apiFunction,
      loadingKey = "default",
      errorContext = "API call"
    ) {
      return this.withLoading(async () => {
        try {
          const response = await apiFunction();

          if (response.data.success) {
            return response.data.data;
          } else {
            throw new Error(response.data.message || "API call failed");
          }
        } catch (error) {
          this.handleError(error, errorContext);
          throw error;
        }
      }, loadingKey);
    },

    async safeApiCall(
      apiFunction,
      fallbackValue = null,
      loadingKey = "default",
      errorContext = "API call"
    ) {
      try {
        return await this.apiCall(apiFunction, loadingKey, errorContext);
      } catch (error) {
        return fallbackValue;
      }
    },
  },
};
