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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 30px;
  margin-bottom: 30px;
  color: white;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.breadcrumb {
  margin-bottom: 16px;
  font-size: 0.9rem;
  opacity: 0.9;
}

.breadcrumb-link {
  color: white;
  text-decoration: none;
}

.breadcrumb-link:hover {
  text-decoration: underline;
}

.breadcrumb-separator {
  margin: 0 8px;
}

.course-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 16px 0;
}

.course-meta {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.course-code,
.course-credits,
.course-semester {
  background: rgba(255, 255, 255, 0.2);
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 600;
  backdrop-filter: blur(10px);
}

.course-stats {
  display: flex;
  gap: 20px;
}

.stat-card {
  background: rgba(255, 255, 255, 0.15);
  padding: 20px;
  border-radius: 12px;
  text-align: center;
  backdrop-filter: blur(10px);
  min-width: 120px;
}

.stat-card h3 {
  margin: 0 0 12px 0;
  font-size: 0.9rem;
  font-weight: 600;
  opacity: 0.9;
}

.grade-display,
.rank-display {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 8px;
}

.grade-display.grade-a-plus,
.grade-display.grade-a {
  color: #10b981;
}
.grade-display.grade-a-minus,
.grade-display.grade-b-plus {
  color: #3b82f6;
}
.grade-display.grade-b {
  color: #f59e0b;
}

.grade-points,
.rank-total {
  margin: 0;
  font-size: 0.8rem;
  opacity: 0.8;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 20px;
  }

  .course-stats {
    flex-direction: column;
    width: 100%;
  }

  .course-title {
    font-size: 2rem;
  }

  .course-meta {
    flex-direction: column;
    gap: 8px;
  }
}
</style>
