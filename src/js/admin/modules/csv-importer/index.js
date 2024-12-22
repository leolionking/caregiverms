import Papa from 'papaparse';
import { validateCsvData } from './validator';
import { createPreviewTable } from './preview';

export function initializeCsvImporter() {
    const form = document.getElementById('import-caregivers-form');
    const fileInput = document.getElementById('csv_file');
    const previewContainer = document.getElementById('preview-container');
    const validationResults = document.getElementById('validation-results');
    const importButton = document.getElementById('import-button');
    const templateButton = document.getElementById('download-template');

    if (!form) return;

    fileInput?.addEventListener('change', handleFileSelect);
    form?.addEventListener('submit', handleFormSubmit);
    templateButton?.addEventListener('click', downloadTemplate);
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;

    Papa.parse(file, {
        complete: function(results) {
            const validation = validateCsvData(results.data);
            displayValidationResults(validation);
            
            if (validation.isValid) {
                createPreviewTable(results.data, previewContainer);
                importButton.disabled = false;
            } else {
                importButton.disabled = true;
            }
        },
        header: true
    });
}

function displayValidationResults(validation) {
    const container = document.getElementById('validation-results');
    if (!container) return;

    if (validation.isValid) {
        container.innerHTML = `
            <div class="notice notice-success">
                <p>${validation.validRecords} records ready for import</p>
            </div>
        `;
    } else {
        container.innerHTML = `
            <div class="notice notice-error">
                <p>Validation errors found:</p>
                <ul>
                    ${validation.errors.map(error => `<li>${error}</li>`).join('')}
                </ul>
            </div>
        `;
    }
}

async function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(`Successfully imported ${data.data.imported} caregivers`);
            window.location.reload();
        } else {
            alert(data.data.message || 'Import failed');
        }
    } catch (error) {
        console.error('Import error:', error);
        alert('Import failed. Please try again.');
    }
}

function downloadTemplate(e) {
    e.preventDefault();
    
    const headers = [
        'First Name',
        'Last Name',
        'Email',
        'Qualification',
        'Province',
        'State',
        'City',
        'Hourly Rate'
    ];

    const csv = Papa.unparse([headers]);
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.href = url;
    a.download = 'caregivers-template.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}