export function initializeLocationManager() {
    const provinceSelect = document.getElementById('province');
    const stateSelect = document.getElementById('state');
    const citySelect = document.getElementById('city');
  
    if (!provinceSelect || !stateSelect || !citySelect) return;
  
    provinceSelect.addEventListener('change', () => {
      loadStates(provinceSelect.value);
    });
  
    stateSelect.addEventListener('change', () => {
      loadCities(stateSelect.value);
    });
  }
  
  function loadStates(province) {
    fetch(`${ajaxurl}?action=cms_get_states&province=${province}&_wpnonce=${cmsAjax.nonce}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          updateSelect('state', data.data);
          document.getElementById('city').innerHTML = '<option value="">Select City</option>';
        }
      })
      .catch(error => console.error('Error loading states:', error));
  }
  
  function loadCities(state) {
    fetch(`${ajaxurl}?action=cms_get_cities&state=${state}&_wpnonce=${cmsAjax.nonce}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          updateSelect('city', data.data);
        }
      })
      .catch(error => console.error('Error loading cities:', error));
  }
  
  function updateSelect(id, options) {
    const select = document.getElementById(id);
    select.innerHTML = `<option value="">Select ${id.charAt(0).toUpperCase() + id.slice(1)}</option>` +
      options.map(option => `<option value="${option.id}">${option.name}</option>`).join('');
  }