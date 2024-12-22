export function initializeClockSystem() {
    const clockButton = document.querySelector('.cms-clock-button');
    if (!clockButton) return;
  
    clockButton.addEventListener('click', handleClockAction);
  }
  
  function handleClockAction(e) {
    e.preventDefault();
    const button = e.target;
    const action = button.dataset.action;
  
    if ("geolocation" in navigator) {
      navigator.geolocation.getCurrentPosition(
        position => submitClockAction(action, position.coords),
        error => alert('Please enable location services to clock in/out.')
      );
    } else {
      alert('Your browser doesn\'t support geolocation.');
    }
  }
  
  function submitClockAction(action, coords) {
    fetch(ajaxurl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=cms_clock_action&clock_action=${action}&lat=${coords.latitude}&lng=${coords.longitude}&_wpnonce=${cmsPublic.nonce}`
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert(data.data.message);
        }
      })
      .catch(error => console.error('Error submitting clock action:', error));
  }