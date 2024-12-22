import { formatDate, formatTime } from '../../../../utils/date-helpers';
import { showModal, hideModal } from '../../../../utils/modal-helpers';
import { exportToExcel } from '../../../../utils/export-helpers';

export function initializePayrollManager() {
  const exportButton = document.getElementById('export-payroll');
  if (exportButton) {
    exportButton.addEventListener('click', handleExportPayroll);
  }

  initializeTimesheetModal();
  loadPayrollData();
}

function loadPayrollData() {
  const startDate = document.getElementById('start_date')?.value;
  const endDate = document.getElementById('end_date')?.value;
  
  if (!startDate || !endDate) return;

  fetch(`${ajaxurl}?action=cms_get_payroll&start_date=${startDate}&end_date=${endDate}&_wpnonce=${cmsAjax.nonce}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        updatePayrollTable(data.data);
      }
    })
    .catch(error => console.error('Error loading payroll:', error));
}

function updatePayrollTable(payrollData) {
  const tbody = document.getElementById('payroll-data');
  if (!tbody) return;

  tbody.innerHTML = payrollData.map(entry => `
    <tr>
      <td>${entry.caregiver_name}</td>
      <td>${entry.qualification}</td>
      <td>${entry.total_hours}</td>
      <td>$${entry.hourly_rate}</td>
      <td>$${entry.total_amount}</td>
      <td>
        <button type="button" class="button view-timesheet" data-caregiver="${entry.caregiver_id}">
          View Timesheet
        </button>
      </td>
    </tr>
  `).join('');
}

function initializeTimesheetModal() {
  const modal = document.getElementById('cms-timesheet-modal');
  if (!modal) return;

  document.getElementById('close-timesheet')?.addEventListener('click', () => hideModal(modal));

  document.getElementById('payroll-data')?.addEventListener('click', e => {
    const viewButton = e.target.closest('.view-timesheet');
    if (!viewButton) return;

    const caregiverId = viewButton.dataset.caregiver;
    showTimesheetDetails(caregiverId);
  });
}

function showTimesheetDetails(caregiverId) {
  const startDate = document.getElementById('start_date').value;
  const endDate = document.getElementById('end_date').value;

  fetch(`${ajaxurl}?action=cms_get_timesheet&caregiver_id=${caregiverId}&start_date=${startDate}&end_date=${endDate}&_wpnonce=${cmsAjax.nonce}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        updateTimesheetModal(data.data);
        showModal(document.getElementById('cms-timesheet-modal'));
      }
    })
    .catch(error => console.error('Error loading timesheet:', error));
}

function updateTimesheetModal(timesheetData) {
  const tbody = document.getElementById('timesheet-details');
  if (!tbody) return;

  tbody.innerHTML = timesheetData.map(entry => `
    <tr>
      <td>${formatDate(entry.date)}</td>
      <td>${formatTime(entry.clock_in)}</td>
      <td>${formatTime(entry.clock_out)}</td>
      <td>${entry.hours}</td>
    </tr>
  `).join('');
}

function handleExportPayroll() {
  const startDate = document.getElementById('start_date').value;
  const endDate = document.getElementById('end_date').value;

  fetch(`${ajaxurl}?action=cms_get_payroll&start_date=${startDate}&end_date=${endDate}&_wpnonce=${cmsAjax.nonce}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        exportToExcel(data.data, `payroll-${startDate}-${endDate}`);
      }
    })
    .catch(error => console.error('Error exporting payroll:', error));
}