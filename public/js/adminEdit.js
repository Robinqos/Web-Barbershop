class InlineEditor {
    constructor(options = {}) {
        this.options = {
            endpoint: '/?c=admin&a=updateAjax', // default endpoint
            selector: '.editable-cell', // selektor edit buniek
            ...options
        };

        this.init();
    }

    init() {
        // KLIK na bunku
        document.addEventListener('click', (e) => {
            const cell = e.target.closest(this.options.selector);
            if (cell && !cell.classList.contains('editing')) {
                this.editCell(cell);
            }
        });
    }

    editCell(cell) {
        const id = cell.dataset.id;
        const field = cell.dataset.field;
        const type = cell.dataset.type || 'text';
        const entity = cell.dataset.entity || 'reservation';

        cell.classList.add('editing');
        const originalHTML = cell.innerHTML;

        // Vytvor input podľa typu
        let input = this.createInput(type, cell);

        //nahrad
        cell.innerHTML = '';
        cell.appendChild(input);
        input.focus();

        // save focusout
        input.addEventListener('blur', () => {
            this.saveCell(cell, id, field, input.value, originalHTML, entity);
        });

        // save enter
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                this.saveCell(cell, id, field, input.value, originalHTML, entity);
            } else if (e.key === 'Escape') {
                cell.innerHTML = originalHTML;
                cell.classList.remove('editing');
            }
        });
    }

    createInput(type, cell) {
        let input;

        switch(type) {
            case 'select':
                input = document.createElement('select');
                input.className = 'form-select form-select-sm';

                // moznosti z data atribut
                const options = cell.dataset.options ? JSON.parse(cell.dataset.options) : [];
                options.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option.value;
                    opt.textContent = option.text;

                    // aktualna hodnota
                    if (cell.textContent.trim() === option.text) {
                        opt.selected = true;
                    }

                    input.appendChild(opt);
                });
                break;

            case 'textarea':
                input = document.createElement('textarea');
                input.className = 'form-control form-control-sm';
                input.rows = 2;
                input.value = cell.textContent.trim();
                break;

            case 'datetime':
                input = document.createElement('input');
                input.type = 'datetime-local';
                input.className = 'form-control form-control-sm';

                const text = cell.textContent.trim();
                if (text) {
                    const date = new Date(text);
                    if (!isNaN(date)) {
                        input.value = date.toISOString().slice(0, 16);
                    }
                }
                break;

            case 'email':
                input = document.createElement('input');
                input.type = 'email';
                input.className = 'form-control form-control-sm';
                input.value = cell.textContent.trim();
                break;

            case 'number':
                input = document.createElement('input');
                input.type = 'number';
                input.className = 'form-control form-control-sm';
                input.value = cell.textContent.trim();
                break;

            default: // text
                input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control form-control-sm';
                input.value = cell.textContent.trim();
        }

        return input;
    }

    async saveCell(cell, id, field, value, originalHTML, entity) {
        if (value.trim() === '' || value === cell.textContent.trim()) {
            cell.innerHTML = originalHTML;
            cell.classList.remove('editing');
            return;
        }

        try {
            // pouzi custom endpoint alebo default
            const endpoint = cell.dataset.endpoint || this.options.endpoint;

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id,
                    field,
                    value,
                    entity // Pridáme typ entity
                })
            });

            if (!response.ok) {
                new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                const displayValue = result.value !== undefined ? result.value : value;
                // pouzi custom render funkciu alebo default
                if (cell.dataset.render) {
                    this.renderCell(cell, displayValue, result);
                } else {
                    cell.innerHTML = displayValue;
                }

                this.showMessage('Uložené!', 'success');
            } else {
                alert('Chyba: ' + (result.errors?.join(', ') || result.message));
                cell.innerHTML = originalHTML;
            }
        } catch(error) {
            console.error('Chyba:', error);
            alert('Došlo k chybe pri ukladaní.');
            cell.innerHTML = originalHTML;
        }

        cell.classList.remove('editing');
    }

    renderCell(cell, value, result) {
        const renderType = cell.dataset.render;

        // spolozne pre badge a status
        if (renderType === 'badge' || renderType === 'status') {
            const badgeClass = result.badgeClass || 'secondary';

            if (renderType === 'status') {
                const statusMap = {
                    'pending': 'Čakajúca',
                    'completed': 'Dokončená',
                    'cancelled': 'Zrušená',
                    'active': 'Aktívny',
                    'inactive': 'Neaktívny'
                };
                value = statusMap[value] || value;
            }

            cell.innerHTML = `<span class="badge bg-${badgeClass} text-dark">${value}</span>`;
            return;
        }

        // Ostatné typy
        switch(renderType) {
            case 'price':
                cell.innerHTML = `${value} €`;
                break;

            case 'duration':
                cell.innerHTML = `${value} min`;
                break;

            default:
                cell.innerHTML = value;
        }
    }

    showMessage(text, type) {
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
}

// init
document.addEventListener('DOMContentLoaded', function() {
    window.inlineEditor = new InlineEditor();
});