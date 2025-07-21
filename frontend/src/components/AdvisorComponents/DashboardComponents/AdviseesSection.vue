<template>
  <div class="advisees-section">
    <div class="section-header">
      <h2>My Advisees</h2>
      <div class="section-controls">
        <div class="search-filter">
          <input
            type="text"
            :value="searchQuery"
            @input="updateSearch($event.target.value)"
            placeholder="Search students..."
            class="search-input"
          />
          <select
            :value="selectedFilter"
            @change="updateFilter($event.target.value)"
            class="filter-select"
          >
            <option value="all">All Students</option>
            <option value="excellent">Excellent (GPA ≥ 3.5)</option>
            <option value="good">Good (3.0 - 3.49)</option>
            <option value="average">Average (2.5 - 2.99)</option>
            <option value="at-risk">At Risk ( 2.5)</option>
            <option value="recent-meetings">Recent Meetings</option>
          </select>
        </div>
        <div class="view-options">
          <button
            :class="['view-btn', { active: viewMode === 'grid' }]"
            @click="updateViewMode('grid')"
          >
            📱 Grid
          </button>
          <button
            :class="['view-btn', { active: viewMode === 'list' }]"
            @click="updateViewMode('list')"
          >
            📋 List
          </button>
        </div>
      </div>
    </div>

    <!-- Grid View -->
    <div v-if="viewMode === 'grid'" class="advisees-grid">
      <div
        v-for="student in filteredAdvisees"
        :key="student.id"
        class="student-card"
        :class="getPerformanceClass(student.currentGPA)"
        @click="viewStudent(student.id)"
      >
        <div class="student-header">
          <div class="student-avatar">
            {{ getStudentInitials(student.name) }}
          </div>
          <div class="student-basic-info">
            <h4>{{ student.name }}</h4>
            <p class="student-matric">{{ student.matricNumber }}</p>
            <p class="student-program">{{ student.program }}</p>
          </div>
          <div
            class="performance-indicator"
            :class="getPerformanceClass(student.currentGPA)"
          >
            {{ student.currentGPA }}
          </div>
        </div>

        <div class="student-stats">
          <div class="stat-row">
            <span class="stat-label">Current Semester:</span>
            <span class="stat-value">{{ student.currentSemester }}</span>
          </div>
          <div class="stat-row">
            <span class="stat-label">Enrolled Courses:</span>
            <span class="stat-value">{{ student.enrolledCourses }}</span>
          </div>
          <div class="stat-row">
            <span class="stat-label">Last Meeting:</span>
            <span class="stat-value">{{ student.lastMeeting || "Never" }}</span>
          </div>
        </div>

        <div class="student-actions">
          <button
            class="action-btn primary"
            @click.stop="viewStudent(student.id)"
          >
            View Profile
          </button>
          <button
            class="action-btn secondary"
            @click.stop="scheduleStudentMeeting(student.id)"
          >
            Schedule Meeting
          </button>
          <button
            class="action-btn notes"
            @click.stop="addStudentNote(student.id)"
          >
            Add Note
          </button>
        </div>

        <!-- Quick Performance Indicators -->
        <div class="quick-indicators">
          <div v-if="isAtRisk(student)" class="indicator warning">
            ⚠️ At Risk
          </div>
          <div v-if="student.hasRecentIssues" class="indicator alert">
            🔴 Recent Issues
          </div>
          <div v-if="student.improvingTrend" class="indicator positive">
            📈 Improving
          </div>
          <div v-if="student.hasUpcomingMeeting" class="indicator meeting">
            📅 Meeting Scheduled
          </div>
        </div>
      </div>
    </div>

    <!-- List View -->
    <div v-if="viewMode === 'list'" class="advisees-list">
      <div class="list-header">
        <div class="col-header name">Student</div>
        <div class="col-header gpa">GPA</div>
        <div class="col-header semester">Semester</div>
        <div class="col-header courses">Courses</div>
        <div class="col-header meeting">Last Meeting</div>
        <div class="col-header status">Status</div>
        <div class="col-header actions">Actions</div>
      </div>

      <div
        v-for="student in filteredAdvisees"
        :key="student.id"
        class="list-row"
        :class="getPerformanceClass(student.currentGPA)"
      >
        <div class="col-data name">
          <div class="student-info">
            <div class="student-avatar small">
              {{ getStudentInitials(student.name) }}
            </div>
            <div>
              <p class="student-name">{{ student.name }}</p>
              <p class="student-matric">{{ student.matricNumber }}</p>
            </div>
          </div>
        </div>
        <div class="col-data gpa">
          <span
            class="gpa-value"
            :class="getPerformanceClass(student.currentGPA)"
          >
            {{ student.currentGPA }}
          </span>
        </div>
        <div class="col-data semester">{{ student.currentSemester }}</div>
        <div class="col-data courses">{{ student.enrolledCourses }}</div>
        <div class="col-data meeting">
          {{ student.lastMeeting || "Never" }}
        </div>
        <div class="col-data status">
          <div class="status-indicators">
            <span v-if="isAtRisk(student)" class="status-badge at-risk"
              >At Risk</span
            >
            <span
              v-else-if="isExcellent(student)"
              class="status-badge excellent"
              >Excellent</span
            >
            <span v-else-if="isGood(student)" class="status-badge good"
              >Good</span
            >
            <span v-else class="status-badge average">Average</span>
          </div>
        </div>
        <div class="col-data actions">
          <div class="action-buttons">
            <button
              class="action-btn small primary"
              @click="viewStudent(student.id)"
            >
              View
            </button>
            <button
              class="action-btn small secondary"
              @click="scheduleStudentMeeting(student.id)"
            >
              Meet
            </button>
            <button
              class="action-btn small notes"
              @click="addStudentNote(student.id)"
            >
              Note
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredAdvisees.length === 0" class="empty-state">
      <div class="empty-icon">👥</div>
      <h3>No students found</h3>
      <p>Try adjusting your search or filter criteria</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdviseesSection",
  props: {
    advisees: {
      type: Array,
      required: true,
      default: () => [],
    },
    searchQuery: {
      type: String,
      required: true,
      default: "",
    },
    selectedFilter: {
      type: String,
      required: true,
      default: "all",
    },
    viewMode: {
      type: String,
      required: true,
      default: "grid",
    },
    filteredAdvisees: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: [
    "update-search",
    "update-filter",
    "update-view-mode",
    "view-student",
    "schedule-student-meeting",
    "add-student-note",
  ],
  methods: {
    updateSearch(query) {
      this.$emit("update-search", query);
    },

    updateFilter(filter) {
      this.$emit("update-filter", filter);
    },

    updateViewMode(mode) {
      this.$emit("update-view-mode", mode);
    },

    viewStudent(studentId) {
      this.$emit("view-student", studentId);
    },

    scheduleStudentMeeting(studentId) {
      this.$emit("schedule-student-meeting", studentId);
    },

    addStudentNote(studentId) {
      this.$emit("add-student-note", studentId);
    },

    getPerformanceClass(gpa) {
      if (gpa >= 3.5) return "excellent";
      if (gpa >= 3.0) return "good";
      if (gpa >= 2.5) return "average";
      return "at-risk";
    },

    getStudentInitials(name) {
      return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
    },

    isAtRisk(student) {
      return student.currentGPA < 2.5;
    },

    isExcellent(student) {
      return student.currentGPA >= 3.5;
    },

    isGood(student) {
      return student.currentGPA >= 3.0;
    },
  },
};
</script>

<style scoped>
.advisees-section {
  margin-bottom: 30px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.section-header h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.section-controls {
  display: flex;
  gap: 16px;
  align-items: center;
  flex-wrap: wrap;
}

.search-filter {
  display: flex;
  gap: 12px;
}

.search-input {
  padding: 8px 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  min-width: 200px;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
}

.filter-select {
  padding: 8px 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  background: white;
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
}

.view-options {
  display: flex;
  gap: 4px;
}

.view-btn {
  padding: 8px 12px;
  border: 2px solid #e2e8f0;
  background: white;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.view-btn.active,
.view-btn:hover {
  border-color: #3b82f6;
  color: #3b82f6;
  background: #eff6ff;
}

/* Grid View */
.advisees-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 20px;
}

.student-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.2s;
  border-left: 4px solid #e2e8f0;
  position: relative;
}

.student-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.student-card.excellent {
  border-left-color: #059669;
}

.student-card.good {
  border-left-color: #3b82f6;
}

.student-card.average {
  border-left-color: #f59e0b;
}

.student-card.at-risk {
  border-left-color: #ef4444;
}

.student-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}

.student-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.2rem;
}

.student-basic-info {
  flex: 1;
}

.student-basic-info h4 {
  margin: 0 0 4px 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.student-matric {
  margin: 0 0 2px 0;
  font-size: 0.9rem;
  color: #64748b;
}

.student-program {
  margin: 0;
  font-size: 0.8rem;
  color: #94a3b8;
}

.performance-indicator {
  font-size: 1.2rem;
  font-weight: 700;
  padding: 8px 12px;
  border-radius: 8px;
  background: #f1f5f9;
}

.performance-indicator.excellent {
  background: #dcfce7;
  color: #059669;
}

.performance-indicator.good {
  background: #dbeafe;
  color: #3b82f6;
}

.performance-indicator.average {
  background: #fef3c7;
  color: #d97706;
}

.performance-indicator.at-risk {
  background: #fee2e2;
  color: #dc2626;
}

.student-stats {
  margin-bottom: 16px;
}

.stat-row {
  display: flex;
  justify-content: space-between;
  padding: 4px 0;
  font-size: 0.9rem;
}

.stat-label {
  color: #64748b;
}

.stat-value {
  font-weight: 600;
  color: #1e293b;
}

.student-actions {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
}

.action-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  flex: 1;
}

.action-btn.primary {
  background: #3b82f6;
  color: white;
}

.action-btn.primary:hover {
  background: #2563eb;
}

.action-btn.secondary {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.action-btn.secondary:hover {
  background: #e2e8f0;
}

.action-btn.notes {
  background: #fef3c7;
  color: #d97706;
}

.action-btn.notes:hover {
  background: #fde68a;
}

.quick-indicators {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  position: absolute;
  top: 12px;
  right: 12px;
}

.indicator {
  font-size: 0.7rem;
  padding: 2px 6px;
  border-radius: 10px;
  font-weight: 600;
}

.indicator.warning {
  background: #fee2e2;
  color: #dc2626;
}

.indicator.alert {
  background: #fef2f2;
  color: #ef4444;
}

.indicator.positive {
  background: #dcfce7;
  color: #059669;
}

.indicator.meeting {
  background: #dbeafe;
  color: #3b82f6;
}

/* List View */
.advisees-list {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.list-header {
  display: grid;
  grid-template-columns: 2fr 80px 120px 80px 120px 100px 150px;
  gap: 16px;
  padding: 16px 20px;
  background: #f8fafc;
  font-weight: 700;
  color: #475569;
  font-size: 0.9rem;
  border-bottom: 1px solid #e2e8f0;
}

.list-row {
  display: grid;
  grid-template-columns: 2fr 80px 120px 80px 120px 100px 150px;
  gap: 16px;
  padding: 16px 20px;
  border-bottom: 1px solid #f1f5f9;
  align-items: center;
  cursor: pointer;
  transition: background 0.2s;
  border-left: 4px solid transparent;
}

.list-row:hover {
  background: #f8fafc;
}

.list-row.excellent {
  border-left-color: #059669;
}

.list-row.good {
  border-left-color: #3b82f6;
}

.list-row.average {
  border-left-color: #f59e0b;
}

.list-row.at-risk {
  border-left-color: #ef4444;
}

.col-data {
  display: flex;
  align-items: center;
  font-size: 0.9rem;
}

.col-data.name {
  gap: 12px;
}

.student-avatar.small {
  width: 36px;
  height: 36px;
  font-size: 0.9rem;
}

.student-name {
  margin: 0;
  font-weight: 600;
  color: #1e293b;
}

.gpa-value {
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 6px;
  background: #f1f5f9;
}

.gpa-value.excellent {
  background: #dcfce7;
  color: #059669;
}

.gpa-value.good {
  background: #dbeafe;
  color: #3b82f6;
}

.gpa-value.average {
  background: #fef3c7;
  color: #d97706;
}

.gpa-value.at-risk {
  background: #fee2e2;
  color: #dc2626;
}

.status-badge {
  font-size: 0.7rem;
  padding: 4px 8px;
  border-radius: 12px;
  font-weight: 600;
}

.status-badge.excellent {
  background: #dcfce7;
  color: #059669;
}

.status-badge.good {
  background: #dbeafe;
  color: #3b82f6;
}

.status-badge.average {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.at-risk {
  background: #fee2e2;
  color: #dc2626;
}

.action-buttons {
  display: flex;
  gap: 4px;
}

.action-btn.small {
  padding: 4px 8px;
  font-size: 0.7rem;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #64748b;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
  opacity: 0.5;
}

.empty-state h3 {
  margin: 0 0 8px 0;
  font-size: 1.5rem;
  font-weight: 700;
}

.empty-state p {
  margin: 0;
  font-size: 1rem;
}

@media (max-width: 768px) {
  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .section-controls {
    width: 100%;
    justify-content: space-between;
  }

  .search-filter {
    flex-direction: column;
    width: 100%;
  }

  .search-input {
    min-width: auto;
    width: 100%;
  }

  .advisees-grid {
    grid-template-columns: 1fr;
  }

  .list-header,
  .list-row {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .col-header {
    display: none;
  }

  .col-data {
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
  }

  .col-data:before {
    content: attr(data-label);
    font-weight: 600;
    color: #64748b;
  }
}

@media (max-width: 480px) {
  .student-actions {
    flex-direction: column;
  }

  .action-buttons {
    flex-direction: column;
  }
}
</style>
