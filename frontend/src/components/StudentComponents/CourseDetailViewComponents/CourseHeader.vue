<template>
  <div class="course-header">
    <div class="header-content">
      <div class="course-info">
        <div class="breadcrumb">
          <router-link to="/student/dashboard" class="breadcrumb-link"
            >Dashboard</router-link
          >
          <span class="breadcrumb-separator">›</span>
          <span class="breadcrumb-current">{{ course.code }}</span>
        </div>
        <h1 class="course-title">{{ course.name }}</h1>
        <div class="course-meta">
          <span class="course-code">{{ course.code }}</span>
          <span class="course-credits">{{ course.credits }} Credits</span>
          <span class="course-semester">{{ course.semester }}</span>
        </div>
      </div>

      <div class="course-stats">
        <div class="stat-card">
          <h3>Current Grade</h3>
          <div
            class="grade-display"
            :class="getGradeClass(course.currentGrade)"
          >
            {{ course.currentGrade }}
          </div>
          <p class="grade-points">
            {{ course.currentMarks }}/{{ course.totalMarks }}
          </p>
        </div>

        <div class="stat-card">
          <h3>Class Rank</h3>
          <div class="rank-display">#{{ course.classRank }}</div>
          <p class="rank-total">of {{ course.totalStudents }} students</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CourseHeader",
  props: {
    course: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  methods: {
    getGradeClass(grade) {
      const gradeMap = {
        "A+": "grade-a-plus",
        A: "grade-a",
        "A-": "grade-a-minus",
        "B+": "grade-b-plus",
        B: "grade-b",
        "B-": "grade-b-minus",
        "C+": "grade-c-plus",
        C: "grade-c",
      };
      return gradeMap[grade] || "grade-default";
    },
  },
};
</script>

<style scoped>
.course-header {
  background: linear-gradient(135deg, #111 0%, #1a3d1a 100%);
  border-radius: 20px;
  padding: 36px 28px;
  margin-bottom: 32px;
  color: #d1ffd6;
  box-shadow: 0 6px 32px 0 rgba(0, 64, 0, 0.18);
  border: 2px solid #1db954;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: stretch;
  gap: 40px;
}

.breadcrumb {
  margin-bottom: 18px;
  font-size: 1rem;
  opacity: 0.85;
  letter-spacing: 0.04em;
}

.breadcrumb-link {
  color: #1db954;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}

.breadcrumb-link:hover {
  color: #aaffc3;
  text-decoration: underline;
}

.breadcrumb-separator {
  margin: 0 10px;
  color: #1db954;
}

.breadcrumb-current {
  color: #d1ffd6;
  font-weight: 700;
}

.course-title {
  font-size: 2.8rem;
  font-weight: 800;
  margin: 0 0 18px 0;
  color: #1db954;
  letter-spacing: 0.03em;
  text-shadow: 0 2px 12px #0a2d0a;
}

.course-meta {
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
}

.course-code,
.course-credits,
.course-semester {
  background: rgba(29, 185, 84, 0.18);
  padding: 7px 16px;
  border-radius: 16px;
  font-size: 1rem;
  font-weight: 700;
  color: #d1ffd6;
  border: 1px solid #1db954;
  box-shadow: 0 1px 6px 0 rgba(29, 185, 84, 0.08);
}

.course-stats {
  display: flex;
  gap: 28px;
  align-items: stretch;
}

.stat-card {
  background: linear-gradient(120deg, #1a3d1a 60%, #111 100%);
  padding: 26px 22px;
  border-radius: 18px;
  text-align: center;
  min-width: 140px;
  border: 1.5px solid #1db954;
  box-shadow: 0 2px 16px 0 rgba(29, 185, 84, 0.12);
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.stat-card h3 {
  margin: 0 0 14px 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: #aaffc3;
  opacity: 0.92;
  letter-spacing: 0.02em;
}

.grade-display,
.rank-display {
  font-size: 2.2rem;
  font-weight: 900;
  margin-bottom: 10px;
  letter-spacing: 0.04em;
  border-radius: 10px;
  padding: 6px 0;
  background: rgba(29, 185, 84, 0.10);
  border: 1px solid #1db954;
  color: #1db954;
  box-shadow: 0 1px 8px 0 rgba(29, 185, 84, 0.10);
}

.grade-display.grade-a-plus,
.grade-display.grade-a {
  color: #00ff6a;
  background: rgba(0, 255, 106, 0.10);
  border-color: #00ff6a;
}
.grade-display.grade-a-minus,
.grade-display.grade-b-plus {
  color: #1db954;
  background: rgba(29, 185, 84, 0.13);
  border-color: #1db954;
}
.grade-display.grade-b {
  color: #aaffc3;
  background: rgba(170, 255, 195, 0.10);
  border-color: #aaffc3;
}
.grade-display.grade-default {
  color: #d1ffd6;
  background: rgba(209, 255, 214, 0.08);
  border-color: #d1ffd6;
}

.grade-points,
.rank-total {
  margin: 0;
  font-size: 0.9rem;
  opacity: 0.85;
  color: #aaffc3;
  font-weight: 600;
}

@media (max-width: 900px) {
  .header-content {
    flex-direction: column;
    gap: 28px;
  }
  .course-stats {
    flex-direction: column;
    width: 100%;
    gap: 18px;
  }
  .course-title {
    font-size: 2.1rem;
  }
  .course-meta {
    flex-direction: column;
    gap: 10px;
  }
}
</style>
