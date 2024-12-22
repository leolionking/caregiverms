import { formatDate } from '../../../../utils/date-helpers';

export function initializeScheduleViewer() {
  loadUpcomingShifts();
}

function loadUpcomingShifts() {
  const shiftsList = document.querySelector('.cms-upcoming-shifts');
  if (!shiftsList) return;

  fetch(`${ajaxurl}?action=cms_get_upcoming_shifts&_wpnonce=${cmsSchedule.nonce}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        updateShiftsList(data.data);
      }
    })
    .catch(error => console.error('Error loading shifts:', error));
}

function updateShiftsList(shifts) {
  const shiftsList = document.querySelector('.cms-upcoming-shifts');
  
  shiftsList.innerHTML = shifts.map(shift => `
    <li class="cms-upcoming-shift">
      <span class="cms-shift-date">${formatDate(shift.shift_date)}</span>
      <span class="cms-shift-type ${shift.shift_type}">${shift.shift_type}</span>
    </li>
  `).join('');
}