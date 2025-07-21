// src/services/api.js
import axios from "axios";
import authService from "./auth";

// API Configuration
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

// Create axios instance with default configuration
const api = axios.create({
  baseURL: API_CONFIG.baseURL,
  headers: {
    "Content-Type": "application/json",
  },
  timeout: API_CONFIG.timeout,
});

// Request interceptor to add authentication token
api.interceptors.request.use(
  (config) => {
    // Add auth token if available
    const token = authService.getToken();
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    console.log('API Request:', {
      method: config.method,
      url: config.url,
      baseURL: config.baseURL,
      params: config.params,
      data: config.data
    });

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor for error handling and token refresh
api.interceptors.response.use(
  (response) => {
    console.log('API Response:', {
      status: response.status,
      data: response.data,
      headers: response.headers
    });
    return response;
  },
  async (error) => {
    console.error('API Error:', {
      message: error.message,
      response: error.response?.data,
      status: error.response?.status,
      config: error.config
    });

    const originalRequest = error.config;

    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      // Try to refresh token
      const refreshSuccess = await authService.refreshToken();
      if (refreshSuccess) {
        // Retry original request with new token
        return api(originalRequest);
      } else {
        // Refresh failed, redirect to login
        authService.logout();
        window.location.href = "/login";
      }
    }

    return Promise.reject(error);
  }
);

// API response wrapper
const handleApiResponse = (response) => {
  if (response.data.success) {
    return {
      success: true,
      data: response.data.data,
      message: response.data.message,
    };
  } else {
    return {
      success: false,
      message: response.data.message || "Operation failed",
      errors: response.data.errors || {},
    };
  }
};

// API error handler
const handleApiError = (error) => {
  console.error("API Error:", error);

  if (error.response) {
    // Server responded with error status
    return {
      success: false,
      message: error.response.data?.message || "Server error occurred",
      errors: error.response.data?.errors || {},
      status: error.response.status,
    };
  } else if (error.request) {
    // Request was made but no response received
    return {
      success: false,
      message: "Network error. Please check your connection.",
    };
  } else {
    // Something else happened
    return {
      success: false,
      message: "An unexpected error occurred",
    };
  }
};

// Authentication API Services
export const authAPI = {
  // Login
  login: async (credentials) => {
    try {
      const response = await api.post("/auth/login", credentials);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Logout
  logout: async () => {
    try {
      const response = await api.post("/auth/logout");
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Refresh token
  refreshToken: async () => {
    try {
      const response = await api.post("/auth/refresh");
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Forgot password
  forgotPassword: async (data) => {
    try {
      const response = await api.post("/auth/forgot-password", data);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Reset password
  resetPassword: async (data) => {
    try {
      const response = await api.post("/auth/reset-password", data);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },
};

// Student Performance API Services
export const studentPerformanceAPI = {
  // Get student's component-wise marks for a specific course
  getStudentCourseMarks: async (studentId, courseId) => {
    try {
      const response = await api.get(
        `/student/${studentId}/course/${courseId}/marks`
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get student's full mark breakdown across all courses
  getStudentFullBreakdown: async (studentId) => {
    try {
      const response = await api.get(`/student/${studentId}/marks/breakdown`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get anonymous comparison with coursemates
  getCourseComparison: async (courseId) => {
    try {
      const response = await api.get(`/course/${courseId}/marks/comparison`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get student's class rank and percentile
  getStudentRank: async (studentId, courseId) => {
    try {
      const response = await api.get(
        `/student/${studentId}/course/${courseId}/rank`
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get performance trends over time
  getPerformanceTrends: async (studentId, courseId = null) => {
    try {
      const params = {};
      if (courseId && courseId !== "all") {
        params.course_id = courseId;
      }

      const response = await api.get(
        `/student/${studentId}/performance/trends`,
        { params }
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // What-if grade simulation
  whatIfSimulation: async (studentId, simulationData) => {
    try {
      const response = await api.post(
        `/student/${studentId}/what-if-simulation`,
        simulationData
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get class averages per component
  getClassAverages: async (courseId) => {
    try {
      const response = await api.get(`/course/${courseId}/averages`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Submit remark request
  submitRemarkRequest: async (studentId, courseId, remarkData) => {
    try {
      const response = await api.post(
        `/remark-requests/submit`,
        {
          student_id: studentId,
          assessment_component_id: remarkData.component_id,
          justification: remarkData.reason,
          original_marks: Number.isFinite(remarkData.original_marks) ? remarkData.original_marks : 0,
        }
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get remark requests for student
  getRemarkRequests: async (studentId) => {
    try {
      const response = await api.get(`/remark-requests/student/${studentId}`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },
};

// Advisor Module API Services
export const advisorAPI = {
  // Get list of advisees under supervision
  getAdvisees: async (advisorId) => {
    try {
      const response = await api.get(`/advisor/${advisorId}/advisees`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get advisee's full mark breakdown across all courses
  getAdviseeBreakdown: async (advisorId, studentId) => {
    try {
      const response = await api.get(
        `/advisor/${advisorId}/advisee/${studentId}/breakdown`
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get at-risk students
  getAtRiskStudents: async (advisorId) => {
    try {
      const response = await api.get(`/advisor/${advisorId}/at-risk-students`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get notes for advisee
  getAdviseeNotes: async (advisorId, studentId) => {
    try {
      const response = await api.get(
        `/advisor/${advisorId}/advisee/${studentId}/notes`
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Add note for advisee
  addAdviseNote: async (advisorId, studentId, noteData) => {
    try {
      const response = await api.post(
        `/advisor/${advisorId}/advisee/${studentId}/notes`,
        noteData
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Delete note for advisee
  deleteAdviseNote: async (advisorId, studentId, noteId) => {
    try {
      const response = await api.delete(
        `/advisor/${advisorId}/advisee/${studentId}/notes/${noteId}`
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Export consultation report
  exportConsultationReport: async (advisorId, studentId, params = {}) => {
    try {
      const queryParams = new URLSearchParams();

      // Add all parameters to query string
      Object.keys(params).forEach((key) => {
        if (params[key] !== undefined && params[key] !== null) {
          queryParams.append(key, params[key]);
        }
      });

      const response = await api.get(
        `/advisor/${advisorId}/advisee/${studentId}/report?${queryParams.toString()}`,
        {
          responseType: params.format === "json" ? "json" : "blob",
        }
      );

      if (params.format === "json") {
        return handleApiResponse(response);
      } else {
        // For PDF/CSV downloads
        const filename =
          response.headers["content-disposition"]?.split("filename=")[1] ||
          `consultation_report_${studentId}.${params.format || "pdf"}`;

        return {
          success: true,
          data: response.data,
          filename: filename.replace(/"/g, ""),
        };
      }
    } catch (error) {
      return handleApiError(error);
    }
  },
};

// Lecturer API Services
export const lecturerAPI = {
  // Get lecturer's dashboard overview
  getDashboard: async (lecturerId) => {
    try {
      const response = await api.get(`/lecturer/${lecturerId}/dashboard`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get lecturer's courses
  getCourses: async (lecturerId) => {
    try {
      const response = await api.get(`/lecturer/${lecturerId}/courses`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get students in a course
  getCourseStudents: async (courseId) => {
    try {
      const response = await api.get(`/course/${courseId}/students`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Add/update student marks
  updateStudentMarks: async (courseId, studentId, marksData) => {
    try {
      const response = await api.put(
        `/course/${courseId}/student/${studentId}/marks`,
        marksData
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get course assessment components
  getCourseComponents: async (lecturerId, courseId) => {
    try {
      const response = await api.get(`/lecturer/${lecturerId}/course/${courseId}/components`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Create/update assessment component
  updateCourseComponent: async (lecturerId, courseId, componentData) => {
    try {
      const response = await api.post(
        `/lecturer/${lecturerId}/course/${courseId}/components`,
        componentData
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Bulk upload marks via CSV
  bulkUploadMarks: async (courseId, csvFile) => {
    try {
      const formData = new FormData();
      formData.append("csv_file", csvFile);

      const response = await api.post(
        `/course/${courseId}/marks/bulk-upload`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Export marks as CSV
  exportMarks: async (lecturerId, courseId) => {
    try {
      const response = await api.get(`/lecturer/${lecturerId}/course/${courseId}/marks/export`, {
        responseType: "blob",
      });
      return {
        success: true,
        data: response.data,
        filename:
          response.headers["content-disposition"]?.split("filename=")[1] ||
          "marks.csv",
      };
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get course analytics
  getCourseAnalytics: async (courseId) => {
    try {
      const response = await api.get(`/course/${courseId}/analytics`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Send notification to students
  sendNotification: async (courseId, notificationData) => {
    try {
      const response = await api.post(
        `/course/${courseId}/notifications`,
        notificationData
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },
};

// Admin API Services
export const adminAPI = {
  // Get all users
  getUsers: async (filters = {}) => {
    try {
      const response = await api.get("/admin/users", { params: filters });
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Create user
  createUser: async (userData) => {
    try {
      const response = await api.post("/admin/users", userData);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Update user
  updateUser: async (userId, userData) => {
    try {
      const response = await api.put(`/admin/users/${userId}`, userData);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Delete user
  deleteUser: async (userId) => {
    try {
      const response = await api.delete(`/admin/users/${userId}`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Reset user password
  resetUserPassword: async (userId) => {
    try {
      const response = await api.post(`/admin/users/${userId}/reset-password`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get system logs
  getSystemLogs: async (filters = {}) => {
    try {
      const response = await api.get("/admin/logs", { params: filters });
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Assign lecturer to course
  assignLecturerToCourse: async (courseId, lecturerId) => {
    try {
      const response = await api.post(
        `/admin/courses/${courseId}/assign-lecturer`,
        {
          lecturer_id: lecturerId,
        }
      );
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },
};

// General utility API services
export const utilityAPI = {
  // Health check
  healthCheck: async () => {
    try {
      const response = await api.get("/health");
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get user profile
  getUserProfile: async () => {
    try {
      const response = await api.get("/user/profile");
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Update user profile
  updateUserProfile: async (profileData) => {
    try {
      const response = await api.put("/user/profile", profileData);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Upload file
  uploadFile: async (file, folder = "general") => {
    try {
      const formData = new FormData();
      formData.append("file", file);
      formData.append("folder", folder);

      const response = await api.post("/upload", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },
};

// Course Management API Services
export const courseAPI = {
  // Get all courses
  getAllCourses: async () => {
    try {
      const response = await api.get("/courses");
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Get single course
  getCourse: async (courseId) => {
    try {
      const response = await api.get(`/courses/${courseId}`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Create course
  createCourse: async (courseData) => {
    try {
      const response = await api.post("/courses", courseData);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Update course
  updateCourse: async (courseId, courseData) => {
    try {
      const response = await api.put(`/courses/${courseId}`, courseData);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  },

  // Delete course
  deleteCourse: async (courseId) => {
    try {
      const response = await api.delete(`/courses/${courseId}`);
      return handleApiResponse(response);
    } catch (error) {
      return handleApiError(error);
    }
  }
};

export default api;
