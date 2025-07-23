<template>
  <div class="student-profile">
    <div class="page-header">
      <h1>My Profile</h1>
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
  min-height: 100vh;
  width: 100%;
  background: linear-gradient(135deg, #7C9885 0%, #B5B682 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2.5rem 0;
}

.page-header {
  width: 100%;
  max-width: 900px;
  margin-bottom: 2.5rem;
  text-align: left;
}

.page-header h1 {
  font-size: 2.2rem;
  font-weight: 800;
  color: #000000;
  margin-bottom: 0.5rem;
}

.page-header p {
  color: #B5B682;
  font-size: 1.1rem;
  margin: 0;
}

.profile-container {
  width: 100%;
  max-width: 900px;
  display: flex;
  justify-content: center;
}

.profile-card {
  background: rgba(255,255,255,0.97);
  border-radius: 1.5rem;
  box-shadow: 0 4px 24px rgba(181, 182, 130, 0.10);
  border: 2px solid #B5B682;
  width: 100%;
  padding: 2.5rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  border-radius: 20%;
  background: linear-gradient(135deg, #B5B682 60%, #7C9885 100%);
  color: #fff;
  font-size: 2.5rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 12px 0 rgba(181, 182, 130, 0.25);
  border: 3px solid #7C9885;
}

.profile-info h2 {
  margin: 0 0 0.3rem 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #7C9885;
}

.student-id, .student-program {
  color: #B5B682;
  font-size: 1rem;
  margin: 0;
}

.profile-details {
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
  padding: 0 0.5rem;
}

.detail-section h3 {
  color: #7C9885;
  margin: 0 0 18px 0;
  font-size: 1.15rem;
  border-bottom: 2px solid #B5B682;
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
  background: #f9f9e0;
  border: 1.5px solid #B5B682;
  border-radius: 10px;
  padding: 14px 18px;
  transition: background 0.2s;
}

.detail-item label {
  font-size: 0.85rem;
  color: #7C9885;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.detail-item span {
  color: #23272f;
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
  background: #f9f9e0;
  border-radius: 12px;
  border: 2px solid #7C9885;
  box-shadow: 0 2px 12px #B5B68222;
  transition: background 0.2s;
}

.stat-item:hover {
  background: #7C9885;
  color: #fff;
}

.stat-value {
  font-size: 2.2rem;
  font-weight: 900;
  color: #B5B682;
  margin-bottom: 10px;
  text-shadow: none;
}

.stat-label {
  font-size: 0.9rem;
  color: #7C9885;
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
