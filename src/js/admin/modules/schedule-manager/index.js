import { initializeScheduleGrid } from './schedule-grid';
import { initializeScheduleControls } from './schedule-controls';
import { initializeAssignmentModal } from './assignment-modal';

export function initializeScheduleManager() {
  initializeScheduleGrid();
  initializeScheduleControls();
  initializeAssignmentModal();
}