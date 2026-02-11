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
  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      if (!form.checkValidity()) {
        showAlert('Please fill all required fields.', 'danger');
        return;
      }
      const fd = new FormData(form);
      try {
        const res = await fetch(form.action, {method:'POST', body:fd});
        const json = await res.json();
        if (json.success) {
          showAlert(json.message || 'Message sent.', 'success');
          form.reset();
        } else {
          showAlert(json.message || 'Submission failed.', 'danger');
        }
      } catch (err) {
        showAlert('Network error. Try again later.', 'danger');
      }
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
