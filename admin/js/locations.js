document.addEventListener('DOMContentLoaded', function() {
    initializeLocationManager();
});

function initializeLocationManager() {
    // Add Province Form
    const addProvinceForm = document.getElementById('add-province-form');
    if (addProvinceForm) {
        addProvinceForm.addEventListener('submit', handleAddProvince);
    }

    // Add City Form
    const addCityForm = document.getElementById('add-city-form');
    if (addCityForm) {
        addCityForm.addEventListener('submit', handleAddCity);
    }

    // Import Locations Form
    const importForm = document.getElementById('import-locations-form');
    if (importForm) {
        importForm.addEventListener('submit', handleImportLocations);
    }

    // Delete buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.delete-province')) {
            handleDeleteProvince(e.target.dataset.id);
        } else if (e.target.matches('.delete-city')) {
            handleDeleteCity(e.target.dataset.id);
        }
    });
}

async function handleAddProvince(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('action', 'cms_add_province');

    try {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.data.message);
            location.reload();
        } else {
            alert(data.data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}

async function handleAddCity(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('action', 'cms_add_city');

    try {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.data.message);
            location.reload();
        } else {
            alert(data.data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}

async function handleDeleteProvince(id) {
    if (!confirm('Are you sure you want to delete this province? This will also delete all associated cities.')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'cms_delete_province');
    formData.append('id', id);
    formData.append('nonce', cmsAjax.nonce);

    try {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.data.message);
            location.reload();
        } else {
            alert(data.data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}

async function handleDeleteCity(id) {
    if (!confirm('Are you sure you want to delete this city?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'cms_delete_city');
    formData.append('id', id);
    formData.append('nonce', cmsAjax.nonce);

    try {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.data.message);
            location.reload();
        } else {
            alert(data.data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}

async function handleImportLocations(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('action', 'cms_import_locations');

    try {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.data.message);
            location.reload();
        } else {
            alert(data.data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}