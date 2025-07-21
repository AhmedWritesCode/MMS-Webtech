<template>
  <div class="user-management">
    <h2>👥 Manage User Accounts</h2>

    <!-- Alert Message -->
    <div v-if="message" :class="['alert', messageType]">{{ message }}</div>

    <!-- Add / Edit Form -->
    <form @submit.prevent="submitForm" class="form-card">
      <div class="form-row">
        <div class="form-group">
          <label>Username</label>
          <input v-model="form.username" required />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" required />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Role</label>
          <select v-model="form.user_type" required>
            <option value="admin">Admin</option>
            <option value="lecturer">Lecturer</option>
            <option value="student">Student</option>
          </select>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select v-model="form.is_active" required>
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn primary">
          {{ editId ? "Update" : "Add" }} User
        </button>
        <button
          type="button"
          class="btn secondary"
          v-if="editId"
          @click="resetForm"
        >
          Cancel
        </button>
      </div>
    </form>

    <!-- User List -->
    <ul class="user-list">
      <li v-for="user in users" :key="user.id" class="user-card">
        <div class="user-details">
          <div class="user-name">{{ user.username }}</div>
          <div class="user-email">{{ user.email }}</div>
          <div class="user-tags">
            <span class="tag role">{{ user.user_type }}</span>
            <span :class="['tag', user.is_active ? 'active' : 'inactive']">
              {{ user.is_active ? "Active" : "Inactive" }}
            </span>
          </div>
        </div>
        <div class="user-actions">
          <button @click="editUser(user)" class="btn small edit">✏</button>
          <button @click="confirmDelete(user)" class="btn small delete">
            🗑
          </button>
        </div>
      </li>
    </ul>

    <!-- Delete Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <h3>⚠ Confirm Deletion</h3>
        <p>
          Are you sure you want to delete
          <strong>{{ toDelete.username }}</strong>?
        </p>
        <div class="modal-actions">
          <button @click="deleteUserConfirmed" class="btn delete">
            Yes, Delete
          </button>
          <button @click="cancelDelete" class="btn secondary">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/services/api";

export default {
  data() {
    return {
      users: [],
      form: {
        username: "",
        email: "",
        user_type: "student",
        is_active: true,
      },
      editId: null,
      message: "",
      messageType: "",
      showModal: false,
      toDelete: null,
    };
  },
  methods: {
    fetchUsers() {
      api
        .get("/users")
        .then((res) => (this.users = res.data))
        .catch((err) => this.showMessage(this.errToText(err), "error"));
    },
    submitForm() {
      const isUpdate = !!this.editId;
      const url = isUpdate ? `/users/${this.editId}` : "/users";
      const method = isUpdate ? api.put : api.post;

      method(url, this.form)
        .then(() => {
          this.fetchUsers();
          this.resetForm();
          this.showMessage(
            `User ${isUpdate ? "updated" : "added"}!`,
            "success"
          );
        })
        .catch((err) => this.showMessage(this.errToText(err), "error"));
    },
    editUser(user) {
      this.form = { ...user };
      this.editId = user.id;
    },
    confirmDelete(user) {
      this.toDelete = user;
      this.showModal = true;
    },
    cancelDelete() {
      this.toDelete = null;
      this.showModal = false;
    },
    deleteUserConfirmed() {
      api
        .delete(`/users/${this.toDelete.id}`)
        .then(() => {
          this.fetchUsers();
          this.showMessage("User deleted.", "success");
        })
        .catch((err) => this.showMessage(this.errToText(err), "error"))
        .finally(() => this.cancelDelete());
    },
    resetForm() {
      this.form = {
        username: "",
        email: "",
        user_type: "student",
        is_active: true,
      };
      this.editId = null;
    },
    showMessage(msg, type) {
      this.message = msg;
      this.messageType = type;
      setTimeout(() => {
        this.message = "";
        this.messageType = "";
      }, 3000);
    },
    errToText(err) {
      return err.response
        ? `Server error ${err.response.status}: ${
            err.response.data || err.message
          }`
        : `Action Failed: ${err.message}`;
    },
  },
  mounted() {
    this.fetchUsers();
  },
};
</script>

<style scoped>
.user-management {
  max-width: 900px;
  margin: 0 auto;
  padding: 2rem;
  font-family: "Segoe UI", sans-serif;
}

h2 {
  font-size: 1.8rem;
  margin-bottom: 1rem;
  color: #333;
}

.alert {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 5px;
  font-weight: 600;
  text-align: center;
}
.alert.success {
  background: #e6fffa;
  color: #2c7a7b;
  border: 1px solid #b2f5ea;
}
.alert.error {
  background: #fff5f5;
  color: #c53030;
  border: 1px solid #feb2b2;
}

.form-card {
  background: #fff;
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  margin-bottom: 2rem;
}
.form-row {
  display: flex;
  gap: 1rem;
}
.form-group {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.form-group label {
  font-weight: 600;
  margin-bottom: 0.5rem;
}
input,
select {
  padding: 0.6rem;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
}
.form-actions {
  margin-top: 1rem;
}
.btn {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  margin-right: 0.5rem;
  transition: background 0.2s ease;
}
.primary {
  background: #4f46e5;
  color: #fff;
}
.primary:hover {
  background: #4338ca;
}
.secondary {
  background: #e5e7eb;
  color: #374151;
}
.secondary:hover {
  background: #d1d5db;
}
.small {
  padding: 0.4rem 0.7rem;
  font-size: 0.9rem;
}
.edit {
  background: #facc15;
  color: #1f2937;
}
.edit:hover {
  background: #eab308;
}
.delete {
  background: #f87171;
  color: #fff;
}
.delete:hover {
  background: #ef4444;
}

.user-list {
  list-style: none;
  padding: 0;
}
.user-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9fafb;
  padding: 1rem;
  border-radius: 10px;
  margin-bottom: 1rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
.user-details {
  display: flex;
  flex-direction: column;
}
.user-name {
  font-weight: bold;
  font-size: 1.1rem;
}
.user-email {
  font-size: 0.95rem;
  color: #6b7280;
}
.user-tags {
  margin-top: 0.5rem;
}
.tag {
  display: inline-block;
  background: #e5e7eb;
  color: #374151;
  padding: 0.2rem 0.6rem;
  border-radius: 5px;
  font-size: 0.8rem;
  margin-right: 0.5rem;
}
.tag.role {
  background: #c7d2fe;
  color: #3730a3;
}
.tag.active {
  background: #d1fae5;
  color: #065f46;
}
.tag.inactive {
  background: #fee2e2;
  color: #991b1b;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal {
  background: #fff;
  padding: 2rem;
  border-radius: 12px;
  width: 100%;
  max-width: 400px;
  text-align: center;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
.modal h3 {
  margin-bottom: 1rem;
  font-size: 1.2rem;
}
.modal-actions {
  margin-top: 1.5rem;
}
</style> 