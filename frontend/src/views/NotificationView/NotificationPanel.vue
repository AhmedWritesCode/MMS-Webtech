<template>
  <div class="notification-panel">
    <h2>🔔 Notifications</h2>

    <!-- Success/Error Message -->
    <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>

    <!-- Notification List -->
    <div class="notification-list">
      <h3>📋 Recent Notifications</h3>
      <table v-if="notifications.length">
        <thead>
          <tr>
            <th>Title</th>
            <th>Message</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="notification in notifications" :key="notification.id">
            <td>{{ notification.title }}</td>
            <td>{{ notification.message }}</td>
            <td>{{ formatDate(notification.created_at) }}</td>
            <td>
              <button
                v-if="!notification.is_read"
                class="primary"
                @click="markAsRead(notification.id)"
              >
                Mark as Read
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else-if="!loading">No notifications found.</p>
      <p v-if="loading">Loading notifications...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:8080';

const notifications = ref([]);
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const userId = ref(localStorage.getItem('userId') || '5');

const fetchNotifications = async () => {
  try {
    loading.value = true;
    const res = await axios.get(`/notifications?user_id=${userId.value}`);
    console.log('Fetched notifications:', res.data);
    notifications.value = Array.isArray(res.data) ? res.data : [];
    if (!notifications.value.length) {
      errorMessage.value = '⚠️ No notifications found.';
      setTimeout(() => (errorMessage.value = ''), 3000);
    }
  } catch (err) {
    console.error('Fetch Error:', err.response?.data, err.message);
    errorMessage.value = `❌ Failed to fetch notifications: ${err.response?.data?.error || err.message}`;
    setTimeout(() => (errorMessage.value = ''), 3000);
  } finally {
    loading.value = false;
  }
};

const markAsRead = async (id) => {
  try {
    await axios.delete(`/notifications/${id}?user_id=${userId.value}`);
    successMessage.value = '✅ Notification deleted successfully.';
    await fetchNotifications();
    setTimeout(() => (successMessage.value = ''), 3000);
  } catch (err) {
    console.error('Delete Error:', err.response?.data, err.message);
    errorMessage.value = `❌ Failed to delete notification: ${err.response?.data?.error || err.message}`;
    setTimeout(() => (errorMessage.value = ''), 3000);
  }
};

const formatDate = (timestamp) => {
  return new Date(timestamp).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

onMounted(fetchNotifications);
</script>

<style scoped>
.notification-panel {
  max-width: 850px;
  margin: 2rem auto;
  padding: 2rem;
  background: #fefefe;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

h2,
h3 {
  text-align: center;
  color: #3c3c3c;
}

.notification-list {
  margin-top: 1.5rem;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  border: 1px solid #ddd;
  padding: 0.75rem;
  text-align: left;
}

button {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
}

.primary {
  background-color: #007bff;
  color: white;
}

.success-message {
  text-align: center;
  color: #28a745;
  font-weight: bold;
  margin-bottom: 1rem;
}

.error-message {
  text-align: center;
  color: #dc3545;
  font-weight: bold;
  margin-bottom: 1rem;
}
</style>
