export function initializeCaregiverManager() {
    const form = document.getElementById('add-caregiver-form');
    if (!form) return;
  
    form.addEventListener('submit', handleCaregiverSubmit);
  }
  
  function handleCaregiverSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
  
    fetch(ajaxurl, {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = data.data.redirect;
        } else {
          alert(data.data.message);
        }
      })
      .catch(error => console.error('Error adding caregiver:', error));
  }