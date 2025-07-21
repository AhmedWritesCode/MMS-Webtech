<template>
  <div class="student-settings">
    <div class="page-header">
      <h1>Settings</h1>
      <p>Manage your account preferences and notification settings.</p>
    </div>

    <div class="settings-container">
      <div class="settings-section">
        <h2>Account Settings</h2>
        <div class="setting-item">
          <div class="setting-info">
            <h3>Change Password</h3>
            <p>Update your account password for better security.</p>
          </div>
          <button class="btn-secondary">Change Password</button>
        </div>

        <div class="setting-item">
          <div class="setting-info">
            <h3>Email Preferences</h3>
            <p>Manage how often you receive email notifications.</p>
          </div>
          <select class="setting-select">
            <option>Immediate</option>
            <option>Daily Digest</option>
            <option>Weekly Summary</option>
            <option>Never</option>
          </select>
        </div>
      </div>

      <div class="settings-section">
        <h2>Notification Settings</h2>
        <div class="setting-item">
          <div class="setting-info">
            <h3>Grade Updates</h3>
            <p>Get notified when new grades are posted.</p>
          </div>
          <label class="toggle-switch">
            <input type="checkbox" checked />
            <span class="toggle-slider"></span>
          </label>
        </div>

        <div class="setting-item">
          <div class="setting-info">
            <h3>Assignment Reminders</h3>
            <p>Receive reminders for upcoming assignments.</p>
          </div>
          <label class="toggle-switch">
            <input type="checkbox" checked />
            <span class="toggle-slider"></span>
          </label>
        </div>

        <div class="setting-item">
          <div class="setting-info">
            <h3>Schedule Changes</h3>
            <p>Get notified about class schedule updates.</p>
          </div>
          <label class="toggle-switch">
            <input type="checkbox" />
            <span class="toggle-slider"></span>
          </label>
        </div>
      </div>

      <div class="settings-section">
        <h2>Privacy Settings</h2>
        <div class="setting-item">
          <div class="setting-info">
            <h3>Profile Visibility</h3>
            <p>Control who can see your academic profile.</p>
          </div>
          <select class="setting-select">
            <option>Public</option>
            <option>Classmates Only</option>
            <option>Private</option>
          </select>
        </div>
      </div>

      <div class="settings-section danger-zone">
        <h2>Danger Zone</h2>
        <div class="setting-item">
          <div class="setting-info">
            <h3>Delete Account</h3>
            <p>Permanently delete your account and all associated data.</p>
          </div>
          <button class="btn-danger">Delete Account</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "StudentSettings",
};
</script>

<style scoped>
.student-settings {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.page-header {
  margin-bottom: 30px;
}

.page-header h1 {
  color: #1e293b;
  margin-bottom: 8px;
}

.page-header p {
  color: #64748b;
  margin: 0;
}

.settings-container {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.settings-section {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 24px;
}

.settings-section h2 {
  color: #1e293b;
  margin: 0 0 20px 0;
  font-size: 1.3rem;
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 8px;
}

.setting-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 0;
  border-bottom: 1px solid #f1f5f9;
}

.setting-item:last-child {
  border-bottom: none;
}

.setting-info h3 {
  color: #1e293b;
  margin: 0 0 4px 0;
  font-size: 1rem;
}

.setting-info p {
  color: #64748b;
  margin: 0;
  font-size: 0.9rem;
}

.btn-secondary {
  padding: 8px 16px;
  background: white;
  border: 1px solid #3b82f6;
  color: #3b82f6;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #3b82f6;
  color: white;
}

.btn-danger {
  padding: 8px 16px;
  background: white;
  border: 1px solid #dc2626;
  color: #dc2626;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-danger:hover {
  background: #dc2626;
  color: white;
}

.setting-select {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background: white;
  color: #1e293b;
  font-size: 0.9rem;
  min-width: 150px;
}

/* Toggle Switch */
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #cbd5e1;
  transition: 0.4s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

input:checked + .toggle-slider {
  background-color: #3b82f6;
}

input:checked + .toggle-slider:before {
  transform: translateX(26px);
}

.danger-zone {
  border-color: #fecaca;
  background: #fef2f2;
}

.danger-zone h2 {
  color: #dc2626;
  border-bottom-color: #fecaca;
}

@media (max-width: 768px) {
  .setting-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .setting-select {
    min-width: 100%;
  }
}
</style>
