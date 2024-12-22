jQuery(document).ready(function($) {
    const provinceSelect = $('#province');
    const citySelect = $('#city');

    // Handle province change
    provinceSelect.on('change', function() {
        const provinceId = $(this).val();
        updateCityDropdown(provinceId);
    });

    function updateCityDropdown(provinceId) {
        if (!provinceId) {
            citySelect.html('<option value="">Select City</option>');
            citySelect.prop('disabled', true);
            return;
        }

        citySelect.prop('disabled', true);

        $.ajax({
            url: cmsAjax.ajaxurl,
            type: 'GET',
            data: {
                action: 'cms_get_cities',
                province_id: provinceId,
                nonce: cmsAjax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select City</option>';
                    response.data.forEach(function(city) {
                        options += `<option value="${city.id}">${city.name}</option>`;
                    });
                    citySelect.html(options);
                    citySelect.prop('disabled', false);
                } else {
                    console.error('Error loading cities:', response);
                    alert('Error loading cities. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error loading cities. Please try again!!!');
            }
        });
    }

    // Initialize city dropdown as disabled
    citySelect.prop('disabled', true);
});