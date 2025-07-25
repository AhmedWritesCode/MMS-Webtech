<template>
  <div class="course-list">
    <h2> Manage Courses</h2>

    <!-- Alert Message -->
    <div v-if="message" :class="['alert', messageType]">{{ message }}</div>

    <!-- Add / Edit Form -->
    <form @submit.prevent="submitForm" class="form">
      <div class="form-group">
        <label>Course Code</label>
        <input v-model="form.course_code" required />
      </div>
      <div class="form-group">
        <label>Course Name</label>
        <input v-model="form.course_name" required />
      </div>
      <div class="form-group">
        <label>Credits</label>
        <input v-model="form.credits" type="number" required />
      </div>
      <div class="form-group">
        <label>Semester</label>
        <input v-model="form.semester" required />
      </div>
      <div class="form-group">
        <label>Academic Year</label>
        <input v-model="form.academic_year" required />
      </div>

      <button type="submit" class="btn primary">
        {{ editId ? 'Update' : 'Add' }} Course
      </button>
      <button type="button" class="btn" v-if="editId" @click="resetForm">Cancel</button>
    </form>

    <!-- Course List -->
    <ul class="course-items">
      <li v-for="course in courses" :key="course.id" class="course-card">
        <div class="course-info">
          <div class="title">
            <strong class="code">{{ course.course_code }}</strong> — <span class="name">{{ course.course_name }}</span>
          </div>
          <div class="tags">
            <span class="tag">Credits: {{ course.credits }}</span>
            <span class="tag">Semester: {{ course.semester }}</span>
            <span class="tag">Year: {{ course.academic_year }}</span>
          </div>
        </div>
        <div class="actions">
          <button @click="editCourse(course)" class="btn small edit">✏️</button>
          <button @click="confirmDelete(course)" class="btn small delete">🗑️</button>
        </div>
      </li>
    </ul>

    <!-- Custom Delete Confirmation Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <p>Are you sure you want to delete <strong>{{ toDelete.course_code }}</strong> — {{ toDelete.course_name }}?</p>
        <div class="modal-actions">
          <button @click="deleteCourseConfirmed" class="btn delete">Yes, Delete</button>
          <button @click="cancelDelete" class="btn">Cancel</button>
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
      form: {
        course_code: '',
        course_name: '',
        credits: '',
        semester: '',
        academic_year: '',
        is_active: true
      },
      editId: null,
      message: '',
      messageType: '',
      showModal: false,
      toDelete: null
    };
  },
  methods: {
    async fetchCourses() {
      try {
        console.log('Fetching courses...');
        const response = await api.get('/courses');
        console.log('Courses API Response:', response);
        
        this.courses = response.data;
        if (!this.courses || this.courses.length === 0) {
          console.warn('No courses found in the response');
          this.showMessage('No courses found', 'error');
        }
      } catch (error) {
        console.error('Error fetching courses:', error);
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        });
        this.showMessage(this.errToText(error), 'error');
      }
    },
    async submitForm() {
      try {
        const isUpdate = !!this.editId;
        const url = isUpdate ? `/courses/${this.editId}` : '/courses';
        const method = isUpdate ? 'put' : 'post';

        console.log(`${isUpdate ? 'Updating' : 'Creating'} course:`, this.form);
        const response = await api[method](url, this.form);
        console.log('Submit response:', response);

        await this.fetchCourses();
        this.resetForm();
        this.showMessage(`Course ${isUpdate ? 'updated' : 'added'} successfully!`, 'success');
      } catch (error) {
        console.error('Error submitting course:', error);
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        });
        this.showMessage(this.errToText(error), 'error');
      }
    },
    editCourse(course) {
      console.log('Editing course:', course);
      this.form = { ...course };
      this.editId = course.id;
    },
    confirmDelete(course) {
      console.log('Confirming delete for course:', course);
      this.toDelete = course;
      this.showModal = true;
    },
    cancelDelete() {
      this.toDelete = null;
      this.showModal = false;
    },
    async deleteCourseConfirmed() {
      try {
        console.log('Deleting course:', this.toDelete);
        const response = await api.delete(`/courses/${this.toDelete.id}`);
        console.log('Delete response:', response);

        await this.fetchCourses();
        this.showMessage('Course deleted successfully', 'success');
      } catch (error) {
        console.error('Error deleting course:', error);
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        });
        this.showMessage(this.errToText(error), 'error');
      } finally {
        this.cancelDelete();
      }
    },
    resetForm() {
      console.log('Resetting form');
      this.form = {
        course_code: '',
        course_name: '',
        credits: '',
        semester: '',
        academic_year: '',
        is_active: true
      };
      this.editId = null;
    },
    showMessage(msg, type) {
      this.message = msg;
      this.messageType = type;
      setTimeout(() => {
        this.message = '';
        this.messageType = '';
      }, 3000);
    },
    errToText(err) {
      if (err.response?.data?.message) {
        return `Error: ${err.response.data.message}`;
      }
      if (err.response) {
        return `Server error ${err.response.status}: ${err.response.statusText}`;
      }
      return `Error: ${err.message}`;
    }
  },
  async mounted() {
    await this.fetchCourses();
  }
};
</script>

<style>
.course-list {
  max-width: 700px;
  margin: 2rem auto;
  padding: 2rem;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  font-family: "Segoe UI", sans-serif;
}

h2 {
  text-align: center;
  margin-bottom: 1rem;
  color: #333;
}

.alert {
  padding: 0.75rem 1rem;
  margin-bottom: 1rem;
  border-radius: 6px;
  font-weight: bold;
  text-align: center;
}
.alert.success {
  background-color: #e6ffed;
  color: #1a7f37;
  border: 1px solid #1a7f37;
}
.alert.error {
  background-color: #ffe6e6;
  color: #c53030;
  border: 1px solid #c53030;
}

.form {
  margin-bottom: 2rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 0.4rem;
}

input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  transition: border-color 0.3s ease;
}

input:focus {
  border-color: #007bff;
  outline: none;
}

.btn {
  display: inline-block;
  padding: 0.45rem 1rem;
  margin-top: 0.5rem;
  margin-right: 0.5rem;
  font-size: 0.95rem;
  cursor: pointer;
  background-color: #f0f0f0;
  border: none;
  border-radius: 6px;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: #ddd;
}

.btn.primary {
  background-color: #007bff;
  color: #fff;
}
.btn.primary:hover {
  background-color: #0056b3;
}

.btn.small {
  font-size: 0.8rem;
  padding: 0.3rem 0.6rem;
}

.btn.edit {
  background-color: #ffc107;
  color: #000;
}
.btn.edit:hover {
  background-color: #e0a800;
}

.btn.delete {
  background-color: #dc3545;
  color: #fff;
}
.btn.delete:hover {
  background-color: #b02a37;
}

.course-items {
  list-style: none;
  padding: 0;
  margin: 0;
}

.course-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  margin-bottom: 0.75rem;
  background-color: #f9f9f9;
  border-left: 4px solid #007bff;
  border-radius: 6px;
  transition: box-shadow 0.2s ease;
}

.course-card:hover {
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.course-info {
  max-width: 80%;
}

.title {
  font-size: 1.05rem;
  font-weight: 600;
  margin-bottom: 0.3rem;
}

.name {
  color: #333;
}

.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.tag {
  background-color: #eef2f6;
  color: #333;
  font-size: 0.85rem;
  padding: 0.25rem 0.6rem;
  border-radius: 4px;
  border: 1px solid #d0d7de;
}

.actions {
  display: flex;
  gap: 0.4rem;
}

/* Custom Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25);
}

.modal-actions {
  margin-top: 1.5rem;
  display: flex;
  justify-content: center;
  gap: 1rem;
}


</style>
