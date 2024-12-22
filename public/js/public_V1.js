document.addEventListener('DOMContentLoaded', function () {
    // Ensure cmsPublic is defined and accessible
    if (!cmsPublic || !cmsPublic.ajaxurl || !cmsPublic.nonce) {
        console.error("Missing essential variables from cmsPublic");
        return;
    }

    // Clock In/Out functionality
    document.querySelectorAll('.cms-clock-button').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const $button = this;
            const action = $button.dataset.action;

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        console.log('Geolocation success:', position.coords);
                        submitClockAction(action, position.coords);
                    },
                    function (error) {
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

        // Create a FormData object
        const formData = new FormData();
        formData.append('action', 'cms_clock_action');
        formData.append('clock_action', action);
        formData.append('lat', coords.latitude);
        formData.append('lng', coords.longitude);
        formData.append('nonce', cmsPublic.nonce);

         // Log the FormData content
         for (let pair of formData.entries()) {
            console.log(pair[0] + ':', pair[1]);
        }


        // Send the request using XMLHttpRequest
        const xhr = new XMLHttpRequest();
        xhr.open('POST', cmsPublic.ajaxurl, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    console.log('AJAX success:', response);
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'An error occurred');
                    }
                } else {
                    console.error('AJAX error:', xhr.status, xhr.statusText);
                    alert('An error occurred. Please try again later.');
                }
            }
        };
        xhr.send(formData);
    }

    // Availability toggle functionality
    document.querySelectorAll('.cms-shift-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const $toggle = this;
            const day = $toggle.dataset.day;
            const shift = $toggle.dataset.shift;

            // Create a FormData object
            const formData = new FormData();
            formData.append('action', 'cms_toggle_availability');
            formData.append('day', day);
            formData.append('shift', shift);
            formData.append('nonce', cmsPublic.nonce);

            // Send the request using XMLHttpRequest
            const xhr = new XMLHttpRequest();
            xhr.open('POST', cmsPublic.ajaxurl, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            $toggle.classList.toggle('available');
                        } else {
                            alert(response.data.message || 'An error occurred');
                        }
                    } else {
                        console.error('AJAX error:', xhr.status, xhr.statusText);
                        alert('AJAX request failed: ' + xhr.statusText);
                    }
                }
            };
            xhr.send(formData);
        });
    });
});