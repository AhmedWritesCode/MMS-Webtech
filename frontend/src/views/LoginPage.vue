<template>
  <div class="login-container">
    <!-- Animated Background -->
    <div class="animated-background">
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
      <div class="floating-shape shape-3"></div>
    </div>

    <!-- Login Card -->
    <div class="login-wrapper">
      <!-- Logo/Title Section -->
      <div class="title-section">
        <div class="logo-container">
          <GraduationCap size="48" class="logo-icon" />
        </div>
        <h1>Marks Management System</h1>
        <p class="subtitle">Manage and monitor academic performance</p>
      </div>

      <!-- Role Selection Buttons -->
      <div class="role-selection" v-if="!selectedRole">
        <h3>Select Your Role</h3>
        <div class="role-buttons">
          <button
            v-for="role in roles"
            :key="role.value"
            @click="selectRole(role.value)"
            class="role-btn"
            :class="`role-${role.value}`"
          >
            <component :is="role.icon" size="24" />
            <span>{{ role.label }}</span>
          </button>
        </div>
      </div>

      <!-- Login Form -->
      <form
        v-if="selectedRole"
        @submit.prevent="handleLogin"
        class="login-form"
      >
        <!-- Back to Role Selection -->
        <button type="button" @click="backToRoleSelection" class="back-btn">
          <ArrowLeft size="20" />
          <span>Back to role selection</span>
        </button>

        <!-- Selected Role Display -->
        <div class="selected-role-display">
          <component :is="getCurrentRoleIcon()" size="24" />
          <span>{{ getCurrentRoleLabel() }} Login</span>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="error-message">
          <AlertCircle size="20" />
          <span>{{ error }}</span>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="success-message">
          <CheckCircle size="20" />
          <span>{{ successMessage }}</span>
        </div>

        <!-- Username/Matric Number Field -->
        <div class="form-group">
          <label :for="usernameFieldId">
            {{ getUsernameLabel() }}
          </label>
          <div class="input-container">
            <User class="input-icon" size="20" />
            <input
              :id="usernameFieldId"
              v-model="credentials.username"
              :placeholder="getUsernamePlaceholder()"
              type="text"
              required
              :disabled="loading"
              @input="clearErrors"
            />
          </div>
          <span v-if="errors.username" class="field-error">{{
            errors.username
          }}</span>
        </div>

        <!-- Password/PIN Field -->
        <div class="form-group">
          <label for="password">
            {{ getPasswordLabel() }}
          </label>
          <div class="input-container">
            <Lock class="input-icon" size="20" />
            <input
              id="password"
              v-model="credentials.password"
              :placeholder="getPasswordPlaceholder()"
              :type="showPassword ? 'text' : 'password'"
              required
              :disabled="loading"
              @input="clearErrors"
            />
            <button
              type="button"
              class="toggle-password"
              @click="togglePassword"
              :disabled="loading"
            >
              <Eye v-if="showPassword" size="20" />
              <EyeOff v-else size="20" />
            </button>
          </div>
          <span v-if="errors.password" class="field-error">{{
            errors.password
          }}</span>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="form-options">
          <label class="checkbox-container">
            <input type="checkbox" v-model="rememberMe" :disabled="loading" />
            <span class="checkmark"></span>
            <div class="remember-me-text">
              <span>Remember me</span>
              <small class="remember-me-hint">
                {{
                  rememberMe
                    ? "Stay logged in for 30 days"
                    : "Log out when browser closes (8 hours max)"
                }}
              </small>
            </div>
          </label>
          <a href="#" class="forgot-link" @click.prevent="handleForgotPassword">
            Forgot {{ getPasswordLabel().toLowerCase() }}?
          </a>
        </div>

        <!-- Login Button -->
        <button
          type="submit"
          class="login-btn"
          :disabled="loading || !isFormValid"
        >
          <span v-if="loading" class="loading-spinner"></span>
          <LogIn v-else size="20" />
          <span>{{ loading ? "Logging in..." : "Login" }}</span>
        </button>
      </form>

      <!-- Development Quick Access (Remove in Production) -->
      <div v-if="isDevelopment && selectedRole" class="dev-section">
        <h4>Quick Test Login</h4>
        <div class="dev-buttons">
          <button
            v-for="user in getTestUsers()"
            :key="user.username"
            @click="quickLogin(user)"
            class="dev-btn"
            :disabled="loading"
          >
            {{ user.label }}
          </button>
        </div>

       
      </div>

      <!-- Footer -->
      <div class="login-footer">
        <p>&copy; 2025 Marks Management System</p>
      </div>
    </div>
  </div>
</template>

<script>
// Import icons from lucide-vue-next
import {
  GraduationCap,
  User,
  Lock,
  Eye,
  EyeOff,
  LogIn,
  ArrowLeft,
  AlertCircle,
  CheckCircle,
  UserCheck,
  Users,
  BookOpen,
  Shield,
} from "lucide-vue-next";

// Import authentication service
import authService from "@/services/auth";

export default {
  name: "LoginPage",
  components: {
    GraduationCap,
    User,
    Lock,
    Eye,
    EyeOff,
    LogIn,
    ArrowLeft,
    AlertCircle,
    CheckCircle,
    UserCheck,
    Users,
    BookOpen,
    Shield,
  },
  data() {
    return {
      selectedRole: null,
      credentials: {
        username: "",
        password: "",
      },
      rememberMe: false,
      showPassword: false,
      loading: false,
      error: null,
      successMessage: null,
      errors: {},

      roles: [
        {
          value: "student",
          label: "Student",
          icon: "GraduationCap",
        },
        {
          value: "lecturer",
          label: "Lecturer",
          icon: "BookOpen",
        },
        {
          value: "advisor",
          label: "Academic Advisor",
          icon: "Users",
        },
        {
          value: "admin",
          label: "Administrator",
          icon: "Shield",
        },
      ],

      // Test users for development
      testUsers: {
        student: [
          { username: "A12345", password: "123456", label: "Test Student 1" },
          { username: "B23456", password: "234567", label: "Test Student 2" },
        ],
        lecturer: [
          { username: "L001", password: "lecturer123", label: "Dr. Smith" },
          { username: "L002", password: "lecturer456", label: "Prof. Johnson" },
        ],
        advisor: [
          { username: "ADV001", password: "advisor123", label: "Dr. Williams" },
          { username: "ADV002", password: "advisor456", label: "Dr. Brown" },
        ],
        admin: [
          { username: "admin", password: "admin123", label: "System Admin" },
        ],
      },
    };
  },

  computed: {
    isFormValid() {
      return (
        this.credentials.username.trim() !== "" &&
        this.credentials.password.trim() !== ""
      );
    },

    isDevelopment() {
      return process.env.NODE_ENV === "development";
    },

    usernameFieldId() {
      return `username-${this.selectedRole}`;
    },

    authStatus() {
      if (this.isDevelopment) {
        return authService.getAuthStatus();
      }
      return null;
    },
  },

  methods: {
    selectRole(role) {
      this.selectedRole = role;
      this.clearForm();
      this.clearErrors();
    },

    backToRoleSelection() {
      this.selectedRole = null;
      this.clearForm();
      this.clearErrors();
    },

    clearForm() {
      this.credentials.username = "";
      this.credentials.password = "";
      this.rememberMe = false;
    },

    clearErrors() {
      this.error = null;
      this.errors = {};
    },

    togglePassword() {
      this.showPassword = !this.showPassword;
    },

    getCurrentRoleIcon() {
      const role = this.roles.find((r) => r.value === this.selectedRole);
      return role ? role.icon : "User";
    },

    getCurrentRoleLabel() {
      const role = this.roles.find((r) => r.value === this.selectedRole);
      return role ? role.label : "";
    },

    getUsernameLabel() {
      switch (this.selectedRole) {
        case "student":
          return "Matric Number";
        case "lecturer":
          return "Lecturer ID";
        case "advisor":
          return "Advisor ID";
        case "admin":
          return "Username";
        default:
          return "Username";
      }
    },

    getUsernamePlaceholder() {
      switch (this.selectedRole) {
        case "student":
          return "Enter your matric number (e.g., A12345)";
        case "lecturer":
          return "Enter your lecturer ID (e.g., L001)";
        case "advisor":
          return "Enter your advisor ID (e.g., ADV001)";
        case "admin":
          return "Enter your username";
        default:
          return "Enter your username";
      }
    },

    getPasswordLabel() {
      return this.selectedRole === "student" ? "PIN" : "Password";
    },

    getPasswordPlaceholder() {
      return this.selectedRole === "student"
        ? "Enter your 6-digit PIN"
        : "Enter your password";
    },

    getTestUsers() {
      return this.testUsers[this.selectedRole] || [];
    },

    validateForm() {
      const errors = {};

      if (!this.credentials.username.trim()) {
        errors.username = `${this.getUsernameLabel()} is required`;
      }

      if (!this.credentials.password.trim()) {
        errors.password = `${this.getPasswordLabel()} is required`;
      }

      this.errors = errors;
      return Object.keys(errors).length === 0;
    },

    async handleLogin() {
      this.clearErrors();

      if (!this.validateForm()) {
        return;
      }

      this.loading = true;

      try {
        // Prepare login data
        const loginData = {
          username: this.credentials.username,
          password: this.credentials.password,
          role: this.selectedRole,
          rememberMe: this.rememberMe,
        };

        // Call authentication service
        const result = await authService.login(loginData);

        if (result.success) {
          this.successMessage = "Login successful! Redirecting...";

          // Redirect based on role
          setTimeout(() => {
            this.redirectToRoleDashboard(result.user.role);
          }, 1500);
        } else {
          this.error =
            result.message || "Login failed. Please check your credentials.";
        }
      } catch (error) {
        console.error("Login error:", error);
        this.error = "Network error. Please try again.";
      } finally {
        this.loading = false;
      }
    },

    redirectToRoleDashboard(role) {
      const routes = {
        student: "/student/dashboard",
        advisor: "/advisor/dashboard",
        lecturer: "/lecturer/dashboard",
        admin: "/admin/dashboard",
      };

      const route = routes[role] || "/student/dashboard";
      this.$router.push(route);
    },

    async quickLogin(testUser) {
      this.credentials.username = testUser.username;
      this.credentials.password = testUser.password;
      await this.handleLogin();
    },

    handleForgotPassword() {
      // Implement forgot password logic
      this.$router.push("/forgot-password");
    },
  },

  mounted() {
    // Check if user is already authenticated
    if (authService.isAuthenticated()) {
      const userRole = authService.getCurrentUserRole();
      this.redirectToRoleDashboard(userRole);
    }

    // Set development mode defaults
    if (this.isDevelopment) {
      // Pre-select student role for faster testing
      this.selectedRole = "student";
    }
  },
};
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0f2027 0%, #2f9e44 100%);
  padding: 20px;
  position: relative;
  overflow: hidden;
}

.animated-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 0;
}

.floating-shape {
  position: absolute;
  background: rgba(47, 158, 68, 0.08);
  border-radius: 50%;
  animation: float 7s ease-in-out infinite;
}

.shape-1 {
  width: 100px;
  height: 100px;
  top: 8%;
  left: 12%;
  animation-delay: 0s;
  background: rgba(47, 158, 68, 0.18);
}

.shape-2 {
  width: 160px;
  height: 160px;
  top: 65%;
  right: 8%;
  animation-delay: 2s;
  background: rgba(15, 32, 39, 0.13);
}

.shape-3 {
  width: 70px;
  height: 70px;
  bottom: 12%;
  left: 35%;
  animation-delay: 4s;
  background: rgba(47, 158, 68, 0.10);
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0) rotate(0deg);
  }
  50% {
    transform: translateY(-25px) rotate(180deg);
  }
}

.login-wrapper {
  background: #101820;
  border-radius: 20px;
  box-shadow: 0 10px 40px 0 rgba(47, 158, 68, 0.15),
    0 2px 8px 0 rgba(0,0,0,0.12);
  padding: 48px 36px;
  width: 100%;
  max-width: 420px;
  position: relative;
  z-index: 1;
  border: 1.5px solid #2f9e44;
}

.title-section {
  text-align: center;
  margin-bottom: 32px;
}

.logo-container {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 72px;
  height: 72px;
  background: linear-gradient(135deg, #2f9e44, #101820 80%);
  border-radius: 50%;
  margin-bottom: 18px;
  color: #fff;
  border: 2.5px solid #2f9e44;
  box-shadow: 0 2px 12px #2f9e4433;
}

.logo-icon {
  color: #fff;
}

.title-section h1 {
  font-size: 26px;
  font-weight: 700;
  color: #2f9e44;
  margin: 0 0 8px 0;
  letter-spacing: 1px;
}

.subtitle {
  color: #b7f5c5;
  font-size: 15px;
  margin: 0;
  font-weight: 400;
}

.role-selection h3 {
  font-size: 18px;
  color: #b7f5c5;
  text-align: center;
  margin-bottom: 20px;
  font-weight: 600;
}

.role-buttons {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 14px;
}

.role-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 7px;
  padding: 18px 0;
  background: #182c22;
  border: 2px solid #1b3b2a;
  border-radius: 14px;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(.4,0,.2,1);
  font-size: 15px;
  font-weight: 500;
  color: #b7f5c5;
  box-shadow: 0 2px 8px #2f9e4415;
}

.role-btn:hover {
  border-color: #2f9e44;
  color: #2f9e44;
  background: #101820;
  transform: scale(1.04);
}

.role-student:hover {
  border-color: #2f9e44;
  background: #1b3b2a;
}

.role-lecturer:hover {
  border-color: #38b000;
  background: #1b3b2a;
}

.role-advisor:hover {
  border-color: #70e000;
  background: #1b3b2a;
}

.role-admin:hover {
  border-color: #008000;
  background: #1b3b2a;
}

.login-form {
  margin-top: 24px;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  color: #b7f5c5;
  font-size: 14px;
  cursor: pointer;
  padding: 8px 0;
  margin-bottom: 20px;
  transition: color 0.2s;
}

.back-btn:hover {
  color: #2f9e44;
}

.selected-role-display {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px;
  background: rgba(47, 158, 68, 0.12);
  border-radius: 8px;
  margin-bottom: 24px;
  color: #2f9e44;
  font-weight: 600;
}

.error-message,
.success-message {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 14px;
}

.error-message {
  background: #2f1b1b;
  color: #ff5e5e;
  border: 1px solid #ff5e5e55;
}

.success-message {
  background: #1b2f1b;
  color: #38b000;
  border: 1px solid #38b00055;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #b7f5c5;
  margin-bottom: 8px;
}

.input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.input-container input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border: 2px solid #1b3b2a;
  border-radius: 8px;
  font-size: 16px;
  background: #182c22;
  color: #b7f5c5;
  transition: all 0.2s;
  outline: none;
}

.input-container input:focus {
  border-color: #2f9e44;
  box-shadow: 0 0 0 2px #2f9e4422;
}

.input-container input:disabled {
  background: #101820;
  color: #6c757d;
  cursor: not-allowed;
}

.input-icon {
  position: absolute;
  left: 16px;
  color: #38b000;
  z-index: 1;
}

.toggle-password {
  position: absolute;
  right: 16px;
  background: none;
  border: none;
  color: #38b000;
  cursor: pointer;
  transition: color 0.2s;
}

.toggle-password:hover {
  color: #b7f5c5;
}

.field-error {
  font-size: 12px;
  color: #ff5e5e;
  margin-top: 4px;
  display: block;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.checkbox-container {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #b7f5c5;
  cursor: pointer;
}

.checkbox-container input[type="checkbox"] {
  display: none;
}

.checkmark {
  width: 18px;
  height: 18px;
  border: 2px solid #1b3b2a;
  border-radius: 4px;
  position: relative;
  transition: all 0.2s;
  background: #182c22;
}

.checkbox-container input[type="checkbox"]:checked + .checkmark {
  background: #2f9e44;
  border-color: #2f9e44;
}

.checkbox-container input[type="checkbox"]:checked + .checkmark::after {
  content: "✓";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #fff;
  font-size: 12px;
  font-weight: bold;
}

.remember-me-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.remember-me-hint {
  font-size: 11px;
  color: #b7f5c5;
  font-weight: normal;
}

.forgot-link {
  font-size: 14px;
  color: #38b000;
  text-decoration: none;
  font-weight: 500;
}

.forgot-link:hover {
  text-decoration: underline;
  color: #b7f5c5;
}

.login-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 14px 24px;
  background: linear-gradient(90deg, #2f9e44 60%, #101820 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 2px 8px #2f9e4415;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 4px 16px #2f9e4433;
  background: linear-gradient(90deg, #38b000 60%, #101820 100%);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(47, 158, 68, 0.3);
  border-top: 2px solid #fff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.dev-section {
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #1b3b2a;
}

.dev-section h4 {
  font-size: 14px;
  color: #b7f5c5;
  margin-bottom: 12px;
  text-align: center;
}

.dev-buttons {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}

.dev-btn {
  padding: 8px 12px;
  background: #182c22;
  border: 1px solid #1b3b2a;
  border-radius: 6px;
  font-size: 12px;
  color: #b7f5c5;
  cursor: pointer;
  transition: all 0.2s;
}

.dev-btn:hover:not(:disabled) {
  background: #2f9e44;
  color: #fff;
}

.login-footer {
  margin-top: 30px;
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid #1b3b2a;
}

.login-footer p {
  font-size: 12px;
  color: #b7f5c5;
  margin: 4px 0;
}

.debug-section {
  margin-top: 20px;
  padding-top: 15px;
  border-top: 1px solid #1b3b2a;
}

.debug-section h5 {
  font-size: 12px;
  color: #b7f5c5;
  margin-bottom: 8px;
  text-align: center;
}

.debug-info {
  background: #182c22;
  border: 1px solid #1b3b2a;
  border-radius: 6px;
  padding: 10px;
  font-size: 11px;
  color: #b7f5c5;
}

.debug-info p {
  margin: 2px 0;
  display: flex;
  justify-content: space-between;
}

.debug-info strong {
  color: #2f9e44;
}

/* Responsive Design */
@media (max-width: 768px) {
  .login-container {
    padding: 10px;
  }

  .login-wrapper {
    padding: 28px 10px;
    max-width: 98vw;
  }

  .role-buttons {
    grid-template-columns: 1fr;
  }

  .form-options {
    flex-direction: column;
    gap: 12px;
    align-items: flex-start;
  }

  .dev-buttons {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .login-wrapper {
    padding: 18px 4vw;
  }

  .title-section h1 {
    font-size: 20px;
  }

  .input-container input {
    font-size: 15px;
  }
}
</style>
