import { formatDate } from '../../../../utils/date-helpers';
import { showModal } from '../../../../utils/modal-helpers';

export function initializeScheduleGrid() {
  const grid = document.querySelector('.cms-schedule-grid');
  if (!grid) return;

  grid.addEventListener('click', e => {
    const assignButton = e.target.closest('.cms-assign-caregiver');
    if (!assignButton) return;

    const cell = assignButton.closest('.cms-schedule-cell');
    const day = cell.dataset.day;
    const shift = cell.dataset.shift;

    showAssignmentModal(day, shift);
  });

  loadScheduleData();
}

function loadScheduleData() {
  const week = document.getElementById('week').value;
  const year = document.getElementById('year').value;

  fetch(`${ajaxurl}?action=cms_get_schedule&week=${week}&year=${year}&_wpnonce=${cmsSchedule.nonce}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        updateScheduleGrid(data.data);
      }
    })
    .catch(error => console.error('Error loading schedule:', error));
}

function updateScheduleGrid(scheduleData) {
  const cells = document.querySelectorAll('.cms-schedule-cell');
  cells.forEach(cell => {
    const day = cell.dataset.day;
    const shift = cell.dataset.shift;
    const assignments = scheduleData[day]?.[shift] || [];
    
    const assignedCaregiversDiv = cell.querySelector('.cms-assigned-caregivers');
    assignedCaregiversDiv.innerHTML = assignments
      .map(caregiver => `
        <div class="cms-assigned-caregiver">
          ${caregiver.name}
          <button type="button" class="cms-remove-assignment" data-id="${caregiver.id}">×</button>
        </div>
      `)
      .join('');
  });
}

function showAssignmentModal(day, shift) {
  const modal = document.getElementById('cms-assignment-modal');
  modal.dataset.day = day;
  modal.dataset.shift = shift;
  showModal(modal);
}