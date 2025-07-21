<template>
  <div>
    <h2>Review Remark Request</h2>
    <div v-if="remarkRequest">
      <p><strong>Student:</strong> {{ remarkRequest.full_name }}</p>
      <p><strong>Assessment:</strong> {{ remarkRequest.component_name }}</p>
      <p><strong>Original Marks:</strong> {{ remarkRequest.original_marks }}</p>
      <p><strong>Justification:</strong> {{ remarkRequest.justification }}</p>

      <form @submit.prevent="submitReview">
        <div>
          <label>Status:</label>
          <select v-model="reviewForm.status" required>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="under_review">Under Review</option>
          </select>
        </div>
        <div>
          <label>New Marks (if approved):</label>
          <input v-model="reviewForm.new_marks" type="number" step="0.01" />
        </div>
        <div>
          <label>Review Comments:</label>
          <textarea v-model="reviewForm.review_comments" required></textarea>
        </div>
        <button type="submit">Submit Review</button>
      </form>
    </div>
    <p v-else>Loading request details...</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api';


const route = useRoute()
const requestId = route.params.id

const remarkRequest = ref(null)
const reviewForm = ref({
  status: '',
  new_marks: '',
  review_comments: '',
  reviewed_by: 1 // <-- replace with the actual lecturer ID (from auth)
})

onMounted(async () => {
  try {
    // You might have an endpoint to get one request by ID, or filter manually
    const allRequests = await api.get(`/remark-requests/course/${remarkRequest.value?.course_id || 1}`)
    remarkRequest.value = allRequests.data.find(r => r.id == requestId)
  } catch (error) {
    console.error(error)
  }
})

async function submitReview() {
  try {
    await api.post(`/remark-requests/review/${requestId}`, reviewForm.value)
    alert('Review submitted!')
  } catch (error) {
    console.error(error)
    alert('Failed to submit review.')
  }
}
</script>
