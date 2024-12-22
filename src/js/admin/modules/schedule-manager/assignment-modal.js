import { hideModal } from '../../../../utils/modal-helpers';

export function initializeAssignmentModal() {
  const modal = document.getElementById('cms-assignment-modal');
  if (!modal) return;

  const cancelButton = document.getElementById('cancel-assignment');
  const confirmButton = document.getElementById('confirm-assignment');

  cancelButton?.addEventListener('click', () => hideModal(modal));
  confirmButton?.addEventListener('click', handleAssignment);

  modal.addEventListener('show', loadAvailableCaregivers);
}

function loadAvailableCaregivers() {
  const modal = document.getElementById('cms-assignment-modal');
  const select = document.getElementById('caregiver-select');
  const day = modal.dataset.day;
  const shift = modal.dataset.shift;

  fetch(`${ajaxurl}?action=cms_get_available_caregivers&day=${day}&shift=${shift}&_wpnonce=${cmsSchedule.nonce}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        select.innerHTML = data.data
          .map(caregiver => `
            <option value="${caregiver.id}">${caregiver.name}</option>
          `)
          .join('');
      }
    })
    .catch(error => console.error('Error loading caregivers:', error));
}

function handleAssignment() {
  const modal = document.getElementById('cms-assignment-modal');
  const select = document.getElementById('caregiver-select');
  
  const caregiverId = select.value;
  const day = modal.dataset.day;
  const shift = modal.dataset.shift;

  fetch(`${ajaxurl}?action=cms_assign_caregiver`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `caregiver_id=${caregiverId}&day=${day}&shift=${shift}&_wpnonce=${cmsSchedule.nonce}`,
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert(data.data.message);
      }
    })
    .catch(error => console.error('Error assigning caregiver:', error));
}