export function createPreviewTable(data, container) {
    if (!container || !data || data.length === 0) return;

    const headers = Object.keys(data[0]);
    const previewData = data.slice(0, 5); // Show first 5 rows

    const table = document.createElement('table');
    table.className = 'wp-list-table widefat fixed striped';

    // Create header row
    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    headers.forEach(header => {
        const th = document.createElement('th');
        th.textContent = header;
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    // Create data rows
    const tbody = document.createElement('tbody');
    previewData.forEach(row => {
        const tr = document.createElement('tr');
        headers.forEach(header => {
            const td = document.createElement('td');
            td.textContent = row[header] || '';
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
    table.appendChild(tbody);

    // Add preview message if there are more rows
    if (data.length > 5) {
        const notice = document.createElement('div');
        notice.className = 'notice notice-info';
        notice.innerHTML = `<p>Showing first 5 of ${data.length} records</p>`;
        container.appendChild(notice);
    }

    container.innerHTML = '';
    container.appendChild(table);
}