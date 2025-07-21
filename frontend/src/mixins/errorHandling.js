export const errorHandlingMixin = {
  data() {
    return {
      errors: {},
      globalError: null,
    };
  },
  methods: {
    handleError(error, context = "Operation") {
      console.error(`${context} error:`, error);

      let errorMessage = "An unexpected error occurred";

      if (error.response) {
        // Server responded with error status
        const status = error.response.status;
        const data = error.response.data;

        switch (status) {
          case 400:
            errorMessage = data.message || "Invalid request";
            break;
          case 401:
            errorMessage = "Authentication required";
            this.$auth.logout();
            return;
          case 403:
            errorMessage = "Access denied";
            break;
          case 404:
            errorMessage = "Resource not found";
            break;
          case 422:
            errorMessage = data.message || "Validation error";
            if (data.errors) {
              this.errors = data.errors;
            }
            break;
          case 500:
            errorMessage = "Server error. Please try again later.";
            break;
          default:
            errorMessage = data.message || `Server error (${status})`;
        }
      } else if (error.request) {
        // Network error
        errorMessage = "Network error. Please check your connection.";
      } else {
        // Other error
        errorMessage = error.message || errorMessage;
      }

      this.showError(errorMessage);
    },

    showError(message, duration = 5000) {
      this.globalError = message;

      if (duration > 0) {
        setTimeout(() => {
          this.globalError = null;
        }, duration);
      }
    },

    clearErrors() {
      this.errors = {};
      this.globalError = null;
    },

    hasError(field) {
      return !!this.errors[field];
    },

    getError(field) {
      return this.errors[field] ? this.errors[field][0] : null;
    },
  },
};
