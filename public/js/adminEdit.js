class InlineEditor {
    constructor(options = {}) {
        this.options = {
            endpoint: '/?c=admin&a=updateAjax',
            selector: '.editable-cell',
            ...options
        };

        // def validatory
        this.validators = {
            email: {
                validate: (value) => {
                    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return regex.test(value) ? null : 'Neplatný email (napr. meno@domena.sk)';
                }
            },
            phone: {
                validate: (value) => {
                    const digits = value.replace(/\D/g, '');
                    return (digits.length >= 9 && digits.length <= 15) ? null : 'Telefónne číslo musí obsahovať 9-15 číslic';
                }
            },
            name: {
                validate: (value) => {
                    const trimmed = value.replace(/\s/g, '');
                    return trimmed.length >= 4 ? null : 'Meno musí obsahovať aspoň 4 nemedzerové znaky';
                }
            },
            title: {
                validate: (value) => {
                    const trimmed = value.replace(/\s/g, '');
                    return trimmed.length >= 2 ? null : 'Názov musí obsahovať aspoň 2 nemedzerové znaky';
                }
            },
            price: {
                validate: (value) => {
                    const num = parseFloat(value);
                    return (!isNaN(num) && num > 0) ? null : 'Cena musí byť kladné číslo';
                }
            },
            duration: {
                validate: (value) => {
                    const num = parseInt(value);
                    return (!isNaN(num) && num > 0) ? null : 'Trvanie musí byť kladné celé číslo';
                }
            },
            text: {
                validate: (value) => {
                    return value.length >= 1 ? null : 'Pole je povinné';
                }
            }
        };

        this.init();
    }

    init() {
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

        let input = this.createInput(type, cell, field);

        cell.innerHTML = '';
        cell.appendChild(input);
        input.focus();

        // real time validacia
        input.addEventListener('input', () => {
            this.validateInput(input, field);
        });

        // save focusout
        input.addEventListener('blur', () => {
            if (this.validateInput(input, field)) {
                this.saveCell(cell, id, field, input.value, originalHTML, entity);
            } else {
                // ak zlyha, crat povodnu
                cell.innerHTML = originalHTML;
                cell.classList.remove('editing');
            }
        });

        // save enter
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                if (this.validateInput(input, field)) {
                    this.saveCell(cell, id, field, input.value, originalHTML, entity);
                }
            } else if (e.key === 'Escape') {
                cell.innerHTML = originalHTML;
                cell.classList.remove('editing');
            }
        });
    }

    createInput(type, cell, field) {
        let input;

        switch(type) {
            case 'select':
                input = document.createElement('select');
                input.className = 'form-select form-select-sm editable-input';

                const options = cell.dataset.options ? JSON.parse(cell.dataset.options) : [];
                options.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option.value;
                    opt.textContent = option.text;

                    //treba porovnat iba string pre badge
                    const cellText = cell.textContent.trim();
                    const badgeMatch = cellText.match(/^([A-Za-záäčďéíľňóôŕšťúýžÁÄČĎÉÍĽŇÓÔŔŠŤÚÝŽ]+)/);
                    if (badgeMatch) {
                        if (badgeMatch[1] === option.text) {
                            opt.selected = true;
                        }
                    } else if (cellText === option.text) {
                        opt.selected = true;
                    }

                    input.appendChild(opt);
                });
                break;

            case 'textarea':
                input = document.createElement('textarea');
                input.className = 'form-control form-control-sm editable-input';
                input.rows = 2;
                input.value = cell.textContent.trim();
                break;

            case 'datetime':
                input = document.createElement('input');
                input.type = 'datetime-local';
                input.className = 'form-control form-control-sm editable-input';

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
                input.className = 'form-control form-control-sm editable-input';
                input.value = cell.textContent.trim();
                break;

            case 'number':
                input = document.createElement('input');
                input.type = 'number';
                input.className = 'form-control form-control-sm editable-input';
                input.value = cell.textContent.trim();
                break;

            default: // text
                input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control form-control-sm editable-input';
                input.value = cell.textContent.trim();
        }

        // pridanie data atributu na identifikaciu
        input.dataset.field = field;

        return input;
    }

    validateInput(input, field) {
        const value = input.value.trim();
        let validatorType = 'text';

        // zisti typ validatora
        if (field === 'email' || field === 'guest_email') {
            validatorType = 'email';
        } else if (field === 'phone' || field === 'guest_phone') {
            validatorType = 'phone';
        } else if (field === 'name' || field === 'guest_name' || field === 'full_name') {
            validatorType = 'name';
        } else if (field === 'title') {
            validatorType = 'title';
        } else if (field === 'price') {
            validatorType = 'price';
        } else if (field === 'duration') {
            validatorType = 'duration';
        }

        const validator = this.validators[validatorType];
        if (!validator) return true;

        const error = validator.validate(value);

        if (error) {
            input.classList.add('is-invalid');
            input.title = error;
            return false;
        } else {
            input.classList.remove('is-invalid');
            input.title = '';
            return true;
        }
    }

    async saveCell(cell, id, field, value, originalHTML, entity) {
        // 1.validacia
        if (!this.validateField(field, value)) {
            this.showMessage('Neplatná hodnota', 'error');
            cell.innerHTML = originalHTML;
            cell.classList.remove('editing');
            return;
        }

        if (value.trim() === '' || value === cell.textContent.trim()) {
            cell.innerHTML = originalHTML;
            cell.classList.remove('editing');
            return;
        }

        if (cell.dataset.type === 'number') {
            value = Math.floor(parseFloat(value) || 0);
        }

        try {
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
                    entity
                })
            });

            if (!response.ok) {
                new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                const displayValue = result.value !== undefined ? result.value : value;

                if (cell.dataset.render === 'badge' && !result.badgeClass) {
                    // skus ziskat batch z povodneho html
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = originalHTML;
                    const badgeSpan = tempDiv.querySelector('.badge');
                    if (badgeSpan) {
                        const bgClass = badgeSpan.className.match(/bg-(\w+)/);
                        if (bgClass) {
                            result.badgeClass = bgClass[1];
                        }
                    }
                }

                if (cell.dataset.render) {
                    this.renderCell(cell, displayValue, result);
                } else {
                    cell.innerHTML = displayValue;
                }

                this.showMessage('Uložené!', 'success');
            } else {
                this.showMessage('Chyba: ' + (result.errors?.join(', ') || result.message), 'error');
                cell.innerHTML = originalHTML;
            }
        } catch(error) {
            console.error('Chyba:', error);
            this.showMessage('Došlo k chybe pri ukladaní.', 'error');
            cell.innerHTML = originalHTML;
        }

        cell.classList.remove('editing');
    }

    validateField(field, value) {
        const trimmedValue = value.trim();

        if (trimmedValue === '') {
            return false;
        }

        // speci validacia podla typu pola
        switch(field) {
            case 'email':
            case 'guest_email':
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedValue);

            case 'phone':
            case 'guest_phone':
                const digits = trimmedValue.replace(/\D/g, '');
                return digits.length >= 9 && digits.length <= 15;

            case 'name':
            case 'guest_name':
            case 'full_name':
                return trimmedValue.replace(/\s/g, '').length >= 4;

            case 'title':
                return trimmedValue.replace(/\s/g, '').length >= 2;

            case 'price':
                const price = parseFloat(trimmedValue);
                return !isNaN(price) && price > 0;

            case 'duration':
                const duration = parseInt(trimmedValue);
                return !isNaN(duration) && duration > 0;

            default:
                return true;
        }
    }

    renderCell(cell, value, result) {
        const renderType = cell.dataset.render;

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

        switch(renderType) {
            case 'service':
                cell.innerHTML = value;
                break;
            case 'price':
                const intPrice = parseInt(value);
                if (isNaN(intPrice)) {
                    cell.innerHTML = `${value} €`;
                } else {
                    cell.innerHTML = `${intPrice} €`;
                }
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