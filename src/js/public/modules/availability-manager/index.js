export function initializeAvailabilityManager() {
    const availabilityGrid = document.querySelector('.cms-availability-grid');
    if (!availabilityGrid) return;
  
    availabilityGrid.addEventListener('click', handleAvailabilityToggle);
  }
  
  function handleAvailabilityToggle(e) {
    const toggle = e.target.closest('.cms-shift-toggle');
    if (!toggle) return;
  
    const day = toggle.dataset.day;
    const shift = toggle.dataset.shift;
  
    fetch(ajaxurl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=cms_toggle_availability&day=${day}&shift=${shift}&_wpnonce=${cmsPublic.nonce}`
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          toggle.classList.toggle('available');
        } else {
          alert(data.data.message);
        }
      })
      .catch(error => console.error('Error toggling availability:', error));
  }