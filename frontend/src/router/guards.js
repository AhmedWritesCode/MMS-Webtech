// src/router/guards.js
import authService from "@/services/auth";

/**
 * Authentication guard - checks if user is logged in
 * @param {Object} to - target route
 * @param {Object} from - current route
 * @param {Function} next - navigation function
 */
export const authGuard = (to, from, next) => {
  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authService.isAuthenticated()) {
      // Redirect to login with return URL
      next({
        path: "/login",
        query: {
          redirect: to.fullPath,
          message: "Please log in to access this page",
        },
      });
      return;
    }
  }

  // Check if authenticated user should be redirected away from login
  if (to.meta.hideForAuthenticated && authService.isAuthenticated()) {
    const userRole = authService.getCurrentUserRole();
    const dashboardRoutes = {
      student: "/student",
      advisor: "/advisor",
      lecturer: "/lecturer",
      admin: "/admin",
    };

    next(dashboardRoutes[userRole] || "/");
    return;
  }

  next();
};

/**
 * Role-based guard factory - creates guards for specific roles
 * @param {Array} allowedRoles - array of allowed roles
 * @returns {Function} navigation guard function
 */
export const roleGuard = (allowedRoles) => {
  return (to, from, next) => {
    if (!authService.isAuthenticated()) {
      next("/login");
      return;
    }

    const userRole = authService.getCurrentUserRole();

    if (!allowedRoles.includes(userRole)) {
      // User doesn't have required role
      next({
        path: "/unauthorized",
        query: {
          message: `Access denied. Required role: ${allowedRoles.join(" or ")}`,
          userRole: userRole,
        },
      });
      return;
    }

    next();
  };
};

/**
 * Student-only guard
 */
export const studentGuard = roleGuard(["student"]);

/**
 * Advisor-only guard
 */
export const advisorGuard = roleGuard(["advisor"]);

/**
 * Lecturer-only guard
 */
export const lecturerGuard = roleGuard(["lecturer"]);

/**
 * Admin-only guard
 */
export const adminGuard = roleGuard(["admin"]);

/**
 * Staff guard (lecturer, advisor, admin)
 */
export const staffGuard = roleGuard(["lecturer", "advisor", "admin"]);

/**
 * Enhanced auth guard with role checking
 * @param {Object} to - target route
 * @param {Object} from - current route
 * @param {Function} next - navigation function
 */
export const enhancedAuthGuard = (to, from, next) => {
  // First check basic authentication
  if (to.meta.requiresAuth && !authService.isAuthenticated()) {
    next({
      path: "/login",
      query: { redirect: to.fullPath },
    });
    return;
  }

  // Then check role requirements
  if (to.meta.roles && to.meta.roles.length > 0) {
    const userRole = authService.getCurrentUserRole();

    if (!to.meta.roles.includes(userRole)) {
      next("/unauthorized");
      return;
    }
  }

  // Check if user is trying to access routes not meant for their role
  if (authService.isAuthenticated()) {
    const userRole = authService.getCurrentUserRole();

    // Prevent students from accessing non-student routes
    if (
      userRole === "student" &&
      !to.path.startsWith("/student") &&
      to.path !== "/login"
    ) {
      next("/student");
      return;
    }

    // Prevent advisors from accessing non-advisor routes
    if (
      userRole === "advisor" &&
      !to.path.startsWith("/advisor") &&
      to.path !== "/login"
    ) {
      next("/advisor");
      return;
    }

    // Similar checks for other roles...
  }

  next();
};

/**
 * Logout guard - handles cleanup when user logs out
 */
export const logoutGuard = (to, from, next) => {
  // Clear any cached data
  authService.logout();

  // Redirect to login
  next("/login");
};

/**
 * Development guard - only allows access in development mode
 */
export const devGuard = (to, from, next) => {
  if (process.env.NODE_ENV === "development") {
    next();
  } else {
    next("/not-found");
  }
};
