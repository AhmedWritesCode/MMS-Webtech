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
  color: #7C9885;
}

.alert {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 8px;
  font-weight: 600;
  text-align: center;
}
.alert.success {
  background: #e6e8d8;
  color: #7C9885;
  border: 1px solid #B5B682;
}
.alert.error {
  background: #fff5f5;
  color: #c53030;
  border: 1px solid #B5B682;
}

.form-card {
  background: #f8f8f4;
  padding: 1.5rem;
  border-radius: 20px;
  box-shadow: 0 10px 40px 0 rgba(124, 152, 133, 0.15), 0 2px 8px 0 rgba(0,0,0,0.12);
  margin-bottom: 2rem;
  border: 1.5px solid #7C9885;
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
  color: #7C9885;
}
input,
select {
  padding: 0.6rem;
  border: 2px solid #B5B682;
  border-radius: 8px;
  font-size: 1rem;
  background: #e6e8d8;
  color: #7C9885;
}
input:focus,
select:focus {
  border-color: #7C9885;
  box-shadow: 0 0 0 2px rgba(124, 152, 133, 0.15);
  outline: none;
}
.form-actions {
  margin-top: 1rem;
}
.btn {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  margin-right: 0.5rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(124, 152, 133, 0.15);
}
.primary {
  background: linear-gradient(90deg, #7C9885 60%, #B5B682 100%);
  color: #fff;
}
.primary:hover {
  background: linear-gradient(90deg, #B5B682 60%, #7C9885 100%);
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 4px 16px rgba(124, 152, 133, 0.33);
}
.secondary {
  background: #e6e8d8;
  color: #7C9885;
}
.secondary:hover {
  background: #d1d5db;
  transform: translateY(-2px);
}
.small {
  padding: 0.4rem 0.7rem;
  font-size: 0.9rem;
}
.edit {
  background: #B5B682;
  color: #fff;
}
.edit:hover {
  background: #a3a473;
  transform: translateY(-2px);
}
.delete {
  background: #f87171;
  color: #fff;
}
.delete:hover {
  background: #ef4444;
  transform: translateY(-2px);
}

.user-list {
  list-style: none;
  padding: 0;
}
.user-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8f8f4;
  padding: 1rem;
  border-radius: 20px;
  margin-bottom: 1rem;
  box-shadow: 0 10px 40px 0 rgba(124, 152, 133, 0.15), 0 2px 8px 0 rgba(0,0,0,0.12);
  border: 1.5px solid #7C9885;
}
.user-details {
  display: flex;
  flex-direction: column;
}
.user-name {
  font-weight: bold;
  font-size: 1.1rem;
  color: #7C9885;
}
.user-email {
  font-size: 0.95rem;
  color: #B5B682;
}
.user-tags {
  margin-top: 0.5rem;
}
.tag {
  display: inline-block;
  background: #e6e8d8;
  color: #7C9885;
  padding: 0.2rem 0.6rem;
  border-radius: 5px;
  font-size: 0.8rem;
  margin-right: 0.5rem;
}
.tag.role {
  background: #e6e8d8;
  color: #7C9885;
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
  background: #f8f8f4;
  padding: 2rem;
  border-radius: 20px;
  width: 100%;
  max-width: 400px;
  text-align: center;
  box-shadow: 0 10px 40px 0 rgba(124, 152, 133, 0.15), 0 2px 8px 0 rgba(0,0,0,0.12);
  border: 1.5px solid #7C9885;
}
.modal h3 {
  margin-bottom: 1rem;
  font-size: 1.2rem;
  color: #7C9885;
}
.modal-actions {
  margin-top: 1.5rem;
}
</style> 
