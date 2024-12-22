jQuery(document).ready(function($) {
    // Ensure cmsPublic is defined and accessible
    if (!cmsPublic || !cmsPublic.ajaxurl || !cmsPublic.nonce) {
        console.error("Missing essential variables from cmsPublic");
        return;
    }

    // Clock In/Out functionality
    $('.cms-clock-button').on('click', function(e) {
        e.preventDefault();

        var $button = $(this);
        var action = $button.data('action');

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    console.log('Geolocation success:', position.coords);
                    submitClockAction(action, position.coords);
                },
                function(error) {
                    console.error('Geolocation error:', error);
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            alert('Please enable location services to clock in/out.');
                            break;
                        case error.POSITION_UNAVAILABLE:
                            alert('Location information is unavailable.');
                            break;
                        case error.TIMEOUT:
                            alert('The request to get location timed out.');
                            break;
                        default:
                            alert('An unknown geolocation error occurred.');
                            break;
                    }
                },
                {
                    timeout: 10000, // Set a timeout for geolocation request
                    maximumAge: 60000 // Cache the location for 60 seconds
                }
            );
        } else {
            console.error('Geolocation not supported');
            alert('Your browser doesn\'t support geolocation.');
        }
    });

    function submitClockAction(action, coords) {
        console.log('Submitting clock action:', action, coords);

        if (!cmsPublic || !cmsPublic.ajaxurl) {
            console.error('AJAX URL is missing');
            return;
        }

        if (!coords || isNaN(coords.latitude) || isNaN(coords.longitude)) {
            console.error('Invalid coordinates provided');
            alert('Invalid coordinates. Please try again.');
            return;
        }
        
        $.ajax({
            url: cmsPublic.ajaxurl,
            type: 'POST',
            data: {
                action: 'cms_clock_action',
                clock_action: action,
                lat: coords.latitude,
                lng: coords.longitude,
                nonce: cmsPublic.nonce 
            },
            success: function(response) {
                console.log('AJAX success:', response);
                if (response.success) {
                    alert(JSON.stringify(data));
                    location.reload();
                } else {
                    alert(response.data.message || 'An error occurred');
                }
            },
            /* $.ajax({
                url: cmsPublic.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cms_clock_action',
                     clock_action: 'clock_in',
                    lat: 40.1234,
                   lng: -74.1234,
                   nonce: cmsPublic.nonce
                 },
               success: function(response) {
                console.log('Test AJAX Success', response)
                  },
                error: function(xhr, status, error) {
                   console.log('Test AJAX Error', xhr, status, error);
                 }
              }); */
            error: function(xhr, status, error) {
                alert(JSON.stringify(error));
                console.log('Error callback triggered'); // Debugging log
                console.error('XHR object:', xhr.responseJSON); // Log the response text
                console.error('Status:', status); // Log the HTTP status
                console.error('Error:', error); // Log the error message

                // Check if the response is empty or invalid
                if (!xhr.responseText) {
                    console.error('Empty or invalid response from the server.');
                    alert('An unknown error occurred. Please try again later.');
                    return;
                }

                // Extract the error message from the response text
                let errorMessage = xhr.responseText || 'An unknown error occurred.';
                try {
                    // Attempt to parse the response as JSON
                    const responseData = JSON.parse(xhr.responseText);
                    if (responseData && responseData.message) {
                        errorMessage = responseData.message; // Use the server's error message
                    }
                } catch (e) {
                    // If parsing fails, use the raw response text
                    console.error('Failed to parse JSON response:', e);
                    errorMessage = xhr.responseText || 'An unknown error occurred.';
                }

                // Provide a more detailed error message to the user
                if (xhr.status === 0) {
                    alert('Network error. Please check your internet connection.');
                } else if (xhr.status === 403) {
                    alert('Forbidden access. Please ensure you are logged in.');
                } else if (xhr.status === 500) {
                    alert('Server error. Please try again later.');
                } else {
                    alert(errorMessage);
                }
            }
        });
    }

    // Availability toggle functionality
    $('.cms-shift-toggle').on('click', function() {
        var $toggle = $(this);
        var day = $toggle.data('day');
        var shift = $toggle.data('shift');

        $.ajax({
            url: cmsPublic.ajaxurl,
            type: 'POST',
            data: {
                action: 'cms_toggle_availability',
                day: day,
                shift: shift,
                nonce: cmsPublic.nonce
            },
            success: function(response) {
                if (response.success) {
                    $toggle.toggleClass('available');
                } else {
                    alert(response.data.message || 'An error occurred');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('AJAX request failed: ' + error);
            }
        });
    });
});