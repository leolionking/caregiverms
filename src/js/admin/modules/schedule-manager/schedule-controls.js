import { exportToExcel } from '../../../../utils/export-helpers';

export function initializeScheduleControls() {
  const generateButton = document.getElementById('generate-schedule');
  const exportButton = document.getElementById('export-schedule');

  if (generateButton) {
    generateButton.addEventListener('click', handleGenerateSchedule);
  }

  if (exportButton) {
    exportButton.addEventListener('click', handleExportSchedule);
  }
}

function handleGenerateSchedule() {
  const week = document.getElementById('week').value;
  const year = document.getElementById('year').value;

  fetch(`${ajaxurl}?action=cms_generate_schedule`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `week=${week}&year=${year}&_wpnonce=${cmsSchedule.nonce}`,
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert(data.data.message);
      }
    })
    .catch(error => console.error('Error generating schedule:', error));
}

function handleExportSchedule() {
  const week = document.getElementById('week').value;
  const year = document.getElementById('year').value;

  fetch(`${ajaxurl}?action=cms_get_schedule&week=${week}&year=${year}&_wpnonce=${cmsSchedule.nonce}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        exportToExcel(data.data, `schedule-week-${week}-${year}`);
      }
    })
    .catch(error => console.error('Error exporting schedule:', error));
}