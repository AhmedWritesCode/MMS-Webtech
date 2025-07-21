<template>
  <div v-if="showModal" class="modal-overlay" @click="$emit('close-modal')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>{{ isEditing ? "Edit Note" : "Add New Note" }}</h3>
        <button class="modal-close" @click="$emit('close-modal')">×</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="noteType">Note Type:</label>
          <select
            id="noteType"
            :value="noteForm.type"
            @change="updateNoteType($event.target.value)"
            class="form-select"
          >
            <option value="meeting">Meeting</option>
            <option value="observation">Observation</option>
            <option value="concern">Concern</option>
            <option value="achievement">Achievement</option>
            <option value="plan">Action Plan</option>
          </select>
        </div>
        <div class="form-group">
          <label for="noteTitle">Title:</label>
          <input
            id="noteTitle"
            :value="noteForm.title"
            @input="updateNoteTitle($event.target.value)"
            class="form-input"
            placeholder="Enter note title..."
          />
        </div>
        <div class="form-group">
          <label for="meetingDate">Meeting Date:</label>
          <input
            id="meetingDate"
            type="date"
            :value="noteForm.meeting_date"
            @input="updateMeetingDate($event.target.value)"
            class="form-input"
          />
        </div>
        <div class="form-group">
          <label for="noteContent">Note Content:</label>
          <textarea
            id="noteContent"
            :value="noteForm.content"
            @input="updateNoteContent($event.target.value)"
            class="form-textarea"
            rows="6"
            placeholder="Enter your note here..."
          ></textarea>
        </div>
        <div class="form-group">
          <label class="checkbox-label">
            <input
              type="checkbox"
              :checked="noteForm.follow_up_required"
              @change="updateFollowUpRequired($event.target.checked)"
              class="form-checkbox"
            />
            Follow-up Required
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn secondary" @click="$emit('close-modal')">
          Cancel
        </button>
        <button class="btn primary" @click="$emit('save-note')">
          {{ isEditing ? "Update" : "Save" }} Note
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "NoteModal",
  props: {
    showModal: {
      type: Boolean,
      required: true,
      default: false,
    },
    isEditing: {
      type: Boolean,
      required: true,
      default: false,
    },
    noteForm: {
      type: Object,
      required: true,
      default: () => ({
        type: "meeting",
        title: "",
        content: "",
        meeting_date: new Date().toISOString().split("T")[0],
        follow_up_required: false,
      }),
    },
  },
  emits: [
    "close-modal",
    "save-note",
    "update-note-type",
    "update-note-content",
    "update-note-title",
    "update-meeting-date",
    "update-follow-up-required",
  ],
  methods: {
    updateNoteType(type) {
      this.$emit("update-note-type", type);
    },

    updateNoteContent(content) {
      this.$emit("update-note-content", content);
    },

    updateNoteTitle(title) {
      this.$emit("update-note-title", title);
    },

    updateMeetingDate(date) {
      this.$emit("update-meeting-date", date);
    },

    updateFollowUpRequired(checked) {
      this.$emit("update-follow-up-required", checked);
    },
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
  box-sizing: border-box;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  max-height: calc(100vh - 40px);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 12px 12px 0 0;
}

.modal-header h3 {
  margin: 0;
  font-weight: 700;
  color: #1e293b;
  font-size: 1.25rem;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #64748b;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  color: #1e293b;
  background: #e2e8f0;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
}

.form-group {
  margin-bottom: 20px;
}

.form-group:last-child {
  margin-bottom: 0;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9rem;
}

.form-select,
.form-textarea,
.form-input {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  font-family: inherit;
  box-sizing: border-box;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-select:focus,
.form-textarea:focus,
.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
  max-height: 200px;
}

.form-input {
  height: 40px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-weight: 500;
  color: #1e293b;
  padding: 8px 0;
}

.form-checkbox {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #3b82f6;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 0 0 12px 12px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
  min-width: 80px;
}

.btn.primary {
  background: #3b82f6;
  color: white;
}

.btn.primary:hover {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn.secondary {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.btn.secondary:hover {
  background: #e2e8f0;
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
  .modal-overlay {
    padding: 10px;
  }

  .modal-content {
    max-width: 100%;
    max-height: calc(100vh - 20px);
  }

  .modal-header,
  .modal-body,
  .modal-footer {
    padding: 16px 20px;
  }

  .modal-header h3 {
    font-size: 1.1rem;
  }

  .btn {
    padding: 8px 16px;
    font-size: 0.85rem;
  }

  .form-textarea {
    min-height: 100px;
  }
}

@media (max-width: 480px) {
  .modal-footer {
    flex-direction: column;
  }

  .btn {
    width: 100%;
  }
}
</style>
