import api, {
  studentPerformanceAPI,
  advisorAPI,
  utilityAPI,
} from "@/services/api";

const HttpPlugin = {
  install(app) {
    // Make API services available globally in components
    app.config.globalProperties.$http = api;
    app.config.globalProperties.$studentAPI = studentPerformanceAPI;
    app.config.globalProperties.$advisorAPI = advisorAPI;
    app.config.globalProperties.$utilityAPI = utilityAPI;

    // Provide APIs for composition API
    app.provide("$http", api);
    app.provide("$studentAPI", studentPerformanceAPI);
    app.provide("$advisorAPI", advisorAPI);
    app.provide("$utilityAPI", utilityAPI);
  },
};

export default HttpPlugin;
