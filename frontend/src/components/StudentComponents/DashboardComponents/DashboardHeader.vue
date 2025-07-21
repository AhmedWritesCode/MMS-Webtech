<template>
  <section class="dashboard-hero">
    <div class="hero-content">
      <div class="avatar">
        {{ initials }}
      </div>
      <div class="hero-text">
        <h1>Welcome, <span class="student-name">{{ studentName }}</span></h1>
        <div class="student-id">{{ matricNumber }}</div>
      </div>
      <div class="semester-pill">
        {{ currentSemester }}
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "DashboardHeader",
  props: {
    studentName: {
      type: String,
      default: "Student",
    },
    matricNumber: {
      type: String,
      default: "",
    },
    currentSemester: {
      type: String,
      default: "",
    },
  },
  computed: {
    initials() {
      if (!this.studentName) return "S";
      const parts = this.studentName.split(" ");
      return parts.length > 1
        ? parts[0][0] + parts[1][0]
        : parts[0][0];
    },
  },
};
</script>

<style scoped>
.dashboard-hero {
  width: 100%;
  border-radius: 1.5rem; 
background: linear-gradient(90deg, #3b82f6 0%, #06b6d4 100%);
  color: #fff;
  margin-bottom: 2.5rem;
  box-shadow: 0 4px 24px 0 rgba(99, 102, 241, 0.10);
  padding: 2.5rem 2rem 2rem 2rem;
  display: flex;
  align-items: center;
  min-height: 160px;
}
.hero-content {
  display: flex;
  align-items: center;
  width: 100%;
  position: relative;
}
.avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255,255,255,0.18);
  color: #fff;
  font-size: 2.5rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 2rem;
  box-shadow: 0 2px 12px 0 rgba(99, 102, 241, 0.15);
}
.hero-text {
  flex: 1;
}
.hero-text h1 {
  font-size: 2.1rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
  letter-spacing: -1px;
}
.student-name {
  font-weight: 800;
  letter-spacing: 0.5px;
}
.student-id {
  font-size: 1rem;
  opacity: 0.85;
  margin-top: 0.2rem;
}
.semester-pill {
  background: rgba(255,255,255,0.18);
  color: #fff;
  font-weight: 600;
  font-size: 1.1rem;
  border-radius: 2rem;
  padding: 0.6rem 1.5rem;
  position: absolute;
  right: 0;
  top: 1.5rem;
  box-shadow: 0 2px 8px 0 rgba(99, 102, 241, 0.10);
}
@media (max-width: 700px) {
  .dashboard-hero {
    flex-direction: column;
    padding: 1.5rem 1rem 1rem 1rem;
    min-height: 120px;
  }
  .hero-content {
    flex-direction: column;
    align-items: flex-start;
  }
  .avatar {
    margin-right: 0;
    margin-bottom: 1rem;
  }
  .semester-pill {
    position: static;
    margin-top: 1rem;
    align-self: flex-end;
  }
}
</style>
