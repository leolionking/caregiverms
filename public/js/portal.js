jQuery(document).ready(function($) {
    // Clock In/Out functionality
    $('.cms-clock-button').on('click', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const action = button.data('action');
        
        if ("geolocation" in navigator) {
            button.prop('disabled', true).addClass('updating');
            
            navigator.geolocation.getCurrentPosition(
                position => submitClockAction(action, position.coords, button),
                error => {
                    alert('Please enable location services to clock in/out.');
                    button.prop('disabled', false).removeClass('updating');
                }
            );
        } else {
            alert('Your browser doesn\'t support geolocation.');
        }
    });

    function submitClockAction(action, coords, button) {
        $.ajax({
            url: cmsAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'cms_clock_action',
                clock_action: action,
                lat: coords.latitude,
                lng: coords.longitude,
                nonce: cmsAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'An error occurred');
                    button.prop('disabled', false).removeClass('updating');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                button.prop('disabled', false).removeClass('updating');
            }
        });
    }

    // Auto-refresh portal data
    function refreshPortalData() {
        $.ajax({
            url: cmsAjax.ajaxurl,
            type: 'GET',
            data: {
                action: 'cms_get_portal_data',
                nonce: cmsAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePortalUI(response.data);
                }
            }
        });
    }

    function updatePortalUI(data) {
        // Update clock status
        if (data.clock_status) {
            $('.cms-clock-status').replaceWith(data.clock_status);
        }

        // Update upcoming shifts
        if (data.upcoming_shifts) {
            $('.cms-upcoming-shifts').html(data.upcoming_shifts);
        }
    }

    // Refresh portal data every 5 minutes
    setInterval(refreshPortalData, 300000);
});