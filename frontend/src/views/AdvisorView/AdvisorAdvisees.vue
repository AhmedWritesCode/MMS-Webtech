<template>
  <div class="advisor-advisees">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h1>My Advisees</h1>
          <p class="header-subtitle">
            Manage and monitor your assigned students
          </p>
        </div>
        <div class="header-stats">
          <div class="stat-card">
            <span class="stat-number">{{ totalAdvisees }}</span>
            <span class="stat-label">Total Advisees</span>
          </div>
          <div class="stat-card at-risk">
            <span class="stat-number">{{ atRiskCount }}</span>
            <span class="stat-label">At Risk</span>
          </div>
          <div class="stat-card excellent">
            <span class="stat-number">{{ excellentCount }}</span>
            <span class="stat-label">Excellent</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Advisees Section Component -->
    <AdviseesSection
      :advisees="advisees"
      :search-query="searchQuery"
      :selected-filter="selectedFilter"
      :view-mode="viewMode"
      :filtered-advisees="filteredAdvisees"
      :loading="loading"
      @update-search="updateSearch"
      @update-filter="updateFilter"
      @update-view-mode="updateViewMode"
      @view-student="viewStudentProfile"
      @schedule-student-meeting="scheduleStudentMeeting"
      @add-student-note="addStudentNote"
    />

    <!-- Loading Overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner">Loading advisees...</div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="retryLoadData">Retry</button>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && advisees.length === 0" class="empty-state">
      <div class="empty-icon">👥</div>
      <h3>No Advisees Assigned</h3>
      <p>You don't have any students assigned to you yet.</p>
      <p>Contact your department administrator to get assigned advisees.</p>
    </div>
  </div>
</template>

<script>
import AdviseesSection from "@/components/AdvisorComponents/DashboardComponents/AdviseesSection.vue";
import { advisorAPI, utilityAPI } from "@/services/api";

export default {
  name: "AdvisorAdvisees",
  components: {
    AdviseesSection,
  },
  data() {
    return {
      loading: false,
      error: null,
      advisorId: null,

      // Advisees data
      advisees: [],
      searchQuery: "",
      selectedFilter: "all",
      viewMode: "grid",
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
        case "good":
          filtered = filtered.filter(
            (advisee) => advisee.gpa >= 3.0 && advisee.gpa < 3.5
          );
          break;
        case "average":
          filtered = filtered.filter(
            (advisee) => advisee.gpa >= 2.5 && advisee.gpa < 3.0
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

    totalAdvisees() {
      return this.advisees.length;
    },

    atRiskCount() {
      return this.advisees.filter((a) => a.isAtRisk).length;
    },

    excellentCount() {
      return this.advisees.filter((a) => a.gpa >= 3.5).length;
    },
  },

  async mounted() {
    await this.loadAdvisorProfile();
    await this.loadAdvisees();
  },

  methods: {
    async loadAdvisorProfile() {
      try {
        const response = await utilityAPI.getUserProfile();
        if (response.success && response.data.role === "advisor") {
          this.advisorId = response.data.id;
        }
      } catch (error) {
        console.error("Error loading advisor profile:", error);
        this.error = "Failed to load advisor profile";
      }
    },

    async loadAdvisees() {
      this.loading = true;
      this.error = null;

      try {
        if (!this.advisorId) {
          this.error = "Advisor ID not found";
          return;
        }

        const response = await advisorAPI.getAdvisees(this.advisorId);

        if (response.success) {
          const adviseesData = response.data.advisees || [];

          this.advisees = adviseesData.map((advisee) => ({
            id: advisee.id,
            name: advisee.full_name,
            matricNumber: advisee.matric_number,
            program: advisee.program || "Unknown Program",
            currentSemester: advisee.current_semester || 1,
            currentGPA: parseFloat(advisee.current_gpa) || 0.0,
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
        } else {
          this.error = response.message || "Failed to load advisees";
        }
      } catch (error) {
        console.error("Error loading advisees:", error);
        this.error = "Failed to load advisees. Please try again.";
      } finally {
        this.loading = false;
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

    scheduleStudentMeeting(studentId) {
      this.$router.push(`/advisor/schedule-meeting?student=${studentId}`);
    },

    addStudentNote(studentId) {
      this.$router.push(`/advisor/advisee/${studentId}?tab=notes`);
    },

    // Utility methods
    retryLoadData() {
      this.loadAdvisees();
    },
  },
};
</script>

<style scoped>
.advisor-advisees {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
  background: #f8fafc;
  min-height: 100vh;
  position: relative;
}

/* Page Header */
.page-header {
  background: white;
  border-radius: 16px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-info h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 8px 0;
  color: #1e293b;
}

.header-subtitle {
  font-size: 1.1rem;
  color: #64748b;
  margin: 0;
}

.header-stats {
  display: flex;
  gap: 20px;
}

.stat-card {
  text-align: center;
  padding: 16px 24px;
  border-radius: 12px;
  background: #f8fafc;
  border: 2px solid #e2e8f0;
  min-width: 100px;
}

.stat-card.at-risk {
  background: #fef2f2;
  border-color: #fecaca;
}

.stat-card.excellent {
  background: #f0fdf4;
  border-color: #bbf7d0;
}

.stat-number {
  display: block;
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.stat-card.at-risk .stat-number {
  color: #dc2626;
}

.stat-card.excellent .stat-number {
  color: #059669;
}

.stat-label {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 600;
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  background: #4f46e5;
  color: white;
  padding: 20px 40px;
  border-radius: 8px;
  font-weight: 500;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Error Message */
.error-message {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #fee;
  border: 1px solid #fcc;
  color: #c33;
  padding: 15px;
  border-radius: 8px;
  max-width: 300px;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.error-message button {
  background: #c33;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  margin-top: 10px;
  cursor: pointer;
}

.error-message button:hover {
  background: #b22;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 20px;
}

.empty-state h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 12px 0;
}

.empty-state p {
  font-size: 1rem;
  color: #64748b;
  margin: 0 0 8px 0;
  line-height: 1.5;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .header-content {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }

  .header-stats {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .advisor-advisees {
    padding: 10px;
  }

  .page-header {
    padding: 20px;
  }

  .header-info h1 {
    font-size: 2rem;
  }

  .header-stats {
    flex-direction: column;
    gap: 12px;
  }

  .stat-card {
    min-width: auto;
  }

  .error-message {
    top: 10px;
    right: 10px;
    left: 10px;
    max-width: none;
  }
}

@media (max-width: 480px) {
  .header-info h1 {
    font-size: 1.8rem;
  }

  .header-subtitle {
    font-size: 1rem;
  }
}
</style>
