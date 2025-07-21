<template>
  <div class="section-card advisor-notes">
    <div class="section-header">
      <h3>Advisor Notes</h3>
      <button class="add-note-btn" @click="$emit('add-note')">
        + Add Note
      </button>
    </div>
    <div class="notes-list">
      <div v-for="note in advisorNotes" :key="note.id" class="note-item">
        <div class="note-header">
          <div class="note-date">{{ formatDate(note.date) }}</div>
          <div class="note-type" :class="note.type">{{ note.type }}</div>
        </div>
        <div v-if="note.title" class="note-title">
          <h4>{{ note.title }}</h4>
        </div>
        <div class="note-content">
          <p>{{ note.content }}</p>
        </div>
        <div class="note-meta">
          <div v-if="note.followUpRequired" class="follow-up-badge">
            <span class="follow-up-icon">📋</span>
            Follow-up Required
            <span v-if="note.followUpCompleted" class="completed"
              >✓ Completed</span
            >
          </div>
        </div>
        <div class="note-actions">
          <button class="note-action edit" @click="$emit('edit-note', note.id)">
            Edit
          </button>
          <button
            class="note-action delete"
            @click="$emit('delete-note', note.id)"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
    <div v-if="advisorNotes.length === 0" class="no-notes">
      <p>
        No notes yet. Click "Add Note" to start documenting meetings and
        observations.
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdvisorNotesCard",
  props: {
    advisorNotes: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  emits: ["add-note", "edit-note", "delete-note"],
  methods: {
    formatDate(date) {
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },
  },
};
</script>

<style scoped>
.section-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
}

.add-note-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.add-note-btn:hover {
  background: #2563eb;
}

.notes-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.note-item {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
  background: #f8fafc;
}

.note-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.note-date {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 500;
}

.note-type {
  font-size: 0.7rem;
  padding: 4px 8px;
  border-radius: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.note-type.meeting {
  background: #dbeafe;
  color: #3b82f6;
}

.note-type.observation {
  background: #f3f4f6;
  color: #6b7280;
}

.note-type.concern {
  background: #fee2e2;
  color: #dc2626;
}

.note-type.achievement {
  background: #dcfce7;
  color: #059669;
}

.note-type.plan {
  background: #fef3c7;
  color: #d97706;
}

.note-title h4 {
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
  color: #1e293b;
}

.note-content p {
  margin: 0;
  color: #1e293b;
  line-height: 1.5;
}

.note-meta {
  margin-top: 8px;
}

.follow-up-badge {
  background: #fef3c7;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
}

.follow-up-icon {
  margin-right: 4px;
}

.completed {
  background: #dcfce7;
  padding: 2px 4px;
  border-radius: 4px;
  font-size: 0.6rem;
  font-weight: 600;
  color: #059669;
}

.note-actions {
  display: flex;
  gap: 8px;
  margin-top: 12px;
}

.note-action {
  padding: 4px 8px;
  border: none;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.note-action.edit {
  background: #f1f5f9;
  color: #475569;
}

.note-action.edit:hover {
  background: #e2e8f0;
}

.note-action.delete {
  background: #fee2e2;
  color: #dc2626;
}

.note-action.delete:hover {
  background: #fecaca;
}

.no-notes {
  text-align: center;
  padding: 20px;
  color: #64748b;
}

@media (max-width: 480px) {
  .note-actions {
    flex-direction: column;
  }
}
</style>
