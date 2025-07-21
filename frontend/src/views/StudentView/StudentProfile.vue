<template>
  <div class="student-profile">
    <div class="page-header">
      <h1>My Profile</h1>
      <p>View and manage your personal information and academic details.</p>
    </div>

    <div class="profile-container">
      <div class="profile-card">
        <div class="profile-header">
          <div class="profile-avatar">
            {{ getStudentInitials(studentInfo.name) }}
          </div>
          <div class="profile-info">
            <h2>{{ studentInfo.name }}</h2>
            <p class="student-id">{{ studentInfo.matricNumber }}</p>
            <p class="student-program">{{ studentInfo.program }}</p>
          </div>
        </div>

        <div class="profile-details">
          <div class="detail-section">
            <h3>Personal Information</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Full Name</label>
                <span>{{ studentInfo.name }}</span>
              </div>
              <div class="detail-item">
                <label>Email</label>
                <span>{{ studentInfo.email }}</span>
              </div>
              <div class="detail-item">
                <label>Matric Number</label>
                <span>{{ studentInfo.matricNumber }}</span>
              </div>
              <div class="detail-item">
                <label>Department</label>
                <span>{{ studentInfo.department }}</span>
              </div>
              <div class="detail-item">
                <label>Program</label>
                <span>{{ studentInfo.program }}</span>
              </div>
              <div class="detail-item">
                <label>Current Semester</label>
                <span>{{ studentInfo.currentSemester }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3>Academic Summary</h3>
            <div class="academic-stats">
              <div class="stat-item">
                <div class="stat-value">{{ academicStats.overallGPA }}</div>
                <div class="stat-label">Overall GPA</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">
                  {{ academicStats.enrolledCoursesCount }}
                </div>
                <div class="stat-label">Enrolled Courses</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">#{{ academicStats.classRank }}</div>
                <div class="stat-label">Class Rank</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ academicStats.percentile }}%</div>
                <div class="stat-label">Percentile</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "StudentProfile",
  data() {
    return {
      studentInfo: {
        name: "John Smith",
        matricNumber: "A12345",
        email: "student1@student.university.edu",
        department: "Computer Science",
        program: "Bachelor of Computer Science",
        currentSemester: "2024/2025-1",
      },
      academicStats: {
        overallGPA: 2.7,
        enrolledCoursesCount: 5,
        classRank: 1,
        percentile: 95,
      },
    };
  },
  methods: {
    getStudentInitials(name) {
      return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
    },
  },
};
</script>

<style scoped>
.student-profile {
  max-width: 900px;
  margin: 0 auto;
  padding: 32px 12px;
  background: #101712;
  min-height: 100vh;
}

.page-header {
  margin-bottom: 32px;
  text-align: left;
}

.page-header h1 {
  color: #00ff88;
  margin-bottom: 10px;
  font-size: 2.2rem;
  letter-spacing: 0.03em;
  font-weight: 800;
  text-shadow: 0 2px 8px #003d1e44;
}

.page-header p {
  color: #b6f5d8;
  margin: 0;
  font-size: 1.1rem;
  font-weight: 400;
}

.profile-card {
  background: #181f1b;
  border: 2px solid #00ff88;
  border-radius: 20px;
  box-shadow: 0 6px 32px #00ff8822;
  overflow: hidden;
  transition: box-shadow 0.2s;
}

.profile-card:hover {
  box-shadow: 0 8px 40px #00ff8844;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 32px;
  padding: 36px 36px 24px 36px;
  background: linear-gradient(120deg, #003d1e 0%, #00ff88 100%);
  color: #101712;
  border-bottom: 2px solid #00ff88;
}

.profile-avatar {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  background: #101712;
  border: 3px solid #00ff88;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  font-weight: 900;
  color: #00ff88;
  box-shadow: 0 2px 16px #00ff8844;
}

.profile-info h2 {
  margin: 0 0 10px 0;
  font-size: 1.7rem;
  font-weight: 700;
  color: #101712;
  letter-spacing: 0.02em;
}

.student-id {
  margin: 0 0 6px 0;
  opacity: 0.85;
  font-size: 1.1rem;
  color: #003d1e;
  font-weight: 600;
}

.student-program {
  margin: 0;
  opacity: 0.7;
  font-size: 1rem;
  color: #003d1e;
  font-weight: 500;
}

.profile-details {
  padding: 32px 36px;
  background: #101712;
}

.detail-section {
  margin-bottom: 36px;
}

.detail-section:last-child {
  margin-bottom: 0;
}

.detail-section h3 {
  color: #00ff88;
  margin: 0 0 18px 0;
  font-size: 1.15rem;
  border-bottom: 2px solid #003d1e;
  padding-bottom: 7px;
  letter-spacing: 0.04em;
  font-weight: 700;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 22px;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
  background: #181f1b;
  border: 1px solid #003d1e;
  border-radius: 10px;
  padding: 14px 18px;
  transition: background 0.2s;
}

.detail-item label {
  font-size: 0.85rem;
  color: #00ff88;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.detail-item span {
  color: #b6f5d8;
  font-weight: 600;
  font-size: 1.05rem;
}

.academic-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 22px;
}

.stat-item {
  text-align: center;
  padding: 24px 10px;
  background: #003d1e;
  border-radius: 12px;
  border: 2px solid #00ff88;
  box-shadow: 0 2px 12px #00ff8822;
  transition: background 0.2s;
}

.stat-item:hover {
  background: #00ff88;
  color: #003d1e;
}

.stat-value {
  font-size: 2.2rem;
  font-weight: 900;
  color: #00ff88;
  margin-bottom: 10px;
  text-shadow: 0 2px 8px #003d1e44;
}

.stat-label {
  font-size: 0.9rem;
  color: #b6f5d8;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

@media (max-width: 900px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
    padding: 28px 12px 18px 12px;
    gap: 18px;
  }
  .profile-details {
    padding: 24px 10px;
  }
  .detail-grid {
    grid-template-columns: 1fr;
  }
  .academic-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
