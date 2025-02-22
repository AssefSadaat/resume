document.addEventListener('DOMContentLoaded', function() {
  // Header Scroll Effect
  const header = document.querySelector('.site-header');
  let lastScroll = 0;

  window.addEventListener('scroll', () => {
      const currentScroll = window.pageYOffset;
      
      if (currentScroll <= 0) {
          header.classList.remove('scrolled');
      } else if (currentScroll > lastScroll) {
          header.classList.add('scrolled');
      }
      
      lastScroll = currentScroll;
  });

  // Smooth Scrolling for Navigation
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
          e.preventDefault();
          document.querySelector(this.getAttribute('href')).scrollIntoView({
              behavior: 'smooth'
          });
      });
  });

  // Particles.js Configuration
  particlesJS('particles-js', {
      particles: {
          number: { value: 80, density: { enable: true, value_area: 800 } },
          color: { value: '#4f46e5' },
          shape: { type: 'circle' },
          opacity: {
              value: 0.5,
              random: true,
              animation: { enable: true, speed: 1, minimumValue: 0.1, sync: false }
          },
          size: {
              value: 3,
              random: true,
              animation: { enable: true, speed: 2, minimumValue: 0.1, sync: false }
          },
          line_linked: {
              enable: true,
              distance: 150,
              color: '#818cf8',
              opacity: 0.4,
              width: 1
          },
          move: {
              enable: true,
              speed: 1,
              direction: 'none',
              random: false,
              straight: false,
              outModes: { default: 'bounce' },
              attract: { enable: false, rotateX: 600, rotateY: 1200 }
          }
      },
      interactivity: {
          detectsOn: 'canvas',
          events: {
              onHover: { enable: true, mode: 'repulse' },
              onClick: { enable: true, mode: 'push' },
              resize: true
          },
          modes: {
              repulse: { distance: 100, duration: 0.4 },
              push: { particles_nb: 4 }
          }
      },
      retina_detect: true
  });
});

// Intersection Observer for Animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
      if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          observer.unobserve(entry.target);
      }
  });
}, observerOptions);

document.querySelectorAll('.section').forEach(section => {
  observer.observe(section);
});

// Smooth scrolling for navigation links
document.querySelectorAll("nav ul li a").forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
      e.preventDefault();
      document.querySelector(this.getAttribute("href")).scrollIntoView({
          behavior: "smooth",
      });
  });
});

// Form submission handling
document.getElementById('contact-form').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const form = this;
  const formMessage = document.getElementById('form-message');
  const submitButton = form.querySelector('button[type="submit"]');
  const btnText = submitButton.querySelector('.btn-text');
  const spinner = submitButton.querySelector('.spinner');

  // Show loading state
  submitButton.disabled = true;
  btnText.classList.add('hidden');
  spinner.classList.remove('hidden');
  formMessage.classList.add('hidden');

  const formData = new FormData(form);

  fetch('send_email.php', {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      formMessage.textContent = data.message;
      formMessage.classList.remove('hidden', 'error');
      formMessage.classList.add(data.success ? 'success' : 'error');
      
      if (data.success) {
          form.reset();
      }
  })
  .catch(error => {
      formMessage.textContent = 'An error occurred. Please try again.';
      formMessage.classList.remove('hidden');
      formMessage.classList.add('error');
  })
  .finally(() => {
      // Reset button state
      submitButton.disabled = false;
      btnText.classList.remove('hidden');
      spinner.classList.add('hidden');
      
      // Hide message after 5 seconds
      setTimeout(() => {
          formMessage.classList.add('hidden');
      }, 5000);
  });
});

// Simple email validation function
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}