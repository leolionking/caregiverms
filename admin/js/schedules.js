document.addEventListener('DOMContentLoaded', function () {
    initializeScheduleManager();
});

function initializeScheduleManager() {
    // Debug check for essential variables
    console.log('Initialization variables:', {
        cmsSchedule: !!cmsSchedule,
        ajaxurl: cmsSchedule?.ajaxurl,
        nonce: cmsSchedule?.nonce
    });

    if (!cmsSchedule || !cmsSchedule.ajaxurl || !cmsSchedule.nonce) {
        console.error("Missing essential variables from cmsSchedule");
        return;
    }

    // Populate assigned caregivers on page load
    document.querySelectorAll('.cms-schedule-cell').forEach(cell => {
        const day = cell.dataset.day;
        const shift = cell.dataset.shift;
        fetchAssignedCaregivers(day, shift);
    });

    // Populate caregiver options in the modal
    fetchCaregiverOptions();

    // Attach modal event listeners once
    setupModalEventListeners();

    // Handle assign caregiver button click
    document.querySelectorAll('.cms-assign-caregiver').forEach(button => {
        button.addEventListener('click', function () {
            const day = this.parentElement.dataset.day;
            const shift = this.parentElement.dataset.shift;

            // Show the modal
            const modal = document.getElementById('cms-assignment-modal');
            if (!modal) {
                console.error("Modal element with ID 'cms-assignment-modal' not found");
                return;
            }
            modal.style.display = 'block';

            // Store the day and shift in the modal for later use
            modal.dataset.day = day;
            modal.dataset.shift = shift;
        });
    });
}

function setupModalEventListeners() {
    const modal = document.getElementById('cms-assignment-modal');
    if (!modal) {
        console.error("Modal element with ID 'cms-assignment-modal' not found");
        return;
    }

    const confirmButton = document.getElementById('confirm-assignment');
    const cancelButton = document.getElementById('cancel-assignment');

    if (confirmButton) {
        confirmButton.addEventListener('click', () => {
            const caregiverSelect = document.getElementById('caregiver-select');
            if (!caregiverSelect) {
                console.error("Caregiver select element not found");
                return;
            }

            const caregiverId = caregiverSelect.value;
            const day = modal.dataset.day;
            const shift = modal.dataset.shift;

            assignCaregiver(day, shift, caregiverId);
            modal.style.display = 'none';
        });
    } else {
        console.error("Confirm button not found");
    }

    if (cancelButton) {
        cancelButton.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    } else {
        console.error("Cancel button not found");
    }
}

/**
 * Convert a day of the week (e.g., "monday") to the corresponding date in the current week.
 *
 * @param {string} day - The day of the week (e.g., "monday", "tuesday").
 * @returns {string} - The date in 'yyyy/mm/dd' format.
 */
function convertDayToDate(day) {
    // Map the day input to a weekday number (0 = Sunday, 1 = Monday, etc.)
    const dayMap = {
        sunday: 0,
        monday: 1,
        tuesday: 2,
        wednesday: 3,
        thursday: 4,
        friday: 5,
        saturday: 6
    };

    // Get the current date
    const today = new Date();

    // Get the current weekday number (0 = Sunday, 1 = Monday, etc.)
    const currentWeekday = today.getDay();

    // Get the target weekday number
    const targetWeekday = dayMap[day.toLowerCase()] ?? null;

    if (targetWeekday === null) {
        throw new Error(`Invalid day_1: ${day}`);
    }

    // Calculate the difference in days between the current day and the target day
    const dayDifference = targetWeekday - currentWeekday;

    // Add the difference to the current date
    const targetDate = new Date(today);
    targetDate.setDate(today.getDate() + dayDifference);

    // Format the date as 'yyyy/mm/dd'
    const year = targetDate.getFullYear();
    const month = String(targetDate.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const date = String(targetDate.getDate()).padStart(2, '0');

    return `${year}/${month}/${date}`;
}

function fetchAssignedCaregivers(day, shift) {
    // Debug logging
    console.log('Request payload:', {
        day,
        shift,
        nonce: cmsSchedule.nonce,
        ajaxurl: cmsSchedule.ajaxurl
    });
    const date = convertDayToDate(day);
    const data = new FormData();
    data.append('action', 'cms_get_assigned_caregivers');
    data.append('day', date);  // Send the date, not the day
    data.append('shift', shift);
    data.append('nonce', cmsSchedule.nonce);

    console.log('Sending data:', {
        action: data.get('action'),
        day: data.get('day'),
        shift: data.get('shift'),
        nonce: data.get('nonce'),
        ajaxurl: cmsSchedule.ajaxurl
    });

    fetch(cmsSchedule.ajaxurl, {
        method: 'POST',
        dataType: 'json',
        body: data,
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(response => {
        console.log('AJAX Response:', response);

        if (response.success) {
            const caregivers = response.data.caregivers;
            const cell = document.querySelector(`.cms-schedule-cell[data-day="${day}"][data-shift="${shift}"] .cms-assigned-caregivers`);
            
            if (!cell) {
                console.error(`Cell for day "${day}" and shift "${shift}" not found.`);
                return;
            }

            cell.innerHTML = '';

            if (caregivers.length === 0) {
                const noCaregiversMessage = document.createElement('div');
                noCaregiversMessage.classList.add('cms-no-caregivers');
                noCaregiversMessage.textContent = 'No caregivers assigned for this shift.';
                cell.appendChild(noCaregiversMessage);
                console.log('Caregiver:', caregivers.name);
            } else {
                caregivers.forEach(caregiver => {
                    const caregiverElement = document.createElement('div');
                    caregiverElement.classList.add('cms-caregiver');
                    caregiverElement.textContent = caregiver.name;
                    cell.appendChild(caregiverElement);
                    console.log('Caregiver:', caregiver.name);
                });
            }
        } else {
            console.error('Error:', response.data?.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        console.error('AJAX error:', error);
    });
}

function fetchCaregiverOptions() {
    const data = new FormData();
    data.append('action', 'cms_get_caregivers');
    data.append('nonce', cmsSchedule.nonce);

    fetch(cmsSchedule.ajaxurl, {
        method: 'POST',
        body: data,
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(response => {
        if (response.success) {
            const caregivers = response.data.caregivers;
            const select = document.getElementById('caregiver-select');
            if (!select) {
                console.error('Caregiver select element not found');
                return;
            }
            select.innerHTML = '';

            caregivers.forEach(caregiver => {
                const option = document.createElement('option');
                option.value = caregiver.id;
                option.textContent = caregiver.name;
                select.appendChild(option);
            });
        } else {
            console.error('Error:', response.data?.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        console.error('AJAX error:', error);
    });
}

function assignCaregiver(day, shift, caregiverId) {
    const data = new FormData();
    data.append('action', 'cms_assign_caregiver');
    data.append('day', day);
    data.append('shift', shift);
    data.append('caregiver_id', caregiverId);
    data.append('nonce', cmsSchedule.nonce);

    console.log('Assigning caregiver with data:', {
        day,
        shift,
        caregiverId,
        nonce: cmsSchedule.nonce,
        ajaxurl: cmsSchedule.ajaxurl
    });

    fetch(cmsSchedule.ajaxurl, {
        method: 'POST',
        body: data
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(response => {
        if (response.success) {
            fetchAssignedCaregivers(day, shift);
        } else {
            console.error('Error:', response.data?.message || 'Unknown error occurred');
            alert('Failed to assign caregiver. Please try again.');
        }
    })
    .catch(error => {
        console.error('AJAX error:', error);
        alert('Failed to assign caregiver. Please try again.');
    });
}
