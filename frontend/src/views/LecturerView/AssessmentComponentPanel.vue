<template>
  <div class="assessment-panel">
    <h2>Assessment Components</h2>
    <button @click="openAddModal" class="primary">Add Component</button>

    <!-- Table of components -->
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Type</th>
          <th>Max Mark</th>
          <th>Weight (%)</th>
          <th>Due Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="comp in components" :key="comp.id">
          <td>{{ comp.component_name }}</td>
          <td>{{ comp.component_type }}</td>
          <td>{{ comp.max_marks }}</td>
          <td>{{ comp.weight_percentage }}</td>
          <td>{{ comp.due_date || '-' }}</td>
          <td>
            <button @click="editComponent(comp)">Edit</button>
            <button @click="deleteComponent(comp.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="total-weight">
      <strong>Total Weight: {{ totalWeight }}%</strong>
      <span v-if="totalWeight > 70" class="warning">⚠️ CA weight exceeds 70%</span>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <h3>{{ editingId ? 'Edit' : 'Add' }} Component</h3>
        <form @submit.prevent="handleSubmit">
          <input v-model="form.component_name" placeholder="Name" required />
          <select v-model="form.component_type" required>
            <option value="" disabled>Select Type</option>
            <option value="quiz">Quiz</option>
            <option value="assignment">Assignment</option>
            <option value="exercise">Exercise</option>
            <option value="lab">Lab</option>
            <option value="test">Test</option>
            <option value="final_exam">Final Exam</option>
            <option value="other">Other</option>
          </select>
          <input v-model.number="form.max_marks" type="number" placeholder="Max Mark" required />
          <input v-model.number="form.weight_percentage" type="number" placeholder="Weight (%)" required />
          <input v-model="form.due_date" type="date" placeholder="Due Date" />
          <textarea v-model="form.description" placeholder="Description"></textarea>
          <div class="btn-group">
            <button type="submit" class="primary">{{ editingId ? 'Update' : 'Add' }}</button>
            <button type="button" @click="closeModal" class="cancel">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
/* global defineProps */
import { ref, onMounted, computed } from 'vue'
import { lecturerAPI } from '@/services/api'

const props = defineProps({
  courseId: { type: [String, Number], required: true },
  lecturerId: { type: [String, Number], required: true }
})

const components = ref([])
const showModal = ref(false)
const editingId = ref(null)
const form = ref({
  component_name: '',
  component_type: '',
  max_marks: '',
  weight_percentage: '',
  due_date: '',
  description: ''
})

const totalWeight = computed(() =>
  components.value.reduce((sum, c) => sum + Number(c.weight_percentage || 0), 0)
)

const fetchComponents = async () => {
  console.log('Fetching components for', props.lecturerId, props.courseId);
  const res = await lecturerAPI.getCourseComponents(props.lecturerId, props.courseId)
  components.value = res.data?.components || []
}

const openAddModal = () => {
  editingId.value = null
  form.value = {
    component_name: '',
    component_type: '',
    max_marks: '',
    weight_percentage: '',
    due_date: '',
    description: ''
  }
  showModal.value = true
}

const editComponent = (comp) => {
  editingId.value = comp.id
  form.value = { ...comp }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const handleSubmit = async () => {
  console.log('Submitting component for', props.lecturerId, props.courseId, form.value);
  await lecturerAPI.updateCourseComponent(props.lecturerId, props.courseId, form.value)
  await fetchComponents()
  closeModal()
}

const deleteComponent = async (id) => {
  if (confirm('Delete this component?')) {
    await lecturerAPI.delete(`courses/${props.courseId}/components/${id}`)
    await fetchComponents()
  }
}

onMounted(fetchComponents)
</script>

<style scoped>
.assessment-panel {
  max-width: 850px;
  margin: 2rem auto;
  padding: 2rem;
  background: #fefefe;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}
h2 {
  text-align: center;
  color: #3c3c3c;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1.5rem;
  background: #f3f7fb;
  border-radius: 8px;
  overflow: hidden;
}
th, td {
  padding: 0.7rem 1rem;
  border-bottom: 1px solid #e5e7eb;
  text-align: left;
}
th {
  background: #e5e7eb;
  color: #333;
}
tr:last-child td {
  border-bottom: none;
}
.btn-group {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}
button.primary {
  background: #2563eb;
  color: #fff;
  border: none;
  padding: 0.5rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}
button.cancel {
  background: #eee;
  color: #333;
  border: none;
  padding: 0.5rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
}
button {
  font-size: 1rem;
}
.total-weight {
  margin-top: 1rem;
  font-weight: 600;
}
.warning {
  color: #eab308;
  margin-left: 1rem;
}
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.modal {
  background: #fff;
  padding: 2rem;
  border-radius: 10px;
  min-width: 320px;
  max-width: 95vw;
  box-shadow: 0 4px 24px rgba(0,0,0,0.15);
}
input, select, textarea {
  width: 100%;
  padding: 0.6rem;
  font-size: 1rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  margin-bottom: 0.7rem;
}
</style> 