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
        <label class="checkbox">
          <input type="checkbox" v-model="form.is_active" />
          Active
        </label>
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
            <td>
              <button class="edit" @click="editStudent(student)">Edit</button>
              <button class="delete" @click="deleteStudent(student.id)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
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

form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1rem;
  background: #f3f7fb;
  border-radius: 8px;
}

input {
  padding: 0.6rem;
  font-size: 1rem;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-group {
  display: flex;
  gap: 0.5rem;
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

.cancel {
  background-color: #6c757d;
  color: white;
}

.edit {
  background-color: #ffc107;
  color: black;
}

.delete {
  background-color: #dc3545;
  color: white;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1.5rem;
}

th,
td {
  border: 1px solid #ddd;
  padding: 0.75rem;
  text-align: left;
}

.active {
  color: green;
  font-weight: bold;
}

.inactive {
  color: red;
  font-weight: bold;
}

.success-message {
  text-align: center;
  color: #28a745;
  font-weight: bold;
  margin-bottom: 1rem;
}
</style>
