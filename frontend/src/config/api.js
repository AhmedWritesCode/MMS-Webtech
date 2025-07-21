export const API_CONFIG = {
  baseURL: process.env.VUE_APP_API_URL || "http://localhost:8080/api",
  timeout: 10000,
  retryAttempts: 3,
  retryDelay: 1000,
};

export const AUTH_CONFIG = {
  tokenKey: "auth_token",
  refreshTokenKey: "refresh_token",
  userKey: "user_data",
};

export const ENDPOINTS = {
  // Student Performance Module
  STUDENT_PERFORMANCE: {
    COURSE_MARKS: (studentId, courseId) =>
      `/student/${studentId}/course/${courseId}/marks`,
    FULL_BREAKDOWN: (studentId) => `/student/${studentId}/marks/breakdown`,
    COURSE_COMPARISON: (courseId) => `/course/${courseId}/marks/comparison`,
    STUDENT_RANK: (studentId, courseId) =>
      `/student/${studentId}/course/${courseId}/rank`,
    PERFORMANCE_TRENDS: (studentId) =>
      `/student/${studentId}/performance/trends`,
    WHAT_IF_SIMULATION: (studentId) =>
      `/student/${studentId}/what-if-simulation`,
    CLASS_AVERAGES: (courseId) => `/course/${courseId}/averages`,
  },

  // Advisor Module
  ADVISOR: {
    ADVISEES: (advisorId) => `/advisor/${advisorId}/advisees`,
    ADVISEE_BREAKDOWN: (advisorId, studentId) =>
      `/advisor/${advisorId}/advisee/${studentId}/breakdown`,
    AT_RISK_STUDENTS: (advisorId) => `/advisor/${advisorId}/at-risk-students`,
    ADD_NOTE: (advisorId, studentId) =>
      `/advisor/${advisorId}/advisee/${studentId}/notes`,
    GET_NOTES: (advisorId, studentId) =>
      `/advisor/${advisorId}/advisee/${studentId}/notes`,
    EXPORT_REPORT: (advisorId, studentId) =>
      `/advisor/${advisorId}/advisee/${studentId}/report`,
  },

  // Utility
  USER_PROFILE: "/user/profile",
  HEALTH_CHECK: "/health",
};
