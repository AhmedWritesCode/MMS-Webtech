<template>
  <div class="system-logs">
    <h2>📋 System Logs</h2>

    <!-- Filters -->
    <div class="filters">
      <div class="form-group">
        <label>Action</label>
        <select v-model="filters.action">
          <option value="">All Actions</option>
          <option value="CREATE_MARK">Create Mark</option>
          <option value="UPDATE_MARK">Update Mark</option>
          <option value="DELETE_MARK">Delete Mark</option>
          <option value="CREATE_USER">Create User</option>
          <option value="UPDATE_USER">Update User</option>
          <option value="CREATE_COURSE">Create Course</option>
          <option value="UPDATE_COURSE">Update Course</option>
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
      <div class="button-container">
        <button class="pretty-btn" @click="fetchLogs">Apply Filters</button>
      </div>
    </div>

    <!-- Logs Table -->
    <div v-if="message" :class="['alert', messageType]">{{ message }}</div>
    <table v-if="logs.length" class="logs-table">
      <thead>
        <tr>
          <th>Timestamp</th>
          <th>User</th>
          <th>Action</th>
          <th>Table</th>
          <th>Record ID</th>
          <th>Old Values</th>
          <th>New Values</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.id">
          <td>{{ formatDate(log.created_at) }}</td>
          <td>{{ log.user_name || 'N/A' }}</td>
          <td>{{ log.action }}</td>
          <td>{{ log.table_name }}</td>
          <td>{{ log.record_id }}</td>
          <td>{{ formatJson(log.old_values) }}</td>
          <td>{{ formatJson(log.new_values) }}</td>
          <td>{{ log.ip_address || 'N/A' }}</td>
        </tr>
      </tbody>
    </table>
    <p v-else>No logs found.</p>

    <!-- Pagination -->
    <div class="pagination" v-if="pagination.total_pages > 1">
      <button
        :disabled="pagination.page === 1"
        @click="fetchLogs(pagination.page - 1)"
      >
        Previous
      </button>
      <span>Page {{ pagination.page }} of {{ pagination.total_pages }}</span>
      <button
        :disabled="pagination.page === pagination.total_pages"
        @click="fetchLogs(pagination.page + 1)"
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
      logs: [],
      filters: {
        action: '',
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
    async fetchLogs(page = 1) {
      try {
        const params = {
          page,
          per_page: this.pagination.per_page,
          ...this.filters,
        };
        const response = await api.get('/admin/systemlogs', { params });
        this.logs = response.data.data;
        this.pagination = response.data.pagination;
        if (!this.logs.length) {
          this.showMessage('No logs found for the selected filters.', 'info');
        }
      } catch (error) {
        console.error('Failed to fetch system logs:', error);
        this.showMessage('❌ Failed to fetch logs.', 'error');
      }
    },
    formatDate(date) {
      return moment(date).format('YYYY-MM-DD HH:mm:ss');
    },
    formatJson(json) {
      if (!json) return 'N/A';
      try {
        return JSON.stringify(JSON.parse(json), null, 2);
      } catch {
        return 'N/A';
      }
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
    this.fetchLogs();
  },
};
</script>

<style scoped>
.system-logs {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.filters {
  display: flex;
  align-items: center; /* Keep all items in the same line */
  gap: 20px;
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
  border-radius: 4px; 
  cursor: pointer;
  font-weight: bold;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.pretty-btn:hover {
  background: linear-gradient(45deg, #0056b3, #0099cc);
  transform: scale(1.05);
}

.logs-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.logs-table th,
.logs-table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}

.logs-table th {
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
