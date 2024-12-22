jQuery(document).ready(function($) {
    // Handle form submissions
    $('.cms-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var formData = new FormData(this);
        formData.append('action', 'cms_add_caregiver');
        formData.append('nonce', cmsAjax.nonce);

          // Add selected skills with their levels
          form.find('input[name="skills[]"]:checked').each(function() {
            var skillId = $(this).val();
            var level = form.find(`select[name="skill_level_${skillId}"]`).val();
            formData.append(`skill_levels[${skillId}]`, level);
        });

        // Add selected languages with their levels
        form.find('input[name="languages[]"]:checked').each(function() {
            var langId = $(this).val();
            var level = form.find(`select[name="language_level_${langId}"]`).val();
            formData.append(`language_levels[${langId}]`, level);
        });
        
        submitButton.prop('disabled', true);
        var seeData = $(this).serialize();
        console.log('Form Data:', seeData);
        
        $.ajax({
            url: cmsAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', cmsAjax.nonce);
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    }
                } else {
                    alert(response.data.message || 'An error occurred');
                }
            },
            error: function() {
                alert('An error occurred while processing your request-Please try again>>adminjs');
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });

    // Location dropdown handling
    $('#province').on('change', function() {
        var province = $(this).val();
        updateStateDropdown(province);
    });

    $('#state').on('change', function() {
        var state = $(this).val();
        updateCityDropdown(state);
    });

    function updateStateDropdown(province) {
        $.ajax({
            url: cmsAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'cms_get_cities',
                province: province,
                nonce: cmsAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var stateSelect = $('#city');
                    stateSelect.empty();
                    stateSelect.append('<option value="">Select City</option>');
                    
                    $.each(response.data, function(id, name) {
                        stateSelect.append($('<option></option>')
                            .attr('value', id)
                            .text(name));
                    });
                }
            }
        });
    }

    function updateCityDropdown(state) {
        $.ajax({
            url: cmsAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'cms_get_cities',
                state: state,
                nonce: cmsAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    var citySelect = $('#city');
                    citySelect.empty();
                    citySelect.append('<option value="">Select City</option>');
                    
                    $.each(response.data, function(id, name) {
                        citySelect.append($('<option></option>')
                            .attr('value', id)
                            .text(name));
                    });
                }
            }
        });
    }

         // Handle skill checkbox changes
    $('.cms-skill-item input[type="checkbox"]').on('change', function() {
        var levelSelect = $(this).closest('.cms-skill-item').find('select');
        levelSelect.prop('disabled', !this.checked);
    });

    // Handle language checkbox changes
    $('.cms-language-item input[type="checkbox"]').on('change', function() {
        var levelSelect = $(this).closest('.cms-language-item').find('select');
        levelSelect.prop('disabled', !this.checked);
    });

    // Initialize all level selects as disabled
    $('.cms-skill-level, .cms-language-level').prop('disabled', true);




});