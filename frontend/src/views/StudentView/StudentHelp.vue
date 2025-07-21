<template>
  <div class="student-help">
    <div class="page-header">
      <h1>Help & Support</h1>
      <p>Find answers to common questions and get support when you need it.</p>
    </div>

    <div class="help-container">
      <div class="search-section">
        <div class="search-box">
          <input
            type="text"
            placeholder="Search for help topics..."
            v-model="searchQuery"
            class="search-input"
          />
          <button class="search-btn">🔍</button>
        </div>
      </div>

      <div class="help-content">
        <div class="help-section">
          <h2>Frequently Asked Questions</h2>
          <div class="faq-list">
            <div class="faq-item" v-for="faq in faqs" :key="faq.id">
              <div class="faq-question" @click="toggleFaq(faq.id)">
                <h3>{{ faq.question }}</h3>
                <span class="faq-toggle">{{
                  expandedFaqs.includes(faq.id) ? "−" : "+"
                }}</span>
              </div>
              <div class="faq-answer" v-show="expandedFaqs.includes(faq.id)">
                <p>{{ faq.answer }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="help-section">
          <h2>Quick Guides</h2>
          <div class="guides-grid">
            <div class="guide-card" v-for="guide in guides" :key="guide.id">
              <div class="guide-icon">{{ guide.icon }}</div>
              <h3>{{ guide.title }}</h3>
              <p>{{ guide.description }}</p>
              <button class="guide-btn">View Guide</button>
            </div>
          </div>
        </div>

        <div class="help-section">
          <h2>Contact Support</h2>
          <div class="contact-options">
            <div class="contact-card">
              <div class="contact-icon">📧</div>
              <h3>Email Support</h3>
              <p>Send us an email and we'll get back to you within 24 hours.</p>
              <a href="mailto:support@university.edu" class="contact-link"
                >support@university.edu</a
              >
            </div>

            <div class="contact-card">
              <div class="contact-icon">💬</div>
              <h3>Live Chat</h3>
              <p>Chat with our support team during business hours.</p>
              <button class="contact-btn">Start Chat</button>
            </div>

            <div class="contact-card">
              <div class="contact-icon">📞</div>
              <h3>Phone Support</h3>
              <p>Call us directly for immediate assistance.</p>
              <a href="tel:+1234567890" class="contact-link"
                >+1 (234) 567-890</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "StudentHelp",
  data() {
    return {
      searchQuery: "",
      expandedFaqs: [],
      faqs: [
        {
          id: 1,
          question: "How do I view my grades?",
          answer:
            "You can view your grades by navigating to the Dashboard and clicking on any course card, or by going to the Performance Trends page to see your academic progress over time.",
        },
        {
          id: 2,
          question: "What does my GPA mean?",
          answer:
            "Your GPA (Grade Point Average) is a numerical representation of your academic performance. It's calculated by averaging the grade points of all your completed courses.",
        },
        {
          id: 3,
          question: "How do I request a grade remark?",
          answer:
            "To request a grade remark, go to the Quick Actions section on your dashboard and click on 'Request Remark'. Fill out the form with your course details and reason for the request.",
        },
        {
          id: 4,
          question: "Can I see how I compare to other students?",
          answer:
            "Yes! Use the Performance Comparison tool to see how your grades compare to your classmates and understand your standing in the class.",
        },
        {
          id: 5,
          question: "What is the What-If Simulator?",
          answer:
            "The What-If Simulator allows you to calculate how different grades on upcoming assignments would affect your overall course grade and GPA.",
        },
      ],
      guides: [
        {
          id: 1,
          icon: "📊",
          title: "Understanding Your Grades",
          description:
            "Learn how to interpret your grades and understand your academic performance.",
        },
        {
          id: 2,
          icon: "📈",
          title: "Using Performance Trends",
          description:
            "Track your academic progress over time and identify areas for improvement.",
        },
        {
          id: 3,
          icon: "🎯",
          title: "Grade Simulator Guide",
          description:
            "Master the What-If Simulator to plan your academic strategy.",
        },
        {
          id: 4,
          icon: "📝",
          title: "Requesting Remarks",
          description:
            "Step-by-step guide to requesting grade remarks when needed.",
        },
      ],
    };
  },
  methods: {
    toggleFaq(faqId) {
      const index = this.expandedFaqs.indexOf(faqId);
      if (index > -1) {
        this.expandedFaqs.splice(index, 1);
      } else {
        this.expandedFaqs.push(faqId);
      }
    },
  },
};
</script>

<style scoped>
.student-help {
  max-width: 1000px;
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

.search-section {
  margin-bottom: 30px;
}

.search-box {
  display: flex;
  max-width: 500px;
  margin: 0 auto;
}

.search-input {
  flex: 1;
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 8px 0 0 8px;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.2s;
}

.search-input:focus {
  border-color: #3b82f6;
}

.search-btn {
  padding: 12px 16px;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 0 8px 8px 0;
  cursor: pointer;
  font-size: 1rem;
}

.help-content {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.help-section {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 24px;
}

.help-section h2 {
  color: #1e293b;
  margin: 0 0 20px 0;
  font-size: 1.3rem;
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 8px;
}

.faq-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.faq-item {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
}

.faq-question {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #f8fafc;
  cursor: pointer;
  transition: background 0.2s;
}

.faq-question:hover {
  background: #f1f5f9;
}

.faq-question h3 {
  margin: 0;
  color: #1e293b;
  font-size: 1rem;
}

.faq-toggle {
  font-size: 1.5rem;
  font-weight: 700;
  color: #3b82f6;
  width: 24px;
  text-align: center;
}

.faq-answer {
  padding: 20px;
  background: white;
  border-top: 1px solid #e2e8f0;
}

.faq-answer p {
  margin: 0;
  color: #64748b;
  line-height: 1.6;
}

.guides-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.guide-card {
  padding: 24px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  text-align: center;
  transition: all 0.2s;
}

.guide-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.guide-icon {
  font-size: 3rem;
  margin-bottom: 16px;
}

.guide-card h3 {
  color: #1e293b;
  margin: 0 0 12px 0;
  font-size: 1.1rem;
}

.guide-card p {
  color: #64748b;
  margin: 0 0 20px 0;
  line-height: 1.5;
}

.guide-btn {
  padding: 8px 16px;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.2s;
}

.guide-btn:hover {
  background: #2563eb;
}

.contact-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.contact-card {
  padding: 24px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  text-align: center;
  background: #f8fafc;
}

.contact-icon {
  font-size: 2.5rem;
  margin-bottom: 16px;
}

.contact-card h3 {
  color: #1e293b;
  margin: 0 0 12px 0;
  font-size: 1.1rem;
}

.contact-card p {
  color: #64748b;
  margin: 0 0 16px 0;
  line-height: 1.5;
}

.contact-link {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 500;
}

.contact-link:hover {
  text-decoration: underline;
}

.contact-btn {
  padding: 8px 16px;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.2s;
}

.contact-btn:hover {
  background: #2563eb;
}

@media (max-width: 768px) {
  .guides-grid,
  .contact-options {
    grid-template-columns: 1fr;
  }

  .search-box {
    flex-direction: column;
  }

  .search-input {
    border-radius: 8px 8px 0 0;
  }

  .search-btn {
    border-radius: 0 0 8px 8px;
  }
}
</style>
