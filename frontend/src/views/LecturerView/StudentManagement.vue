<template>
  <div class="lecturer-panel">
    <h2>🎓 Student Management Panel</h2>

    <!-- ✅ Success Message -->
    <p v-if="successMessage" class="success-message">{{ successMessage }}</p>

    <!-- Add/Edit Student Form -->
    <div class="form-container">
      <h3>{{ editingId ? '✏️ Edit Student' : '➕ Add New Student' }}</h3>
      <form @submit.prevent="handleSubmit">
        <input v-model="form.username" placeholder="Username" required />
        <input v-model="form.email" type="email" placeholder="Email" required />
        <input v-model="form.full_name" placeholder="Full Name" />
        <input
          v-model="form.password"
          type="password"
          placeholder="Password"
          :required="!editingId"
        />
        <div class="checkbox-container">
          <label class="checkbox">
            <input type="checkbox" v-model="form.is_active" />
            <span>Active</span>
          </label>
        </div>
        <div class="btn-group">
          <button type="submit" class="primary">
            {{ editingId ? 'Update' : 'Add' }}
          </button>
          <button v-if="editingId" type="button" @click="cancelEdit" class="cancel">
            Cancel
          </button>
        </div>
      </form>
    </div>

    <!-- Student List -->
    <div class="student-list">
      <h3>📋 Student Records</h3>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Username</th>
              <th>Email</th>
              <th>Full Name</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="student in students" :key="student.id">
              <td>{{ student.username }}</td>
              <td>{{ student.email }}</td>
              <td>{{ student.full_name }}</td>
              <td :class="{ active: student.is_active, inactive: !student.is_active }">
                {{ student.is_active ? 'Active' : 'Inactive' }}
              </td>
              <td class="action-buttons">
                <button class="edit" @click="editStudent(student)">Edit</button>
                <button class="delete" @click="deleteStudent(student.id)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const students = ref([])
const editingId = ref(null)
const successMessage = ref('')

const form = ref({
  username: '',
  email: '',
  full_name: '',
  password: '',
  is_active: true,
})

const fetchStudents = async () => {
  try {
    const res = await api.get('students')
    students.value = res.data
  } catch (err) {
    console.error('Fetch Error:', err.message)
  }
}

const handleSubmit = async () => {
  try {
    if (editingId.value) {
      await api.put(`students/${editingId.value}`, form.value, {
        headers: { 'Content-Type': 'application/json' },
      })
      successMessage.value = '✅ Student updated successfully.'
    } else {
      await api.post('students', form.value, {
        headers: { 'Content-Type': 'application/json' },
      })
      successMessage.value = '✅ Student added successfully.'
    }

    resetForm()
    await fetchStudents()

    // Auto-clear success message
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (err) {
    console.error('Submit Error:', err.message)
  }
}

const editStudent = (student) => {
  editingId.value = student.id
  form.value = {
    username: student.username,
    email: student.email,
    full_name: student.full_name,
    password: '',
    is_active: !!student.is_active,
  }
}

const deleteStudent = async (id) => {
  if (confirm('Are you sure you want to delete this student?')) {
    try {
      await api.delete(`students/${id}`)
      await fetchStudents()
    } catch (err) {
      console.error('Delete Error:', err.message)
    }
  }
}

const cancelEdit = () => resetForm()

const resetForm = () => {
  editingId.value = null
  form.value = {
    username: '',
    email: '',
    full_name: '',
    password: '',
    is_active: true,
  }
}

onMounted(fetchStudents)
</script>

<style scoped>
.lecturer-panel {
  max-width: 1100px;
  margin: 2rem auto;
  padding: 2.5rem;
  background: #fff;
  border-radius: 1.5rem;
  box-shadow: 0 6px 24px 0 rgba(124, 152, 133, 0.18);
  border: 2px solid #7C9885;
}

h2,
h3 {
  text-align: center;
  color: #7C9885;
  margin-bottom: 1.5rem;
}

.form-container {
  margin-bottom: 2rem;
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(124, 152, 133, 0.1);
  border: 1px solid #B5B682;
}

form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1rem;
  background: rgba(181, 182, 130, 0.1);
  border-radius: 0.8rem;
}

input {
  padding: 0.8rem;
  font-size: 1rem;
  border-radius: 0.5rem;
  border: 1px solid #B5B682;
  background-color: #fff;
  transition: border-color 0.2s;
}

input:focus {
  outline: none;
  border-color: #7C9885;
  box-shadow: 0 0 0 2px rgba(124, 152, 133, 0.2);
}

.checkbox-container {
  display: flex;
  align-items: center;
}

.checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox input {
  cursor: pointer;
}

.btn-group {
  display: flex;
  gap: 0.8rem;
  margin-top: 0.5rem;
}

button {
  padding: 0.7rem 1.2rem;
  border: none;
  border-radius: 0.7rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.primary {
  background-color: #7C9885;
  color: white;
}

.primary:hover {
  background-color: #6a8573;
}

.cancel {
  background-color: #B5B682;
  color: #23272f;
}

.cancel:hover {
  background-color: #a3a473;
}

.student-list {
  background: #fff;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(124, 152, 133, 0.1);
  border: 1px solid #B5B682;
}

.table-container {
  overflow-x: auto;
  border-radius: 0.5rem;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
  background: #fff;
}

th {
  background-color: #7C9885;
  color: white;
  font-weight: 600;
  padding: 1rem;
  text-align: left;
}

th:first-child {
  border-top-left-radius: 0.5rem;
}

th:last-child {
  border-top-right-radius: 0.5rem;
}

td {
  padding: 0.8rem 1rem;
  border-bottom: 1px solid #B5B682;
}

tr:last-child td {
  border-bottom: none;
}

tr:hover {
  background-color: rgba(181, 182, 130, 0.1);
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.edit {
  background-color: #B5B682;
  color: #23272f;
}

.edit:hover {
  background-color: #a3a473;
}

.delete {
  background-color: #e74c3c;
  color: white;
}

.delete:hover {
  background-color: #c0392b;
}

.active {
  color: #7C9885;
  font-weight: bold;
}

.inactive {
  color: #e74c3c;
  font-weight: bold;
}

.success-message {
  text-align: center;
  background-color: rgba(124, 152, 133, 0.2);
  color: #7C9885;
  font-weight: bold;
  margin-bottom: 1.5rem;
  padding: 0.8rem;
  border-radius: 0.5rem;
  border: 1px solid #7C9885;
}
</style>
