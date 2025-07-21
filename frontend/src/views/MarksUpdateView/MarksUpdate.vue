<template>
  <div class="mark-updates">
    <h2>📝 Student Mark Updates</h2>

    <!-- Filters -->
    <div class="filters">
      <div class="form-group">
        <label>Course</label>
        <select v-model="filters.course_id">
          <option value="">All Courses</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">
            {{ course.course_code }} - {{ course.course_name }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label>Start Date</label>
        <input type="date" v-model="filters.start_date" />
      </div>

      <div class="form-group">
        <label>End Date</label>
        <input type="date" v-model="filters.end_date" />
      </div>

      <button class="btn pretty-btn" @click="fetchMarks">🎯 Apply Filters</button>
    </div>

    <!-- Marks Table -->
    <div v-if="message" :class="['alert', messageType]">{{ message }}</div>
    <table v-if="marks.length" class="marks-table">
      <thead>
        <tr>
          <th>Timestamp</th>
          <th>Student</th>
          <th>Course</th>
          <th>Assessment</th>
          <th>Marks Obtained</th>
          <th>Remarks</th>
          <th>Graded By</th>
          <th>Finalized</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="mark in marks" :key="mark.id">
          <td>{{ formatDate(mark.graded_at || mark.created_at) }}</td>
          <td>{{ mark.student_name || 'N/A' }}</td>
          <td>{{ mark.course_code || 'N/A' }}</td>
          <td>{{ mark.component_name || 'N/A' }}</td>
          <td>{{ mark.marks_obtained !== null ? mark.marks_obtained : 'Not Graded' }}</td>
          <td>{{ mark.remarks || 'N/A' }}</td>
          <td>{{ mark.grader_name || 'N/A' }}</td>
          <td>{{ mark.is_final ? 'Yes' : 'No' }}</td>
        </tr>
      </tbody>
    </table>
    <p v-else>No mark updates found.</p>

    <!-- Pagination -->
    <div class="pagination" v-if="pagination.total_pages > 1">
      <button
        :disabled="pagination.page === 1"
        @click="fetchMarks(pagination.page - 1)"
      >
        Previous
      </button>
      <span>Page {{ pagination.page }} of {{ pagination.total_pages }}</span>
      <button
        :disabled="pagination.page === pagination.total_pages"
        @click="fetchMarks(pagination.page + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';
import moment from 'moment';

export default {
  data() {
    return {
      marks: [],
      courses: [],
      filters: {
        course_id: '',
        start_date: '',
        end_date: '',
      },
      pagination: {
        page: 1,
        per_page: 10,
        total: 0,
        total_pages: 0,
      },
      message: '',
      messageType: '',
    };
  },
  methods: {
    async fetchMarks(page = 1) {
      try {
        const params = {
          page,
          per_page: this.pagination.per_page,
          ...this.filters,
        };
        const response = await api.get('/admin/mark-updates', { params });
        this.marks = response.data.data;
        this.pagination = response.data.pagination;
        if (!this.marks.length) {
          this.showMessage('No mark updates found for the selected filters.', 'info');
        }
      } catch (error) {
        console.error('Failed to fetch mark updates:', error);
        this.showMessage('❌ Failed to fetch mark updates.', 'error');
      }
    },
    async fetchCourses() {
      try {
        const response = await api.get('/courses');
        this.courses = response.data;
      } catch (error) {
        console.error('Failed to fetch courses:', error);
        this.showMessage('❌ Failed to fetch courses.', 'error');
      }
    },
    formatDate(date) {
      return date ? moment(date).format('YYYY-MM-DD HH:mm:ss') : 'N/A';
    },
    showMessage(msg, type) {
      this.message = msg;
      this.messageType = type;
      setTimeout(() => {
        this.message = '';
        this.messageType = '';
      }, 3000);
    },
  },
  mounted() {
    this.fetchCourses();
    this.fetchMarks();
  },
};
</script>

<style scoped>
.mark-updates {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.filters {
  display: flex;
  align-items: center; /* Aligns all items on the same line vertically */
  gap: 20px; /* Space between filters and button */
  flex-wrap: wrap;
  justify-content: center;
  margin-bottom: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.form-group label {
  font-weight: bold;
}

.form-group select,
.form-group input {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.pretty-btn {
  background: linear-gradient(45deg, #007bff, #00c6ff);
  color: white;
  padding: 11px 20px;
  border: none;
  border-radius: 24px0px;
  cursor: pointer;
  font-weight: bold;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.pretty-btn:hover {
  background: linear-gradient(45deg, #0056b3, #0099cc);
  transform: scale(1.05);
}

.marks-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.marks-table th,
.marks-table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}

.marks-table th {
  background: #f4f4f4;
}

.pagination {
  display: flex;
  gap: 10px;
  align-items: center;
  justify-content: center;
}

.pagination button {
  padding: 8px 16px;
  border: 1px solid #ccc;
  background: #fff;
  cursor: pointer;
}

.pagination button:disabled {
  background: #eee;
  cursor: not-allowed;
}

.alert {
  padding: 10px;
  margin-bottom: 20px;
  border-radius: 4px;
}

.alert.success {
  background: #d4edda;
  color: #155724;
}

.alert.error {
  background: #f8d7da;
  color: #721c24;
}

.alert.info {
  background: #d1ecf1;
  color: #0c5460;
}
</style>
