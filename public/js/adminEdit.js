document.addEventListener('DOMContentLoaded', function() {
    // KLIK na bunku (nie dvojklik)
    document.addEventListener('click', function(e) {
        const cell = e.target.closest('.editable-cell');
        if (cell && !cell.classList.contains('editing')) {
            editCell(cell);
        }
    });

    function editCell(cell) {
        const id = cell.dataset.id;
        const field = cell.dataset.field;
        const type = cell.dataset.type || 'text';

        cell.classList.add('editing');
        const originalHTML = cell.innerHTML;

        // Vytvor input na zakade typu
        let input;
        if (type === 'select') {
            input = document.createElement('select');
            input.className = 'form-select form-select-sm';

            // status
            const statuses = [
                {value: 'pending', text: 'Čakajúca'},
                {value: 'completed', text: 'Dokončená'},
                {value: 'cancelled', text: 'Zrušená'}
            ];

            statuses.forEach(status => {
                const option = document.createElement('option');
                option.value = status.value;
                option.textContent = status.text;

                // aktualna hodnota
                const currentText = cell.textContent.trim().toLowerCase();
                if (currentText.includes(status.value) ||
                    currentText.includes(status.text.toLowerCase())) {
                    option.selected = true;
                }

                input.appendChild(option);
            });
        }
        else if (type === 'textarea') {
            input = document.createElement('textarea');
            input.className = 'form-control form-control-sm';
            input.rows = 2;
            input.value = cell.textContent.trim();
            input.maxLength = 70;
        }
        else if (type === 'datetime') {
            input = document.createElement('input');
            input.type = 'datetime-local';
            input.className = 'form-control form-control-sm';

            // konverzia datumu
            const text = cell.textContent.trim();
            if (text) {
                const date = new Date(text);
                if (!isNaN(date)) {
                    input.value = date.toISOString().slice(0, 16);
                }
            }
        }
        else {
            input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control form-control-sm';
            input.value = cell.textContent.trim();
        }

        //nahrad
        cell.innerHTML = '';
        cell.appendChild(input);
        input.focus();

        // uloz pri focusout
        input.addEventListener('blur', function() {
            saveCell(cell, id, field, input.value, originalHTML);
        });

        // uloz na enter
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                saveCell(cell, id, field, input.value, originalHTML);
            } else if (e.key === 'Escape') {
                cell.innerHTML = originalHTML;
                cell.classList.remove('editing');
            }
        });
    }

    async function saveCell(cell, id, field, value, originalHTML) {
        console.log('Ukladám:', { id, field, value });

        // ak je prazdna alebo rovnaka tak zrus
        if (value.trim() === '' || value === cell.textContent.trim()) {
            cell.innerHTML = originalHTML;
            cell.classList.remove('editing');
            return;
        }

        try {
            const response = await fetch('/?c=admin&a=updateReservationAjax', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id, field, value })
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Result:', result);

            if (result.success) {
                if (field === 'status') {
                    const badgeClass = result.reservation?.status_badge || 'warning';
                    cell.innerHTML = `<span class="badge bg-${badgeClass}">${result.reservation?.status || value}</span>`;
                } else if (field === 'reservation_date') {
                    cell.innerHTML = result.reservation?.formatted_date || value;
                } else {
                    cell.innerHTML = value;
                }

                // vysledna sprava
                showMessage('Uložené!', 'success');
            } else {
                alert('Chyba: ' + (result.errors?.join(', ') || result.message));
                cell.innerHTML = originalHTML;
            }
        } catch(error) {
            console.error('Chyba:', error);
            alert('Došlo k chybe pri ukladaní. Skontrolujte konzolu (F12).\n' + error.message);
            cell.innerHTML = originalHTML;
        }

        cell.classList.remove('editing');
    }

    function showMessage(text, type) {
        const msg = document.createElement('div');
        msg.textContent = text;
        msg.className = 'position-fixed bottom-0 end-0 m-3 p-3 rounded';
        msg.style.cssText = `
        background: ${type === 'success' ? '#198754' : '#dc3545'};
        color: white;
        z-index: 9999;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        font-weight: 500;
        `;
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 2000);
    }
});