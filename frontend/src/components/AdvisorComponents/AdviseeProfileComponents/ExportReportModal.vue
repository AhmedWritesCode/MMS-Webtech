<template>
  <div v-if="showModal" class="modal-overlay" @click="$emit('close-modal')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>Export Consultation Report</h3>
        <button class="modal-close" @click="$emit('close-modal')">×</button>
      </div>

      <div class="modal-body">
        <div class="export-info">
          <div class="student-info">
            <h4>{{ studentName }}</h4>
            <p class="student-details">
              {{ studentMatric }} • {{ studentProgram }}
            </p>
          </div>

          <div class="report-summary">
            <div class="summary-item">
              <span class="label">Current GPA:</span>
              <span class="value">{{ currentGPA }}</span>
            </div>
            <div class="summary-item">
              <span class="label">Total Notes:</span>
              <span class="value">{{ totalNotes }}</span>
            </div>
            <div class="summary-item">
              <span class="label">Last Meeting:</span>
              <span class="value">{{ lastMeetingDate || "No meetings" }}</span>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="reportFormat">Report Format:</label>
          <select
            id="reportFormat"
            v-model="selectedFormat"
            class="form-select"
          >
            <option value="pdf">PDF Document</option>
            <option value="csv">CSV Spreadsheet</option>
            <option value="json">JSON Data</option>
          </select>
        </div>

        <div class="form-group">
          <label for="reportType">Report Type:</label>
          <select id="reportType" v-model="selectedType" class="form-select">
            <option value="comprehensive">Comprehensive Report</option>
            <option value="academic">Academic Performance Only</option>
            <option value="consultation">Consultation History Only</option>
            <option value="summary">Executive Summary</option>
          </select>
        </div>

        <div class="form-group">
          <label for="dateRange">Date Range:</label>
          <select
            id="dateRange"
            v-model="selectedDateRange"
            class="form-select"
          >
            <option value="all">All Time</option>
            <option value="current_semester">Current Semester</option>
            <option value="last_6_months">Last 6 Months</option>
            <option value="last_year">Last Year</option>
            <option value="custom">Custom Range</option>
          </select>
        </div>

        <div v-if="selectedDateRange === 'custom'" class="form-group">
          <div class="date-inputs">
            <div>
              <label for="startDate">Start Date:</label>
              <input
                id="startDate"
                type="date"
                v-model="customStartDate"
                class="form-input"
              />
            </div>
            <div>
              <label for="endDate">End Date:</label>
              <input
                id="endDate"
                type="date"
                v-model="customEndDate"
                class="form-input"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input
              type="checkbox"
              v-model="includeRecommendations"
              class="form-checkbox"
            />
            Include Recommendations
          </label>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input
              type="checkbox"
              v-model="includeCharts"
              class="form-checkbox"
            />
            Include Charts & Graphs (PDF only)
          </label>
        </div>

        <div class="form-group">
          <label for="reportTitle">Report Title (Optional):</label>
          <input
            id="reportTitle"
            v-model="reportTitle"
            class="form-input"
            placeholder="e.g., Academic Progress Report - Fall 2024"
          />
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn secondary" @click="$emit('close-modal')">
          Cancel
        </button>
        <button
          class="btn primary"
          @click="generateReport"
          :disabled="isGenerating"
        >
          <span v-if="isGenerating" class="loading-spinner-small"></span>
          {{ isGenerating ? "Generating..." : "Generate Report" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { advisorAPI } from "@/services/api";

export default {
  name: "ExportReportModal",
  props: {
    showModal: {
      type: Boolean,
      required: true,
      default: false,
    },
    studentName: {
      type: String,
      required: true,
    },
    studentMatric: {
      type: String,
      required: true,
    },
    studentProgram: {
      type: String,
      required: true,
    },
    currentGPA: {
      type: Number,
      required: true,
    },
    totalNotes: {
      type: Number,
      required: true,
    },
    lastMeetingDate: {
      type: String,
      default: null,
    },
    advisorId: {
      type: Number,
      required: true,
    },
    studentId: {
      type: Number,
      required: true,
    },
  },
  emits: ["close-modal", "report-generated"],
  data() {
    return {
      selectedFormat: "pdf",
      selectedType: "comprehensive",
      selectedDateRange: "all",
      customStartDate: "",
      customEndDate: "",
      includeRecommendations: true,
      includeCharts: true,
      reportTitle: "",
      isGenerating: false,
    };
  },
  methods: {
    async generateReport() {
      try {
        this.isGenerating = true;

        // Prepare export parameters
        const params = {
          format: this.selectedFormat,
          type: this.selectedType,
          date_range: this.selectedDateRange,
          include_recommendations: this.includeRecommendations,
          include_charts: this.includeCharts,
          title:
            this.reportTitle || `Consultation Report - ${this.studentName}`,
        };

        // Add custom date range if selected
        if (this.selectedDateRange === "custom") {
          if (this.customStartDate) params.start_date = this.customStartDate;
          if (this.customEndDate) params.end_date = this.customEndDate;
        }

        // Call the export API
        const response = await advisorAPI.exportConsultationReport(
          this.advisorId,
          this.studentId,
          params
        );

        if (response.success) {
          // Handle different response types
          if (this.selectedFormat === "pdf" || this.selectedFormat === "csv") {
            // Download file
            this.downloadFile(response.data, response.filename);
          } else {
            // For JSON, show data in console or download as file
            this.downloadJSON(response.data, response.filename);
          }

          this.$emit("report-generated", {
            success: true,
            format: this.selectedFormat,
            filename: response.filename,
          });

          this.$emit("close-modal");
        } else {
          throw new Error(response.message || "Failed to generate report");
        }
      } catch (error) {
        console.error("Error generating report:", error);
        this.$emit("report-generated", {
          success: false,
          error: error.message,
        });
      } finally {
        this.isGenerating = false;
      }
    },

    downloadFile(data, filename) {
      const blob = new Blob([data], {
        type: this.selectedFormat === "pdf" ? "application/pdf" : "text/csv",
      });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);
    },

    downloadJSON(data, filename) {
      const jsonString = JSON.stringify(data, null, 2);
      const blob = new Blob([jsonString], { type: "application/json" });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.download = filename || "consultation_report.json";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);
    },
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
  box-sizing: border-box;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 600px;
  max-height: calc(100vh - 40px);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 12px 12px 0 0;
}

.modal-header h3 {
  margin: 0;
  font-weight: 700;
  color: #1e293b;
  font-size: 1.25rem;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #64748b;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  color: #1e293b;
  background: #e2e8f0;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
}

.export-info {
  background: #f8fafc;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 24px;
  border: 1px solid #e2e8f0;
}

.student-info h4 {
  margin: 0 0 4px 0;
  color: #1e293b;
  font-size: 1.1rem;
}

.student-details {
  margin: 0;
  color: #64748b;
  font-size: 0.9rem;
}

.report-summary {
  margin-top: 12px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
}

.summary-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.summary-item .label {
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 500;
}

.summary-item .value {
  font-size: 0.9rem;
  color: #1e293b;
  font-weight: 600;
}

.form-group {
  margin-bottom: 20px;
}

.form-group:last-child {
  margin-bottom: 0;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9rem;
}

.form-select,
.form-input {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  font-family: inherit;
  box-sizing: border-box;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-select:focus,
.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input {
  height: 40px;
}

.date-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-weight: 500;
  color: #1e293b;
  padding: 8px 0;
}

.form-checkbox {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #3b82f6;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 0 0 12px 12px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
  min-width: 80px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn.primary {
  background: #3b82f6;
  color: white;
}

.btn.primary:hover:not(:disabled) {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn.secondary {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.btn.secondary:hover {
  background: #e2e8f0;
  transform: translateY(-1px);
}

.loading-spinner-small {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
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

/* Responsive Design */
@media (max-width: 768px) {
  .modal-overlay {
    padding: 10px;
  }

  .modal-content {
    max-width: 100%;
    max-height: calc(100vh - 20px);
  }

  .modal-header,
  .modal-body,
  .modal-footer {
    padding: 16px 20px;
  }

  .date-inputs {
    grid-template-columns: 1fr;
  }

  .report-summary {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .modal-footer {
    flex-direction: column;
  }

  .btn {
    width: 100%;
  }
}
</style>
