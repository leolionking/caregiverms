import '../../scss/public.scss';
import { initializeClockSystem } from './modules/clock-system';
import { initializeAvailabilityManager } from './modules/availability-manager';
import { initializeScheduleViewer } from './modules/schedule-viewer';

document.addEventListener('DOMContentLoaded', () => {
  initializeClockSystem();
  initializeAvailabilityManager();
  initializeScheduleViewer();
});