import '../../scss/admin.scss';
import { initializeLocationManager } from './modules/location-manager';
import { initializeCaregiverManager } from './modules/caregiver-manager';
import { initializeScheduleManager } from './modules/schedule-manager';
import { initializePayrollManager } from './modules/payroll-manager';

document.addEventListener('DOMContentLoaded', () => {
  initializeLocationManager();
  initializeCaregiverManager();
  initializeScheduleManager();
  initializePayrollManager();
});