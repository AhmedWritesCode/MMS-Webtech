<template>
  <div class="assign-lecturers">
    <h2> Assign Lecturers to Courses</h2>

    <div v-if="message" :class="['alert', messageType]">{{ message }}</div>

    <form @submit.prevent="assignLecturer" class="form">
      <!-- Course Dropdown -->
      <div class="form-group">
        <label>Select Course</label>
        <select v-model="form.course_id" required>
          <option disabled value="">-- Choose Course --</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">
            {{ course.course_code }} - {{ course.course_name }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label>Select Lecturer</label>
        <select v-model="form.lecturer_id" required>
          <option disabled value="">-- Choose Lecturer --</option>
          <option v-for="lecturer in lecturers" :key="lecturer.id" :value="lecturer.id">
            {{ lecturer.full_name || lecturer.username }}
          </option>
        </select>
      </div>

      <!-- Role Dropdown -->
      <div class="form-group">
        <label>Select Role</label>
        <select v-model="form.role" required>
          <option disabled value="">-- Select Role --</option>
          <option value="primary">Lecturer</option>
          <option value="secondary">Assistant Lecturer</option>
        </select>
      </div>

      <button type="submit" class="btn primary">Assign</button>
    </form>

    <!-- Assigned Lecturers List -->
    <div v-if="assigned && assigned.length" class="assigned-box">
      <h3>👨‍🏫 Assigned Lecturers</h3>
      <ul class="assigned-list">
        <li v-for="item in assigned" :key="item.id">
          <span><strong>{{ item.lecturer_name }}</strong> — {{ item.role }}</span>
          <button class="btn small delete" @click="confirmUnassign(item)">🗑️</button>
        </li>
      </ul>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <p>Unassign <strong>{{ selectedToDelete.lecturer_name }}</strong> from course?</p>
        <div class="modal-actions">
          <button @click="unassignLecturer" class="btn delete">Yes</button>
          <button @click="cancelUnassign" class="btn">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  data() {
    return {
      courses: [],
      lecturers: [],
      assigned: [],
      form: {
        course_id: '',
        lecturer_id: '',
        role: 'primary'
      },
      message: '',
      messageType: '',
      showModal: false,
      selectedToDelete: null
    };
  },
  methods: {
    async fetchCourses() {
      try {
        console.log('Fetching courses...');
        const response = await api.get('/courses');
        console.log('Courses API Response:', response);
        
        // Check if response.data exists and has the expected structure
        if (response && response.data) {
          // If the data is already an array, use it directly
          if (Array.isArray(response.data)) {
            this.courses = response.data;
          } 
          // If the data is wrapped in a data property (common API pattern)
          else if (response.data.data) {
            this.courses = response.data.data;
          }
          // If we have data but in an unexpected format
          else {
            console.error('Unexpected data format:', response.data);
            this.courses = [];
          }
        } else {
          console.warn('No data in response');
          this.courses = [];
        }

        if (!this.courses || this.courses.length === 0) {
          console.warn('No courses found in the response');
          this.showMessage('No courses available', 'error');
        }
      } catch (error) {
        console.error('Error fetching courses:', error);
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        });
        this.showMessage('❌ Failed to fetch courses', 'error');
      }
    },
    async fetchLecturers() {
      try {
        console.log('Fetching lecturers...');
        const response = await api.get('/users', {
          params: { type: 'lecturer' }
        });
        console.log('Lecturers API Response:', response);
        this.lecturers = response.data;
        if (!this.lecturers || this.lecturers.length === 0) {
          console.warn('No lecturers found in the response');
        }
      } catch (error) {
        console.error('Error fetching lecturers:', error);
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        });
        this.showMessage('❌ Failed to fetch lecturers', 'error');
      }
    },
    async fetchAssigned() {
      if (this.form.course_id) {
        try {
          console.log('Fetching assigned lecturers for course:', this.form.course_id);
          const response = await api.get(`/course-lecturers/${this.form.course_id}`);
          console.log('Assigned lecturers response:', response);
          this.assigned = response.data;
          if (!this.assigned || this.assigned.length === 0) {
            console.warn('No assigned lecturers found for this course');
          }
        } catch (error) {
          console.error('Error fetching assigned lecturers:', error);
          console.error('Error details:', {
            message: error.message,
            response: error.response?.data,
            status: error.response?.status
          });
          this.showMessage('❌ Failed to fetch assigned lecturers', 'error');
        }
      } else {
        this.assigned = [];
      }
    },
    async assignLecturer() {
      try {
        console.log('Assigning lecturer with data:', this.form); // Debug log
        const response = await api.post('/course-lecturers', this.form);
        console.log('Assign response:', response.data);
        
        if (response.data.status === 'assigned') {
          this.showMessage('✅ Lecturer assigned successfully!', 'success');
          this.fetchAssigned();
          this.form.lecturer_id = '';
          this.form.role = 'primary';
        } else {
          this.showMessage('❌ Failed: Unexpected response', 'error');
        }
      } catch (error) {
        console.error('Error assigning lecturer:', error);
        this.showMessage('❌ Failed to assign lecturer', 'error');
      }
    },
    confirmUnassign(item) {
      this.selectedToDelete = item;
      this.showModal = true;
    },
    cancelUnassign() {
      this.selectedToDelete = null;
      this.showModal = false;
    },
    async unassignLecturer() {
      try {
        await api.delete(`/course-lecturers/${this.form.course_id}/${this.selectedToDelete.lecturer_id}`);
        this.fetchAssigned();
        this.showMessage('Lecturer unassigned successfully', 'success');
      } catch (error) {
        console.error('Error unassigning lecturer:', error);
        this.showMessage('❌ Failed to unassign lecturer', 'error');
      } finally {
        this.cancelUnassign();
      }
    },
    showMessage(msg, type) {
      this.message = msg;
      this.messageType = type;
      setTimeout(() => {
        this.message = '';
        this.messageType = '';
      }, 3000);
    }
  },
  watch: {
    'form.course_id': 'fetchAssigned'
  },
  async mounted() {
    await this.fetchCourses();
    await this.fetchLecturers();
  }
};
</script>

<style scoped>
.assign-lecturers {
  max-width: 700px;
  margin: auto;
  padding: 2rem;
  background: #f9f9f9;
  color: #333;
  border-radius: 12px;
  font-family: "Segoe UI", sans-serif;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

h2, h3 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #2c3e50;
}

.alert.success {
  background: #e0f7e9;
  color: #2e7d32;
  padding: 0.75rem;
  margin-bottom: 1rem;
  border-radius: 6px;
}
.alert.error {
  background: #fdecea;
  color: #c62828;
  padding: 0.75rem;
  margin-bottom: 1rem;
  border-radius: 6px;
}

.form-group {
  margin-bottom: 1.2rem;
}
label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
}
input, select {
  width: 100%;
  padding: 0.55rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  background: #fff;
  color: #333;
  font-size: 0.95rem;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  margin-right: 0.5rem;
  border-radius: 6px;
  cursor: pointer;
  background: #ddd;
  color: #000;
  transition: background 0.3s ease;
}
.btn.primary {
  background: #007bff;
  color: #fff;
}
.btn.primary:hover {
  background: #0056b3;
}
.btn.delete {
  background: #e53935;
  color: #fff;
}
.btn.delete:hover {
  background: #b71c1c;
}
.btn.small {
  font-size: 0.8rem;
  padding: 0.3rem 0.6rem;
}

.assigned-box {
  margin-top: 2rem;
}
.assigned-list {
  list-style: none;
  padding: 0;
}
.assigned-list li {
  background: #f1f1f1;
  padding: 0.75rem;
  margin-bottom: 0.5rem;
  border-radius: 6px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #333;
}

.modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex; justify-content: center; align-items: center;
  z-index: 9999;
}
.modal {
  background: #fff;
  padding: 2rem;
  border-radius: 10px;
  color: #000;
  width: 90%;
  max-width: 400px;
  text-align: center;
}
.modal-actions {
  margin-top: 1rem;
  display: flex;
  justify-content: space-evenly;
}
</style>
