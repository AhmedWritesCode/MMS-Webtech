<template>
  <div class="submit-container">
    <h2>Submit Remark Request</h2>

    <form @submit.prevent="submitRequest">
      <label for="assessment">Assessment Component:</label>
      <select
        v-model="formData.assessment_component_id"
        @change="updateOriginalMarks"
        required
      >
        <option value="" disabled>Select Component</option>
        <option
          v-for="component in components"
          :key="component.id"
          :value="component.id"
        >
          {{ component.component_name }} ({{ component.marks_obtained }})
        </option>
      </select>

      <label for="original_marks">Original Marks:</label>
      <input
        v-model.number="formData.original_marks"
        type="number"
        readonly
        step="0.01"
      />

      <label for="justification">Justification:</label>
      <textarea
        v-model="formData.justification"
        required
        placeholder="Explain why you think your marks should be reviewed"
      ></textarea>

      <button type="submit">Submit Request</button>
    </form>

    <p v-if="message" class="message">{{ message }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api' // Make sure you have api.js configured

// Replace this with auth if available
const studentId = localStorage.getItem('student_id') || 4

const components = ref([])
const message = ref('')
const formData = ref({
  student_id: studentId,
  assessment_component_id: '',
  original_marks: null,
  justification: ''
})

const fetchComponents = async () => {
  try {
    const res = await api.get(`/api/student/${studentId}/assessments`)
    components.value = res.data
  } catch (error) {
    console.error('Failed to fetch components:', error)
  }
}

const updateOriginalMarks = () => {
  const selected = components.value.find(
    c => c.id === formData.value.assessment_component_id
  )
  if (selected) {
    formData.value.original_marks = selected.marks_obtained
  } else {
    formData.value.original_marks = null
  }
}

const submitRequest = async () => {
  try {
    const res = await api.post('/api/remark-requests/submit', formData.value)
    message.value = `Remark request submitted successfully (ID: ${res.data.id})`

    // Reset form except student_id
    formData.value.assessment_component_id = ''
    formData.value.original_marks = null
    formData.value.justification = ''
  } catch (error) {
    console.error('Submission failed:', error)
    message.value = 'Failed to submit request'
  }
}

onMounted(fetchComponents)
</script>

<style scoped>
.submit-container {
  padding: 20px;
  font-family: Arial, sans-serif;
  max-width: 600px;
  margin: auto;
}
h2 {
  font-size: 24px;
  margin-bottom: 20px;
}
label {
  display: block;
  margin: 10px 0 5px;
}
select,
input,
textarea,
button {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
textarea {
  height: 100px;
}
button {
  background-color: #0066cc;
  color: white;
  border: none;
  font-weight: bold;
  cursor: pointer;
}
button:hover {
  background-color: #004c99;
}
.message {
  margin-top: 10px;
  color: green;
}
</style>
