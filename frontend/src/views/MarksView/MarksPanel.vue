<template>
  <div class="marks-panel">
    <h2>📝 Add Marks</h2>

    <!-- Success/Error Message -->
    <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>

    <!-- Marks Form -->
    <div class="form-container">
      <h3>➕ Submit Marks</h3>
      <form @submit.prevent="handleSubmit">
        <select v-model="form.student_id" required>
          <option value="">Select Student</option>
          <option v-for="student in students" :key="student.id" :value="student.id">
            {{ student.full_name }}
          </option>
        </select>
        <select v-model="form.assessment_component_id" required>
          <option value="">Select Assessment Component</option>
          <option v-for="component in components" :key="component.id" :value="component.id">
            {{ component.component_name }} ({{ component.course_code }})
          </option>
        </select>
        <input
          v-model.number="form.marks_obtained"
          type="number"
          step="0.01"
          min="0"
          max="100"
          placeholder="Marks (0-100)"
          required
        />
        <textarea v-model="form.remarks" placeholder="Remarks (optional)"></textarea>
        <label class="checkbox">
          <input type="checkbox" v-model="form.is_final" />
          Final Marks
        </label>
        <div class="btn-group">
          <button type="submit" class="primary" :disabled="submitting">
            {{ submitting ? 'Submitting...' : 'Submit Marks' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:8080';

const students = ref([]);
const components = ref([]);
const submitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const form = ref({
  student_id: '',
  assessment_component_id: '',
  marks_obtained: null,
  remarks: '',
  is_final: false,
});

const fetchData = async () => {
  try {
    const res = await axios.get('/students-components');
    console.log('Fetched data:', res.data);
    students.value = res.data.students || [];
    components.value = res.data.components || [];
    if (!students.value.length || !components.value.length) {
      errorMessage.value = '⚠️ No students or components found.';
      setTimeout(() => (errorMessage.value = ''), 3000);
    }
  } catch (err) {
    console.error('Fetch Error:', err.response?.data, err.response?.status, err.message);
    errorMessage.value = `❌ Failed to load data: ${err.response?.data?.error || err.message} (Status: ${err.response?.status})`;
    setTimeout(() => (errorMessage.value = ''), 3000);
  }
};

const handleSubmit = async () => {
  try {
    submitting.value = true;
    console.log('Submitting:', form.value);
    const res = await axios.post('/marks', form.value, {
      headers: { 'Content-Type': 'application/json' },
    });
    console.log('Response:', res.data);
    successMessage.value = '✅ Marks submitted successfully.';
    resetForm();
    setTimeout(() => (successMessage.value = ''), 3000);
  } catch (err) {
    console.error('Submit Error:', err.response?.data, err.response?.status, err.message);
    errorMessage.value = `❌ Failed to submit marks: ${err.response?.data?.error || err.message} (Status: ${err.response?.status})`;
    setTimeout(() => (errorMessage.value = ''), 3000);
  } finally {
    submitting.value = false;
  }
};

const resetForm = () => {
  form.value.student_id = '';
  form.value.assessment_component_id = '';
  form.value.marks_obtained = null;
  form.value.remarks = '';
  form.value.is_final = false;
};

onMounted(fetchData);
</script>

<style scoped>
.marks-panel {
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

.form-container {
  margin-bottom: 2rem;
}

form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1rem;
  background: #f3f7fb;
  border-radius: 8px;
}

select,
input,
textarea {
  padding: 0.6rem;
  font-size: 1rem;
  border-radius: 6px;
  border: 1px solid #ccc;
}

textarea {
  resize: vertical;
  min-height: 80px;
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

.primary:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
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
