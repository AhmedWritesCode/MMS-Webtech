// src/services/auth.js
// Simple auth service without external dependencies

// Auth configuration
const AUTH_CONFIG = {
  tokenKey: "auth_token",
  userKey: "user_data",
  refreshTokenKey: "refresh_token",
  sessionKey: "session_auth", // New key for session-based auth
};

// Simple HTTP client
class SimpleHTTP {
  constructor(baseURL) {
    this.baseURL = baseURL;
  }

  async request(method, url, data = null, headers = {}) {
    const config = {
      method: method.toUpperCase(),
      headers: {
        "Content-Type": "application/json",
        ...headers,
      },
    };

    if (
      data &&
      (method.toUpperCase() === "POST" || method.toUpperCase() === "PUT")
    ) {
      config.body = JSON.stringify(data);
    }

    try {
      const response = await fetch(`${this.baseURL}${url}`, config);
      const responseData = await response.json();

      return {
        status: response.status,
        data: responseData,
        ok: response.ok,
      };
    } catch (error) {
      throw new Error(`Network error: ${error.message}`);
    }
  }

  async get(url, headers = {}) {
    return this.request("GET", url, null, headers);
  }

  async post(url, data, headers = {}) {
    return this.request("POST", url, data, headers);
  }

  async put(url, data, headers = {}) {
    return this.request("PUT", url, data, headers);
  }

  async delete(url, headers = {}) {
    return this.request("DELETE", url, null, headers);
  }
}

class AuthService {
  constructor() {
    // Get API URL from environment or use default
    const apiUrl = process.env.VUE_APP_API_URL || "http://localhost:8080/api";
    this.http = new SimpleHTTP(apiUrl);

    // Check if this is a session-based authentication
    this.isSessionAuth =
      sessionStorage.getItem(AUTH_CONFIG.sessionKey) === "true";

    // Get token and user from appropriate storage
    this.token = this.isSessionAuth
      ? sessionStorage.getItem(AUTH_CONFIG.tokenKey)
      : localStorage.getItem(AUTH_CONFIG.tokenKey);
    this.user = this.getUserFromStorage();
  }

  // Get user data from appropriate storage
  getUserFromStorage() {
    try {
      const storage = this.isSessionAuth ? sessionStorage : localStorage;
      const userData = storage.getItem(AUTH_CONFIG.userKey);
      return userData ? JSON.parse(userData) : null;
    } catch (error) {
      console.error("Error parsing user data from storage:", error);
      this.clearStorageItem(AUTH_CONFIG.userKey);
      return null;
    }
  }

  // Save user data to appropriate storage
  saveUserToStorage(userData) {
    try {
      const storage = this.isSessionAuth ? sessionStorage : localStorage;
      storage.setItem(AUTH_CONFIG.userKey, JSON.stringify(userData));
      this.user = userData;
    } catch (error) {
      console.error("Error saving user data to storage:", error);
    }
  }

  // Save token to appropriate storage
  saveToken(token) {
    try {
      const storage = this.isSessionAuth ? sessionStorage : localStorage;
      storage.setItem(AUTH_CONFIG.tokenKey, token);
      this.token = token;
    } catch (error) {
      console.error("Error saving token to storage:", error);
    }
  }

  // Clear specific item from appropriate storage
  clearStorageItem(key) {
    const storage = this.isSessionAuth ? sessionStorage : localStorage;
    storage.removeItem(key);
  }

  // Get authorization headers
  getAuthHeaders() {
    const headers = {};
    if (this.token) {
      headers.Authorization = `Bearer ${this.token}`;
    }
    return headers;
  }

  // Login method - matches AuthController.php login endpoint
  async login(credentials) {
    try {
      // Determine if this should be a session-based login
      const isSessionAuth = !credentials.rememberMe;

      // Store the session preference
      if (isSessionAuth) {
        sessionStorage.setItem(AUTH_CONFIG.sessionKey, "true");
        this.isSessionAuth = true;
      } else {
        localStorage.setItem(AUTH_CONFIG.sessionKey, "false");
        this.isSessionAuth = false;
      }

      const response = await this.http.post("/auth/login", {
        username: credentials.username,
        password: credentials.password,
        role: credentials.role,
        remember_me: credentials.rememberMe || false,
      });

      // Handle successful response
      if (response.ok && response.data.success) {
        const { token, user, expires_in } = response.data.data;

        // Save authentication data to appropriate storage
        this.saveToken(token);
        this.saveUserToStorage(user);

        // Store expiry time for token management
        const expiryTime = Date.now() + expires_in * 1000;
        const storage = this.isSessionAuth ? sessionStorage : localStorage;
        storage.setItem("token_expiry", expiryTime.toString());

        return {
          success: true,
          user: user,
          message: response.data.message || "Login successful",
        };
      } else {
        return {
          success: false,
          message: response.data.message || "Login failed",
        };
      }
    } catch (error) {
      console.error("Login error:", error);

      // Handle different error scenarios based on response status
      if (error.message.includes("Network error")) {
        return {
          success: false,
          message: "Network error. Please check your connection.",
        };
      }

      return {
        success: false,
        message: "An unexpected error occurred during login.",
      };
    }
  }

  // Logout method
  async logout() {
    try {
      console.log("AuthService logout - starting...");
      console.log("AuthService logout - current token:", this.token);
      console.log("AuthService logout - current user:", this.user);

      if (this.token) {
        console.log("AuthService logout - calling backend logout endpoint...");
        await this.http.post("/auth/logout", null, this.getAuthHeaders());
        console.log("AuthService logout - backend call completed");
      }
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      // Clear local storage regardless of API call result
      console.log("AuthService logout - clearing auth data...");
      this.clearAuthData();
      console.log("AuthService logout - auth data cleared");
      console.log("AuthService logout - token after clear:", this.token);
      console.log("AuthService logout - user after clear:", this.user);
    }
  }

  // Clear authentication data from storage
  clearAuthData() {
    console.log("clearAuthData - starting...");

    // Clear from both localStorage and sessionStorage to be safe
    const itemsToClear = [
      AUTH_CONFIG.tokenKey,
      AUTH_CONFIG.userKey,
      AUTH_CONFIG.refreshTokenKey,
      AUTH_CONFIG.sessionKey,
      "token_expiry",
    ];

    itemsToClear.forEach((item) => {
      localStorage.removeItem(item);
      sessionStorage.removeItem(item);
    });

    console.log("clearAuthData - storage cleared");

    this.token = null;
    this.user = null;
    this.isSessionAuth = false;

    console.log("clearAuthData - instance variables after clear:", {
      token: this.token,
      user: this.user,
      isSessionAuth: this.isSessionAuth,
    });
  }

  // Refresh token method
  async refreshToken() {
    try {
      if (!this.token) {
        return false;
      }

      const response = await this.http.post(
        "/auth/refresh",
        null,
        this.getAuthHeaders()
      );

      if (response.ok && response.data.success) {
        const { token, expires_in } = response.data.data;
        this.saveToken(token);

        // Update expiry time
        const expiryTime = Date.now() + expires_in * 1000;
        const storage = this.isSessionAuth ? sessionStorage : localStorage;
        storage.setItem("token_expiry", expiryTime.toString());

        return true;
      }

      return false;
    } catch (error) {
      console.error("Token refresh error:", error);
      return false;
    }
  }

  // Check if user is authenticated
  isAuthenticated() {
    if (!this.token || !this.user) {
      return false;
    }

    // Check if token is expired
    const storage = this.isSessionAuth ? sessionStorage : localStorage;
    const expiryTime = storage.getItem("token_expiry");
    if (expiryTime && Date.now() > parseInt(expiryTime)) {
      this.clearAuthData();
      return false;
    }

    return true;
  }

  // Get current user data
  getCurrentUser() {
    return this.user;
  }

  // Get current user role
  getCurrentUserRole() {
    return this.user?.role || null;
  }

  // Get current user ID
  getCurrentUserId() {
    return this.user?.id || null;
  }

  // Check if user has specific role
  hasRole(role) {
    return this.user?.role === role;
  }

  // Check if user has any of the specified roles
  hasAnyRole(roles) {
    if (!this.user?.role) return false;
    return roles.includes(this.user.role);
  }

  // Get current token
  getToken() {
    return this.token;
  }

  // Check if token will expire soon (within 5 minutes)
  isTokenExpiringSoon() {
    const storage = this.isSessionAuth ? sessionStorage : localStorage;
    const expiryTime = storage.getItem("token_expiry");
    if (!expiryTime) return false;

    const timeUntilExpiry = parseInt(expiryTime) - Date.now();
    return timeUntilExpiry < 5 * 60 * 1000; // 5 minutes in milliseconds
  }

  // Auto-refresh token if it's expiring soon
  async autoRefreshToken() {
    if (this.isTokenExpiringSoon() && this.isAuthenticated()) {
      return await this.refreshToken();
    }
    return true;
  }

  // Initialize auto-refresh interval
  startAutoRefresh() {
    // Check every minute if token needs refreshing
    setInterval(() => {
      this.autoRefreshToken();
    }, 60000);
  }

  // Forgot password request
  async forgotPassword(email, role) {
    try {
      const response = await this.http.post("/auth/forgot-password", {
        email,
        role,
      });

      return {
        success: response.data.success,
        message: response.data.message,
      };
    } catch (error) {
      console.error("Forgot password error:", error);
      return {
        success: false,
        message: "Failed to send reset email",
      };
    }
  }

  // Reset password
  async resetPassword(token, newPassword, confirmPassword) {
    try {
      const response = await this.http.post("/auth/reset-password", {
        token,
        new_password: newPassword,
        confirm_password: confirmPassword,
      });

      return {
        success: response.data.success,
        message: response.data.message,
      };
    } catch (error) {
      console.error("Reset password error:", error);
      return {
        success: false,
        message: "Failed to reset password",
      };
    }
  }

  // Get user permissions based on role
  getUserPermissions() {
    const rolePermissions = {
      student: [
        "view_own_marks",
        "compare_marks",
        "view_rank",
        "submit_remark_request",
        "simulate_grades",
      ],
      lecturer: [
        "manage_students",
        "add_marks",
        "edit_marks",
        "view_analytics",
        "export_marks",
        "send_notifications",
      ],
      advisor: [
        "view_advisees",
        "view_advisee_marks",
        "add_notes",
        "export_reports",
        "identify_at_risk_students",
      ],
      admin: [
        "manage_users",
        "assign_lecturers",
        "view_system_logs",
        "reset_passwords",
        "manage_courses",
      ],
    };

    return rolePermissions[this.user?.role] || [];
  }

  // Check if user has specific permission
  hasPermission(permission) {
    const userPermissions = this.getUserPermissions();
    return userPermissions.includes(permission);
  }

  // Check if current authentication is session-based
  isSessionBasedAuth() {
    return this.isSessionAuth;
  }

  // Get authentication status for debugging
  getAuthStatus() {
    const storage = this.isSessionAuth ? sessionStorage : localStorage;
    const expiryTime = storage.getItem("token_expiry");
    const isExpired = expiryTime && Date.now() > parseInt(expiryTime);

    return {
      isAuthenticated: this.isAuthenticated(),
      isSessionAuth: this.isSessionAuth,
      hasToken: !!this.token,
      hasUser: !!this.user,
      tokenExpiry: expiryTime
        ? new Date(parseInt(expiryTime)).toISOString()
        : null,
      isExpired: isExpired,
      timeUntilExpiry: expiryTime ? parseInt(expiryTime) - Date.now() : null,
    };
  }
}

// Create and export a singleton instance
const authService = new AuthService();

// Start auto-refresh when service is created
if (authService.isAuthenticated()) {
  authService.startAutoRefresh();
}

export default authService;
