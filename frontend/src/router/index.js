import { createRouter, createWebHistory } from "vue-router";

// Student Views
import StudentDashboard from "../views/StudentView/StudentDashboard.vue";
import CourseDetailView from "../views/StudentView/CourseDetailView.vue";
import PerformanceComparison from "../views/StudentView/PerformanceComparison.vue";
import PerformanceTrends from "../views/StudentView/PerformanceTrends.vue";
import WhatIfSimulator from "../views/StudentView/WhatIfSimulator.vue";
import RemarkRequests from "../views/StudentView/RemarkRequests.vue";
import StudentNotifications from "../views/StudentView/StudentNotifications.vue";
import StudentProfile from "../views/StudentView/StudentProfile.vue";
import StudentSettings from "../views/StudentView/StudentSettings.vue";
import StudentHelp from "../views/StudentView/StudentHelp.vue";

// Advisor Views
import AdvisorDashboard from "../views/AdvisorView/AdvisorDashboard.vue";
import AdviseeProfile from "../views/AdvisorView/AdviseeProfile.vue";
import AdvisorAdvisees from "../views/AdvisorView/AdvisorAdvisees.vue";

// Lecturer Views
import LecturerDashboard from "../views/LecturerView/LecturerDashboard.vue";
import LecturerCourseView from "../views/LecturerView/LecturerCourseView.vue";
import LecturerStudentsView from "../views/LecturerView/LecturerStudentsView.vue";
import LecturerAnalyticsView from "../views/LecturerView/LecturerAnalyticsView.vue";
import CreateComponent from "../views/LecturerView/CreateComponent.vue";
import LecturerAnalytics from "../views/LecturerView/LecturerAnalytics.vue";
import LecturerNotifications from "../views/LecturerView/LecturerNotifications.vue";
import LecturerProfile from "../views/LecturerView/LecturerProfile.vue";
import LecturerSettings from "../views/LecturerView/LecturerSettings.vue";
import StudentManagement from "../views/LecturerView/StudentManagement.vue";

// Admin Views
import AdminPanel from "../views/AdminView/AdminPanel.vue";
import UserManagement from "../views/AdminView/UserManagement.vue";
import AdminLayout from "../layouts/AdminLayout.vue";

// Other Views from Wajeeha
import HomeView from "../views/HomeView.vue";
import CourseList from "../views/CourseManagementView/CourseList.vue";
import AssignLecturers from "../views/CourseManagementView/AssignLecturers.vue";
import MarksUpdate from "../views/MarksUpdateView/MarksUpdate.vue";
import SystemLogs from "../views/SystemLogsView/SystemLogs.vue";
import SubmitRemarkRequest from "../views/RemarkRequestView/SubmitRemarkRequest.vue";
import ReviewRemarkRequest from "../views/RemarkRequestView/ReviewRemarkRequest.vue";
import EnrollmentPanel from "../views/EnrollmentView/EnrollmentPanel.vue";

// Layouts
import StudentLayout from "../layouts/StudentLayout.vue";
import AdvisorLayout from "../layouts/AdvisorLayout.vue";
import LecturerLayout from "../layouts/LecturerLayout.vue";

// Auth service for route guards
import authService from "@/services/auth";

// Import views
import Login from "@/views/LoginPage.vue";

const routes = [
  {
    path: "/",
    redirect: "/login",
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: {
      guestOnly: true,
    },
  },
  // Student routes
  {
    path: "/student",
    component: StudentLayout,
    meta: {
      requiresAuth: true,
      roles: ["student"],
    },
    children: [
      { path: "", redirect: "/student/dashboard" },
      {
        path: "dashboard",
        name: "StudentDashboard",
        component: StudentDashboard,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "course/:courseId?",
        name: "CourseDetailView",
        component: CourseDetailView,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "performance-comparison",
        name: "PerformanceComparison",
        component: PerformanceComparison,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "performance-trends",
        name: "PerformanceTrends",
        component: PerformanceTrends,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "what-if-simulator",
        name: "WhatIfSimulator",
        component: WhatIfSimulator,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "remark-requests",
        name: "RemarkRequests",
        component: RemarkRequests,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "notifications",
        name: "StudentNotifications",
        component: StudentNotifications,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "profile",
        name: "StudentProfile",
        component: StudentProfile,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "settings",
        name: "StudentSettings",
        component: StudentSettings,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "help",
        name: "StudentHelp",
        component: StudentHelp,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      // Wajeeha's new student views
      {
        path: "marks-update",
        name: "MarksUpdate",
        component: MarksUpdate,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "submit-remark-request",
        name: "SubmitRemarkRequest",
        component: SubmitRemarkRequest,
        meta: { requiresAuth: true, roles: ["student"] },
      },
      {
        path: "review-remark-request",
        name: "ReviewRemarkRequest",
        component: ReviewRemarkRequest,
        meta: { requiresAuth: true, roles: ["student"] },
      },
    ],
  },
  // Lecturer routes
  {
    path: "/lecturer",
    component: LecturerLayout,
    meta: {
      requiresAuth: true,
      roles: ["lecturer"],
    },
    children: [
      { path: "", redirect: "/lecturer/dashboard" },
      {
        path: "dashboard",
        name: "LecturerDashboard",
        component: LecturerDashboard,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
      {
        path: "course/:courseId",
        name: "LecturerCourseView",
        component: LecturerCourseView,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
      {
        path: "course/:courseId/students",
        name: "LecturerStudentsView",
        component: LecturerStudentsView,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
      {
        path: "course/:courseId/analytics",
        name: "LecturerAnalyticsView",
        component: LecturerAnalyticsView,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
      {
        path: "components/new",
        name: "CreateComponent",
        component: CreateComponent,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
      {
        path: "analytics",
        name: "LecturerAnalytics",
        component: LecturerAnalytics,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
      {
        path: "notifications",
        name: "LecturerNotifications",
        component: LecturerNotifications,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
      {
        path: "profile",
        name: "LecturerProfile",
        component: LecturerProfile,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
      {
        path: "settings",
        name: "LecturerSettings",
        component: LecturerSettings,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
      // Wajeeha's new lecturer views
      {
        path: "student-management",
        name: "StudentManagement",
        component: StudentManagement,
        meta: { requiresAuth: true, roles: ["lecturer"] },
      },
    ],
  },
  // Advisor routes
  {
    path: "/advisor",
    component: AdvisorLayout,
    meta: {
      requiresAuth: true,
      roles: ["advisor"],
    },
    children: [
      { path: "", redirect: "/advisor/dashboard" },
      {
        path: "dashboard",
        name: "AdvisorDashboard",
        component: AdvisorDashboard,
        meta: { requiresAuth: true, roles: ["advisor"] },
      },
      {
        path: "advisees",
        name: "AdvisorAdvisees",
        component: AdvisorAdvisees,
        meta: { requiresAuth: true, roles: ["advisor"] },
      },
      {
        path: "advisee/:studentId?",
        name: "AdviseeProfile",
        component: AdviseeProfile,
        meta: { requiresAuth: true, roles: ["advisor"] },
      },
    ],
  },
  // Admin routes
  {
    path: "/admin",
    component: AdminLayout,
    meta: {
      requiresAuth: true,
      roles: ["admin"],
    },
    children: [
      { path: "", redirect: "/admin/dashboard" },
      {
        path: "dashboard",
        name: "AdminDashboard",
        component: AdminPanel,
        meta: { requiresAuth: true, roles: ["admin"] },
      },
      {
        path: "users",
        name: "UserManagement",
        component: UserManagement,
        meta: { requiresAuth: true, roles: ["admin"] },
      },
      {
        path: "courses",
        name: "CourseList",
        component: CourseList,
        meta: { requiresAuth: true, roles: ["admin"] },
      },
      {
        path: "assign-lecturers",
        name: "AssignLecturers",
        component: AssignLecturers,
        meta: { requiresAuth: true, roles: ["admin"] },
      },
      {
        path: "system-logs",
        name: "SystemLogs",
        component: SystemLogs,
        meta: { requiresAuth: true, roles: ["admin"] },
      },
      {
        path: "enrollments",
        name: "EnrollmentPanel",
        component: EnrollmentPanel,
        meta: { requiresAuth: true, roles: ["admin"] },
      },
    ],
  },
  // Home route (if needed)
  { path: "/home", name: "HomeView", component: HomeView },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
  const isAuthenticated = authService.isAuthenticated();
  const userRole = authService.getCurrentUserRole();

  // Check if route requires authentication
  if (to.meta.requiresAuth && !isAuthenticated) {
    next("/login");
    return;
  }

  // Check if route is guest only (like login page)
  if (to.meta.guestOnly && isAuthenticated) {
    // Already authenticated, redirect to appropriate dashboard
    const dashboardRoutes = {
      student: "/student/dashboard",
      advisor: "/advisor/dashboard",
      lecturer: "/lecturer/dashboard",
      admin: "/admin/dashboard",
    };
    next(dashboardRoutes[userRole] || "/login");
    return;
  }

  // Check role-based access
  if (to.meta.roles && !to.meta.roles.includes(userRole)) {
    next("/login");
    return;
  }

  next();
});

// After navigation hook for page titles
router.afterEach((to) => {
  const pageTitles = {
    LoginPage: "Login - Student Marks System",
    StudentDashboard: "Student Dashboard - Student Marks System",
    AdvisorDashboard: "Advisor Dashboard - Student Marks System",
    AdvisorAdvisees: "My Advisees - Student Marks System",
    CourseDetailView: "Course Details - Student Marks System",
    PerformanceComparison: "Performance Comparison - Student Marks System",
    PerformanceTrends: "Performance Trends - Student Marks System",
    WhatIfSimulator: "What-If Simulator - Student Marks System",
    AdviseeProfile: "Advisee Profile - Student Marks System",
    StudentNotifications: "Notifications - Student Marks System",
    StudentProfile: "My Profile - Student Marks System",
    StudentSettings: "Settings - Student Marks System",
    StudentHelp: "Help & Support - Student Marks System",
    // Add new page titles as needed
    MarksUpdate: "Marks Update - Student Marks System",
    SubmitRemarkRequest: "Submit Remark Request - Student Marks System",
    ReviewRemarkRequest: "Review Remark Request - Student Marks System",
    AdminPanel: "Admin Panel - Student Marks System",
    CourseList: "Course List - Student Marks System",
    AssignLecturers: "Assign Lecturers - Student Marks System",
    SystemLogs: "System Logs - Student Marks System",
    EnrollmentPanel: "Enrollment Panel - Student Marks System",
    HomeView: "Home - Student Marks System",
  };

  document.title = pageTitles[to.name] || "Marks Management System";
});

export default router;
