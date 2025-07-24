<template>
  <div class="advisor-dashboard">
    <!-- Header Component -->
    <AdvisorDashboardHeader
      :advisor-name="advisorName"
      :advisor-id="advisorId != null ? String(advisorId) : ''"
      :department="department"
      :total-advisees="totalAdvisees"
      :at-risk-students="atRiskStudents"
      :recent-meetings="recentMeetings"
      :loading="loading"
    />

    <!-- Alert Section Component -->
    <AlertsSection
      :at-risk-students="atRiskStudentsList"
      :loading="loadingAtRisk"
      @view-student="viewStudentProfile"
      @view-all-at-risk="viewAllAtRiskStudents"
    />

    <!-- Overview Cards Component -->
    <OverviewCards
      :performance-stats="performanceStats"
      :recent-activities="recentActivities"
      :upcoming-meetings="upcomingMeetings"
      :loading="loadingOverview"
      @view-all-activity="viewAllActivity"
      @view-meeting="viewMeetingDetails"
      @schedule-meeting="scheduleMeeting"
    />

    <!-- Advisees Section Component -->
    <AdviseesSection
      :advisees="advisees"
      :search-query="searchQuery"
      :selected-filter="selectedFilter"
      :view-mode="viewMode"
      :filtered-advisees="filteredAdvisees"
      :loading="loadingAdvisees"
      @update-search="updateSearch"
      @update-filter="updateFilter"
      @update-view-mode="updateViewMode"
      @view-student="viewStudentProfile"
      @schedule-student-meeting="scheduleStudentMeeting"
      @add-student-note="addStudentNote"
    />

    <!-- Quick Actions Section Component -->
    <QuickActionsSection
      @generate-progress-reports="generateProgressReports"
      @schedule-group-meeting="scheduleGroupMeeting"
      @export-student-data="exportStudentData"
      @view-analytics="viewAnalytics"
      @manage-notifications="manageNotifications"
      @access-resources="accessResources"
    />

    <!-- Loading Overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner">Loading dashboard data...</div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="retryLoadData">Retry</button>
    </div>
  </div>
</template>

<script>
// Import all the components
import AdvisorDashboardHeader from "@/components/AdvisorComponents/DashboardComponents/AdvisorDashboardHeader.vue";
import AlertsSection from "@/components/AdvisorComponents/DashboardComponents/AlertsSection.vue";
import OverviewCards from "@/components/AdvisorComponents/DashboardComponents/OverviewCards.vue";
import AdviseesSection from "@/components/AdvisorComponents/DashboardComponents/AdviseesSection.vue";
import QuickActionsSection from "@/components/AdvisorComponents/DashboardComponents/QuickActionsSection.vue";

// Import API services
import { advisorAPI, utilityAPI } from "@/services/api";

export default {
  name: "AdvisorDashboard",
  components: {
    AdvisorDashboardHeader,
    AlertsSection,
    OverviewCards,
    AdviseesSection,
    QuickActionsSection,
  },
  data() {
    return {
      loading: false,
      loadingAdvisees: false,
      loadingAtRisk: false,
      loadingOverview: false,
      error: null,

      // Advisor Information
      advisorName: "Loading...",
      advisorId: null,
      department: "",
      totalAdvisees: 0,
      atRiskStudents: 0,
      recentMeetings: 0,

      // Filtered and search functionality
      searchQuery: "",
      selectedFilter: "all",
      viewMode: "grid",

      // Data arrays
      advisees: [],
      atRiskStudentsList: [],
      performanceStats: {},
      recentActivities: [],
      upcomingMeetings: [],
    };
  },

  computed: {
    filteredAdvisees() {
      let filtered = this.advisees;

      // Apply search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(
          (advisee) =>
            advisee.name.toLowerCase().includes(query) ||
            advisee.matricNumber.toLowerCase().includes(query) ||
            advisee.program.toLowerCase().includes(query)
        );
      }

      // Apply status filter
      switch (this.selectedFilter) {
        case "at-risk":
          filtered = filtered.filter((advisee) => advisee.isAtRisk);
          break;
        case "excellent":
          filtered = filtered.filter((advisee) => advisee.gpa >= 3.5);
          break;
        case "average":
          filtered = filtered.filter(
            (advisee) => advisee.gpa >= 2.5 && advisee.gpa < 3.5
          );
          break;
        case "struggling":
          filtered = filtered.filter((advisee) => advisee.gpa < 2.5);
          break;
        default:
          // "all" - no additional filtering
          break;
      }

      return filtered;
    },
  },

  async mounted() {
    await this.loadDashboardData();
  },

  methods: {
    async loadDashboardData() {
      this.loading = true;
      this.error = null;

      try {
        // First get advisor profile
        await this.loadAdvisorProfile();

        // Then load all other data concurrently
        await Promise.all([
          this.loadAdvisees(),
          this.loadAtRiskStudents(),
          this.loadPerformanceStats(),
          this.loadRecentActivities(),
        ]);
      } catch (error) {
        console.error("Error loading advisor dashboard:", error);
        this.error = "Failed to load dashboard data. Please try again.";
      } finally {
        this.loading = false;
      }
    },

    async loadAdvisorProfile() {
      try {
        const response = await utilityAPI.getUserProfile();

        if (response.success) {
          const userData = response.data;

          if ((userData.role === "advisor") || (userData.user_type === "advisor")) {
            this.advisorId = userData.id;
            this.advisorName = userData.full_name || "Dr. Advisor";
            this.department = userData.department || "";
          }
        }
      } catch (error) {
        console.error("Error loading advisor profile:", error);
        // Use default values if API fails
        this.advisorName = "Dr. Advisor";
        this.advisorId = 1; // Default for testing
      }
    },

    async loadAdvisees() {
      this.loadingAdvisees = true;

      try {
        if (!this.advisorId) return;

        const response = await advisorAPI.getAdvisees(this.advisorId);

        if (response.success && response.data && response.data.advisees) {
          const adviseesData = response.data.advisees;
          this.advisees = adviseesData.map((advisee) => ({
            id: advisee.id,
            name: advisee.full_name,
            matricNumber: advisee.matric_number,
            program: advisee.program || "Unknown Program",
            currentSemester: advisee.current_semester || 1,
            gpa: parseFloat(advisee.current_gpa) || 0.0,
            creditHours: advisee.total_credit_hours || 0,
            isAtRisk:
              advisee.risk_level === "High" ||
              advisee.risk_level === "Critical",
            lastMeeting: advisee.last_meeting_date
              ? new Date(advisee.last_meeting_date)
              : null,
            email: advisee.email || "",
            phone: advisee.phone || "",
            enrolledCourses: advisee.enrolled_courses || 0,
            completedCourses: advisee.completed_courses || 0,
            status: advisee.academic_standing || "active",
            completionRate: advisee.completion_rate || 0,
            notesCount: advisee.notes_count || 0,
          }));
          this.totalAdvisees = this.advisees.length;
          this.atRiskStudents = this.advisees.filter((a) => a.isAtRisk).length;
        }
      } catch (error) {
        console.error("Error loading advisees:", error);
        this.advisees = [];
      } finally {
        this.loadingAdvisees = false;
      }
    },

    async loadAtRiskStudents() {
      this.loadingAtRisk = true;

      try {
        if (!this.advisorId) return;

        const response = await advisorAPI.getAtRiskStudents(this.advisorId);

        if (response.data.success) {
          const atRiskData = response.data.data || [];

          this.atRiskStudentsList = atRiskData.map((student) => ({
            id: student.student_id,
            name: student.name,
            matricNumber: student.matric_number,
            gpa: parseFloat(student.gpa) || 0.0,
            riskFactors: student.risk_factors || [],
            lastActivity: student.last_activity
              ? new Date(student.last_activity)
              : null,
            interventionRequired: student.intervention_required || false,
            program: student.program || "Unknown",
            semester: student.current_semester || 1,
          }));
        }
      } catch (error) {
        console.error("Error loading at-risk students:", error);
        this.atRiskStudentsList = [];
      } finally {
        this.loadingAtRisk = false;
      }
    },

    async loadPerformanceStats() {
      try {
        // Calculate performance stats from advisees data
        if (this.advisees.length > 0) {
          const totalGPA = this.advisees.reduce(
            (sum, advisee) => sum + advisee.gpa,
            0
          );
          const averageGPA = totalGPA / this.advisees.length;

          const gpaDistribution = {
            excellent: this.advisees.filter((a) => a.gpa >= 3.5).length,
            good: this.advisees.filter((a) => a.gpa >= 3.0 && a.gpa < 3.5)
              .length,
            average: this.advisees.filter((a) => a.gpa >= 2.5 && a.gpa < 3.0)
              .length,
            poor: this.advisees.filter((a) => a.gpa < 2.5).length,
          };

          this.performanceStats = {
            averageGPA: averageGPA.toFixed(2),
            totalAdvisees: this.advisees.length,
            atRiskCount: this.atRiskStudents,
            gpaDistribution,
            improvementRate: 0, // This would need historical data
            meetingsThisMonth: this.recentMeetings,
          };
        }
      } catch (error) {
        console.error("Error calculating performance stats:", error);
        this.performanceStats = {};
      }
    },

    async loadRecentActivities() {
      try {
        // Create activities from recent advisee interactions
        const activities = [];

        // Add recent meetings as activities
        this.advisees.forEach((advisee) => {
          if (advisee.lastMeeting) {
            const daysSinceLastMeeting = Math.floor(
              (new Date() - advisee.lastMeeting) / (1000 * 60 * 60 * 24)
            );

            if (daysSinceLastMeeting <= 7) {
              activities.push({
                id: `meeting_${advisee.id}`,
                type: "meeting",
                title: "Student Meeting",
                description: `Met with ${advisee.name} (${advisee.matricNumber})`,
                timestamp: advisee.lastMeeting,
                studentId: advisee.id,
              });
            }
          }

          // Add at-risk alerts as activities
          if (advisee.isAtRisk) {
            activities.push({
              id: `risk_${advisee.id}`,
              type: "alert",
              title: "At-Risk Student Alert",
              description: `${advisee.name} requires attention (GPA: ${advisee.gpa})`,
              timestamp: new Date(), // Current time for alerts
              studentId: advisee.id,
              priority: "high",
            });
          }
        });

        // Sort by timestamp (most recent first)
        this.recentActivities = activities
          .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
          .slice(0, 10); // Keep only the 10 most recent
      } catch (error) {
        console.error("Error loading recent activities:", error);
        this.recentActivities = [];
      }
    },

    // Filter and search methods
    updateSearch(query) {
      this.searchQuery = query;
    },

    updateFilter(filter) {
      this.selectedFilter = filter;
    },

    updateViewMode(mode) {
      this.viewMode = mode;
    },

    // Event handlers
    viewStudentProfile(studentId) {
      this.$router.push(`/advisor/advisee/${studentId}`);
    },

    viewAllAtRiskStudents() {
      this.$router.push("/advisor/at-risk-students");
    },

    viewAllActivity() {
      this.$router.push("/advisor/activities");
    },

    viewMeetingDetails(meetingId) {
      this.$router.push(`/advisor/meeting/${meetingId}`);
    },

    scheduleMeeting() {
      this.$router.push("/advisor/schedule-meeting");
    },

    async scheduleStudentMeeting(studentId) {
      this.$router.push(`/advisor/schedule-meeting?student=${studentId}`);
    },

    async addStudentNote(studentId) {
      this.$router.push(`/advisor/advisee/${studentId}?action=add-note`);
    },

    // Quick actions handlers
    async generateProgressReports() {
      try {
        // This could trigger bulk report generation
        this.$router.push("/advisor/reports/progress");
      } catch (error) {
        console.error("Error generating progress reports:", error);
        this.showError("Failed to generate progress reports");
      }
    },

    scheduleGroupMeeting() {
      this.$router.push("/advisor/schedule-group-meeting");
    },

    async exportStudentData() {
      try {
        if (!this.advisorId) return;

        // Export all advisees data
        const exportPromises = this.advisees.map((advisee) =>
          advisorAPI.exportConsultationReport(this.advisorId, advisee.id, "csv")
        );

        await Promise.all(exportPromises);

        // Show success message
        this.showSuccess("Student data exported successfully");
      } catch (error) {
        console.error("Error exporting student data:", error);
        this.showError("Failed to export student data");
      }
    },

    viewAnalytics() {
      this.$router.push("/advisor/analytics");
    },

    manageNotifications() {
      this.$router.push("/advisor/notifications");
    },

    accessResources() {
      this.$router.push("/advisor/resources");
    },

    // Utility methods
    retryLoadData() {
      this.loadDashboardData();
    },

    showError(message) {
      this.error = message;
      setTimeout(() => {
        this.error = null;
      }, 5000);
    },

    showSuccess(message) {
      // You can implement a success notification system here
      console.log("Success:", message);
    },
  },
};
</script>

<style scoped>
.advisor-dashboard {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 2rem;
  background: #f8fafc;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  position: relative;
  min-height: 80vh;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(79, 70, 229, 0.2);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  background: white;
  color: #4f46e5;
  padding: 20px 40px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1.2rem;
  box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
  border: 1px solid rgba(79, 70, 229, 0.2);
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
  }
}

.error-message {
  position: fixed;
  top: 24px;
  right: 24px;
  background: white;
  border: 1px solid #fee2e2;
  color: #dc2626;
  padding: 16px;
  border-radius: 12px;
  max-width: 340px;
  z-index: 1000;
  box-shadow: 0 10px 15px rgba(220, 38, 38, 0.1);
}

.error-message p {
  color: #dc2626;
  margin: 0 0 16px 0;
  font-weight: 600;
}

.error-message button {
  background: #4f46e5;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.error-message button:hover {
  background: #4338ca;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
}

/* Global responsive adjustments */
@media (max-width: 1200px) {
  .advisor-dashboard {
    max-width: 95%;
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .advisor-dashboard {
    padding: 1rem;
    border-radius: 0.8rem;
    margin: 1rem;
    max-width: calc(100% - 2rem);
    gap: 16px;
  }

  .error-message {
    top: 10px;
    right: 10px;
    left: 10px;
    max-width: none;
    padding: 12px;
  }
}
</style>
