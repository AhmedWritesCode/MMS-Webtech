<template>
  <div class="advisee-profile">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Loading student profile...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-message">
        <h3>Error Loading Profile</h3>
        <p>{{ error }}</p>
        <button @click="loadStudentData" class="retry-button">Retry</button>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Header Component -->
      <AdviseeProfileHeader :student="student" />

      <!-- Alert Section Component -->
      <ProfileAlertsSection
        :alerts="student.alerts"
        @handle-alert="handleAlert"
      />

      <!-- Quick Actions Bar Component -->
      <QuickActionsBar
        @schedule-meeting="scheduleMeeting"
        @add-note="addNote"
        @export-report="exportReport"
        @send-message="sendMessage"
      />

      <!-- Main Content -->
      <div class="main-content">
        <!-- Left Column -->
        <div class="left-column">
          <!-- Academic Overview Component -->
          <AcademicOverviewCard :student="student" />

          <!-- Current Courses Component -->
          <CurrentCoursesCard
            :courses="student.currentCourses"
            @view-course-details="viewCourseDetails"
          />

          <!-- Performance Trends Component -->
          <PerformanceTrendsCard
            :performance-trends="performanceTrends"
            :selected-trend-period="selectedTrendPeriod"
            @update-trend-period="updateTrendPeriod"
          />
        </div>

        <!-- Right Column -->
        <div class="right-column">
          <!-- Advisor Notes Component -->
          <AdvisorNotesCard
            :advisor-notes="advisorNotes"
            @add-note="addNote"
            @edit-note="editNote"
            @delete-note="deleteNote"
          />

          <!-- Meeting History Component -->
          <MeetingHistoryCard
            :meeting-history="meetingHistory"
            @view-meeting-details="viewMeetingDetails"
            @schedule-meeting="scheduleMeeting"
          />

          <!-- Academic History Component -->
          <AcademicHistoryCard
            :academic-history="academicHistory"
            :selected-history-view="selectedHistoryView"
            @update-history-view="updateHistoryView"
          />

          <!-- Risk Assessment Component -->
          <RiskAssessmentCard
            :risk-level="student.riskLevel"
            :risk-factors="riskFactors"
            :recommendations="student.recommendations"
          />
        </div>
      </div>

      <!-- Note Modal Component -->
      <NoteModal
        :show-modal="showNoteModal"
        :is-editing="editingNote !== null"
        :note-form="noteForm"
        @close-modal="closeNoteModal"
        @save-note="saveNote"
        @update-note-type="updateNoteType"
        @update-note-content="updateNoteContent"
        @update-note-title="updateNoteTitle"
        @update-meeting-date="updateMeetingDate"
        @update-follow-up-required="updateFollowUpRequired"
      />

      <!-- Export Report Modal Component -->
      <ExportReportModal
        :show-modal="showExportReportModal"
        :student-name="student.name"
        :student-matric="student.matricNumber"
        :student-program="student.program"
        :current-gpa="student.currentGPA"
        :total-notes="advisorNotes.length"
        :last-meeting-date="getLastMeetingDate()"
        :advisor-id="advisorId != null ? String(advisorId) : ''"
        :student-id="studentId != null ? String(studentId) : ''"
        @close-modal="closeExportReportModal"
        @report-generated="handleReportGenerated"
      />

      <!-- Notification Component -->
      <NotificationToast
        :show="notification.show"
        :message="notification.message"
        :type="notification.type"
        @close="hideNotification"
      />
    </div>
  </div>
</template>

<script>
// Import all the components
import AdviseeProfileHeader from "@/components/AdvisorComponents/AdviseeProfileComponents/AdviseeProfileHeader.vue";
import ProfileAlertsSection from "@/components/AdvisorComponents/AdviseeProfileComponents/ProfileAlertsSection.vue";
import QuickActionsBar from "@/components/AdvisorComponents/AdviseeProfileComponents/QuickActionsBar.vue";
import AcademicOverviewCard from "@/components/AdvisorComponents/AdviseeProfileComponents/AcademicOverviewCard.vue";
import CurrentCoursesCard from "@/components/AdvisorComponents/AdviseeProfileComponents/CurrentCoursesCard.vue";
import PerformanceTrendsCard from "@/components/AdvisorComponents/AdviseeProfileComponents/PerformanceTrendsCard.vue";
import AdvisorNotesCard from "@/components/AdvisorComponents/AdviseeProfileComponents/AdvisorNotesCard.vue";
import MeetingHistoryCard from "@/components/AdvisorComponents/AdviseeProfileComponents/MeetingHistoryCard.vue";
import AcademicHistoryCard from "@/components/AdvisorComponents/AdviseeProfileComponents/AcademicHistoryCard.vue";
import RiskAssessmentCard from "@/components/AdvisorComponents/AdviseeProfileComponents/RiskAssessmentCard.vue";
import NoteModal from "@/components/AdvisorComponents/AdviseeProfileComponents/NoteModal.vue";
import ExportReportModal from "@/components/AdvisorComponents/AdviseeProfileComponents/ExportReportModal.vue";
import NotificationToast from "@/components/AdvisorComponents/AdviseeProfileComponents/Notification.vue";

// Import API services
import { advisorAPI } from "@/services/api";
import authService from "@/services/auth";

export default {
  name: "AdviseeProfile",
  components: {
    AdviseeProfileHeader,
    ProfileAlertsSection,
    QuickActionsBar,
    AcademicOverviewCard,
    CurrentCoursesCard,
    PerformanceTrendsCard,
    AdvisorNotesCard,
    MeetingHistoryCard,
    AcademicHistoryCard,
    RiskAssessmentCard,
    NoteModal,
    ExportReportModal,
    NotificationToast,
  },
  data() {
    return {
      studentId: this.$route.params.studentId,
      advisorId: null,
      loading: true,
      error: null,
      selectedTrendPeriod: "semester",
      selectedHistoryView: "semester",
      showNoteModal: false,
      showExportReportModal: false,
      editingNote: null,
      noteForm: {
        type: "meeting",
        title: "",
        content: "",
        meeting_date: new Date().toISOString().split("T")[0],
        follow_up_required: false,
      },
      student: {
        id: null,
        name: "",
        matricNumber: "",
        program: "",
        currentSemester: "",
        yearOfStudy: "",
        currentGPA: 0,
        cumulativeGPA: 0,
        gpaTrend: 0,
        totalCreditHours: 0,
        currentCreditLoad: 0,
        academicStanding: "",
        expectedGraduation: "",
        riskLevel: "",
        recommendations: [],
        alerts: [],
        currentCourses: [],
      },
      advisorNotes: [],
      meetingHistory: [],
      academicHistory: [],
      performanceTrends: [],
      riskFactors: [],
      notification: {
        show: false,
        message: "",
        type: "success", // success, error, info
      },
    };
  },
  async mounted() {
    await this.loadStudentData();
  },
  watch: {
    "$route.params.studentId": {
      handler(newStudentId) {
        if (newStudentId && newStudentId !== this.studentId) {
          this.studentId = newStudentId;
          this.loadStudentData();
        }
      },
      immediate: true,
    },
  },
  methods: {
    async loadStudentData() {
      try {
        this.loading = true;
        this.error = null;

        // Get advisor ID from auth service
        const userData = authService.getCurrentUser();
        if (!userData || userData.role !== "advisor") {
          throw new Error("Unauthorized access. Please login as an advisor.");
        }
        this.advisorId = userData.id;

        // Fetch student breakdown data
        const response = await advisorAPI.getAdviseeBreakdown(
          this.advisorId,
          this.studentId
        );

        if (!response.success) {
          throw new Error(response.message || "Failed to load student data");
        }

        // Update student data with real data from API
        const studentData = response.data;
        const studentInfo = studentData.student_info || {};
        const academicSummary = studentData.academic_summary || {};

        this.student = {
          id: studentInfo.id || this.studentId,
          name: studentInfo.full_name || "Unknown Student",
          matricNumber: studentInfo.matric_number || "",
          program: studentInfo.program || "Unknown Program",
          currentSemester: studentInfo.current_semester || "Current Semester",
          yearOfStudy: studentInfo.year_of_study || "Year of Study",
          currentGPA: academicSummary.current_gpa || 0,
          cumulativeGPA: academicSummary.current_gpa || 0,
          gpaTrend: academicSummary.gpa_trend || 0,
          totalCreditHours: academicSummary.total_credits || 0,
          currentCreditLoad: academicSummary.total_enrolled || 0,
          academicStanding:
            academicSummary.academic_standing || "Good Standing",
          expectedGraduation:
            studentInfo.expected_graduation || "Expected Graduation",
          riskLevel: academicSummary.risk_level || "Low Risk",
          recommendations: academicSummary.recommendations || [
            "Maintain current study habits",
            "Consider advanced courses in area of interest",
            "Explore internship opportunities",
          ],
          alerts: studentData.alerts || [
            {
              id: 1,
              type: "info",
              title: "Academic Performance",
              message: "Student is performing well in current courses.",
              actionText: "View Details",
            },
          ],
          currentCourses: studentData.courses
            ? studentData.courses.map((course) => ({
                id: course.course_id,
                code: course.course_code,
                name: course.course_name,
                credits: course.credits,
                lecturer: course.lecturer_name || "N/A",
                progress: course.completion_rate || 0,
                currentGrade: course.grade_letter || "In Progress", // GPA or Grade letter
              }))
            : [],
        };

        // Load additional data
        await this.loadAdvisorNotes();
        this.loadSampleData(); // For now, keep sample data for other sections
      } catch (error) {
        console.error("Error loading student data:", error);
        this.error = error.message || "Failed to load student profile";
      } finally {
        this.loading = false;
      }
    },

    async loadAdvisorNotes() {
      try {
        const response = await advisorAPI.getAdviseeNotes(
          this.advisorId,
          this.studentId
        );
        if (response.success && response.data && response.data.notes) {
          // Transform the API data to match the component's expected format
          this.advisorNotes = response.data.notes.map((note) => ({
            id: note.id,
            date: new Date(note.meeting_date || note.created_at),
            type: note.note_type,
            content: note.content,
            title: note.title,
            followUpRequired: note.follow_up_required,
            followUpCompleted: note.follow_up_completed,
            createdAt: note.created_at,
          }));
        } else {
          this.advisorNotes = [];
        }
      } catch (error) {
        console.error("Error loading advisor notes:", error);
        this.advisorNotes = [];
      }
    },

    loadSampleData() {
      // Keep sample data for sections that don't have backend endpoints yet
      this.meetingHistory = [
        {
          id: 1,
          date: new Date("2024-10-15"),
          type: "scheduled",
          title: "Mid-Semester Progress Review",
          summary:
            "Reviewed academic progress, discussed challenges in Software Engineering, and set goals for remainder of semester.",
          outcomes: [
            "Join study group for Software Engineering",
            "Schedule weekly check-ins",
            "Complete practice problems by next week",
          ],
        },
        {
          id: 2,
          date: new Date("2024-09-20"),
          type: "urgent",
          title: "Academic Support Discussion",
          summary:
            "Emergency meeting to address concerns about falling behind in coursework.",
          outcomes: [
            "Developed study schedule",
            "Connected with tutoring services",
            "Set up regular progress monitoring",
          ],
        },
      ];

      this.academicHistory = [
        {
          id: 1,
          name: "2024/2025-1 (Current)",
          gpa: this.student.currentGPA,
          credits: this.student.currentCreditLoad,
          courseCount: this.student.currentCourses.length,
          status: "In Progress",
        },
        {
          id: 2,
          name: "2023/2024-2",
          gpa: 3.1,
          credits: 18,
          courseCount: 6,
          status: "Completed",
        },
        {
          id: 3,
          name: "2023/2024-1",
          gpa: 3.35,
          credits: 15,
          courseCount: 5,
          status: "Completed",
        },
        {
          id: 4,
          name: "2022/2023-2",
          gpa: 2.95,
          credits: 18,
          courseCount: 6,
          status: "Completed",
        },
      ];

      this.performanceTrends = [
        { label: "2022/23-2", gpa: 2.95 },
        { label: "2023/24-1", gpa: 3.35 },
        { label: "2023/24-2", gpa: 3.1 },
        { label: "2024/25-1", gpa: this.student.currentGPA },
      ];

      this.riskFactors = [
        {
          name: "Academic Performance",
          score: 7,
          level: "good",
          description: "GPA above average and showing improvement",
        },
        {
          name: "Course Load Management",
          score: 6,
          level: "average",
          description: "Managing current course load adequately",
        },
        {
          name: "Attendance & Participation",
          score: 8,
          level: "excellent",
          description: "Excellent attendance and active participation",
        },
        {
          name: "Study Habits",
          score: 7,
          level: "good",
          description: "Good study habits with room for improvement",
        },
        {
          name: "Support System",
          score: 9,
          level: "excellent",
          description: "Strong family and peer support network",
        },
      ];
    },

    // Update methods for child component events
    updateTrendPeriod(period) {
      this.selectedTrendPeriod = period;
    },

    updateHistoryView(view) {
      this.selectedHistoryView = view;
    },

    updateNoteType(type) {
      this.noteForm.type = type;
    },

    updateNoteContent(content) {
      this.noteForm.content = content;
    },

    updateNoteTitle(title) {
      this.noteForm.title = title;
    },

    updateMeetingDate(date) {
      this.noteForm.meeting_date = date;
    },

    updateFollowUpRequired(checked) {
      this.noteForm.follow_up_required = checked;
    },

    // Navigation and action methods
    viewCourseDetails(courseId) {
      this.$router.push(
        `/advisor/course/${courseId}/student/${this.studentId}`
      );
    },

    scheduleMeeting() {
      this.$router.push(`/advisor/schedule-meeting/${this.studentId}`);
    },

    exportReport() {
      this.showExportReportModal = true;
    },

    sendMessage() {
      console.log("Sending message to student...");
    },

    handleAlert(alertId) {
      console.log("Handling alert:", alertId);
    },

    viewMeetingDetails(meetingId) {
      this.$router.push(`/advisor/meeting/${meetingId}`);
    },

    // Note management methods
    addNote() {
      this.editingNote = null;
      this.noteForm = {
        type: "meeting",
        title: "",
        content: "",
        meeting_date: new Date().toISOString().split("T")[0],
        follow_up_required: false,
      };
      this.showNoteModal = true;
    },

    editNote(noteId) {
      const note = this.advisorNotes.find((n) => n.id === noteId);
      if (note) {
        this.editingNote = noteId;
        this.noteForm = {
          type: note.type,
          title: note.title,
          content: note.content,
          meeting_date: note.date.toISOString().split("T")[0],
          follow_up_required: note.followUpRequired,
        };
        this.showNoteModal = true;
      }
    },

    async deleteNote(noteId) {
      if (confirm("Are you sure you want to delete this note?")) {
        try {
          const response = await advisorAPI.deleteAdviseNote(
            this.advisorId,
            this.studentId,
            noteId
          );
          if (response.success) {
            await this.loadAdvisorNotes();
            this.showNotification("Note deleted successfully", "success");
          } else {
            throw new Error(response.message || "Failed to delete note");
          }
        } catch (error) {
          console.error("Error deleting note:", error);
          this.showNotification(
            "Failed to delete note: " + error.message,
            "error"
          );
        }
      }
    },

    async saveNote() {
      if (!this.noteForm.content.trim()) return;

      try {
        const noteData = {
          note_type: this.noteForm.type,
          content: this.noteForm.content,
          title:
            this.noteForm.title ||
            `${
              this.noteForm.type.charAt(0).toUpperCase() +
              this.noteForm.type.slice(1)
            } Note`,
          meeting_date: this.noteForm.meeting_date,
          follow_up_required: this.noteForm.follow_up_required,
        };

        const response = await advisorAPI.addAdviseNote(
          this.advisorId,
          this.studentId,
          noteData
        );

        if (response.success) {
          // Reload notes to get the updated list
          await this.loadAdvisorNotes();
          this.closeNoteModal();

          // Show success message
          this.showNotification("Note saved successfully", "success");
        } else {
          throw new Error(response.message || "Failed to save note");
        }
      } catch (error) {
        console.error("Error saving note:", error);
        // Show error message
        this.showNotification("Failed to save note: " + error.message, "error");
      }
    },

    closeNoteModal() {
      this.showNoteModal = false;
      this.editingNote = null;
      this.noteForm = {
        type: "meeting",
        title: "",
        content: "",
        meeting_date: new Date().toISOString().split("T")[0],
        follow_up_required: false,
      };
    },

    hideNotification() {
      this.notification.show = false;
    },

    showNotification(message, type = "success") {
      this.notification = {
        show: true,
        message,
        type,
      };
      // Auto-hide after 5 seconds
      setTimeout(() => {
        this.hideNotification();
      }, 5000);
    },

    closeExportReportModal() {
      this.showExportReportModal = false;
    },

    handleReportGenerated(result) {
      if (result.success) {
        this.showNotification(
          `Report generated successfully! File: ${result.filename}`,
          "success"
        );
      } else {
        this.showNotification(
          `Failed to generate report: ${result.error}`,
          "error"
        );
      }
    },

    getLastMeetingDate() {
      const meetingNotes = this.advisorNotes.filter(
        (note) => note.type === "meeting"
      );
      if (meetingNotes.length === 0) return null;

      const lastMeeting = meetingNotes.reduce((latest, note) => {
        return note.date > latest.date ? note : latest;
      });

      return lastMeeting.date.toISOString().split("T")[0];
    },
  },
};
</script>

<style scoped>
.advisee-profile {
  padding: 20px;
  background-color: #f8fafc;
  min-height: 100vh;
}

/* Loading and Error States */
.loading-container,
.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  text-align: center;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #059669;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.error-message {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  max-width: 400px;
}

.error-message h3 {
  color: #dc2626;
  margin: 0 0 12px 0;
}

.error-message p {
  color: #6b7280;
  margin: 0 0 16px 0;
}

.retry-button {
  background: #059669;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.retry-button:hover {
  background: #047857;
}

/* Main Content */
.main-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 30px;
}

.left-column,
.right-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .main-content {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .advisee-profile {
    padding: 16px;
  }
}
</style>
