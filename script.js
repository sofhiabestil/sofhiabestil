document.addEventListener('DOMContentLoaded', function () {
  // Insert current year
  const year = document.getElementById('year');
  if (year) year.textContent = new Date().getFullYear();

  // Smooth scroll for internal links
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', (e) => {
      const target = a.getAttribute('href');
      if (target && target.startsWith('#') && target.length>1) {
        e.preventDefault();
        const el = document.querySelector(target);
        if (el) el.scrollIntoView({behavior:'smooth',block:'start'});
      }
    });
  });

  // Contact form submit via AJAX
  const form = document.getElementById('contactForm');
  const alertBox = document.getElementById('formAlert');
  const submitBtn = document.getElementById('submitBtn');
  const messageField = document.getElementById('message');
  const messageCount = document.getElementById('messageCount');
  if (messageField && messageCount) {
    const updateMessageCount = function () {
      messageCount.textContent = String(messageField.value.length);
    };
    updateMessageCount();
    messageField.addEventListener('input', updateMessageCount);
  }

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      if (!form.checkValidity()) {
        showAlert('Please fill all required fields.', 'danger');
        return;
      }

      const fd = new FormData(form);
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
      }

      try {
        const res = await fetch(form.action, { method: 'POST', body: fd });
        const text = await res.text();
        let json = {};
        try {
          json = JSON.parse(text);
        } catch (parseErr) {
          json = {};
        }

        if (json.success) {
          showAlert(json.message || 'Message sent.', 'success');
          form.reset();
          if (messageCount) messageCount.textContent = '0';
        } else {
          showAlert(json.message || 'Submission failed.', 'danger');
        }
      } catch (err) {
        showAlert('Network error. Try again later.', 'danger');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send Message';
        }
      }
    });
  }

  // Project filters
  const filterButtons = document.querySelectorAll('[data-project-filter]');
  const projectItems = document.querySelectorAll('.project-item');
  const projectEmpty = document.getElementById('projectEmpty');
  if (filterButtons.length && projectItems.length) {
    filterButtons.forEach((button) => {
      button.addEventListener('click', function () {
        const filter = button.getAttribute('data-project-filter');

        filterButtons.forEach((btn) => btn.classList.remove('is-active'));
        button.classList.add('is-active');

        let visibleCount = 0;
        projectItems.forEach((item) => {
          const category = item.getAttribute('data-category');
          const showItem = filter === 'all' || category === filter;
          item.classList.toggle('is-hidden', !showItem);
          if (showItem) visibleCount += 1;
        });

        if (projectEmpty) {
          projectEmpty.classList.toggle('d-none', visibleCount !== 0);
        }
      });
    });
  }

  function showAlert(msg, type='info'){
    if (!alertBox) return;
    alertBox.innerHTML = `<div class="alert alert-${type}" role="alert">${escapeHtml(msg)}</div>`;
  }

  function escapeHtml(unsafe){
    return String(unsafe).replace(/[&<>"]/g, function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]});
  }
});
