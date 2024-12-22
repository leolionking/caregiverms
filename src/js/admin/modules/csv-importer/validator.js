export function validateCsvData(data) {
    const errors = [];
    let validRecords = 0;

    // Check if data is empty
    if (!data || data.length === 0) {
        return {
            isValid: false,
            errors: ['No data found in CSV file'],
            validRecords: 0
        };
    }

    // Validate headers
    const requiredHeaders = [
        'First Name',
        'Last Name',
        'Email',
        'Qualification',
        'Province',
        'State',
        'City',
        'Hourly Rate'
    ];

    const headers = Object.keys(data[0]);
    const missingHeaders = requiredHeaders.filter(
        header => !headers.includes(header)
    );

    if (missingHeaders.length > 0) {
        errors.push(`Missing required columns: ${missingHeaders.join(', ')}`);
        return {
            isValid: false,
            errors,
            validRecords: 0
        };
    }

    // Validate each row
    data.forEach((row, index) => {
        const rowErrors = validateRow(row, index + 1);
        if (rowErrors.length > 0) {
            errors.push(...rowErrors);
        } else {
            validRecords++;
        }
    });

    return {
        isValid: errors.length === 0,
        errors,
        validRecords
    };
}

function validateRow(row, rowNumber) {
    const errors = [];

    // Validate required fields
    if (!row['First Name']?.trim()) {
        errors.push(`Row ${rowNumber}: First Name is required`);
    }
    if (!row['Last Name']?.trim()) {
        errors.push(`Row ${rowNumber}: Last Name is required`);
    }
    if (!row['Email']?.trim()) {
        errors.push(`Row ${rowNumber}: Email is required`);
    } else if (!isValidEmail(row['Email'])) {
        errors.push(`Row ${rowNumber}: Invalid email format`);
    }
    if (!row['Hourly Rate']?.trim()) {
        errors.push(`Row ${rowNumber}: Hourly Rate is required`);
    } else if (isNaN(row['Hourly Rate']) || parseFloat(row['Hourly Rate']) <= 0) {
        errors.push(`Row ${rowNumber}: Hourly Rate must be a positive number`);
    }

    return errors;
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}