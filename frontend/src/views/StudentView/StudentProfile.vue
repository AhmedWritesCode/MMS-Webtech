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

.profile-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 24px;
  padding: 32px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
}

.profile-info h2 {
  margin: 0 0 8px 0;
  font-size: 1.5rem;
}

.student-id {
  margin: 0 0 4px 0;
  opacity: 0.9;
  font-size: 1rem;
}

.student-program {
  margin: 0;
  opacity: 0.8;
  font-size: 0.9rem;
}

.profile-details {
  padding: 32px;
}

.detail-section {
  margin-bottom: 32px;
}

.detail-section:last-child {
  margin-bottom: 0;
}

.detail-section h3 {
  color: #1e293b;
  margin: 0 0 20px 0;
  font-size: 1.2rem;
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 8px;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.detail-item label {
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.detail-item span {
  color: #1e293b;
  font-weight: 500;
}

.academic-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 20px;
}

.stat-item {
  text-align: center;
  padding: 20px;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #3b82f6;
  margin-bottom: 8px;
}

.stat-label {
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
    padding: 24px;
  }

  .detail-grid {
    grid-template-columns: 1fr;
  }

  .academic-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
