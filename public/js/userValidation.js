// public/js/userValidation.js
const Validator = {
    showError(field, message) {
        field.classList.add('is-invalid');
        let help = document.getElementById(field.id + '_help') || this.createHelpElement(field);
        help.textContent = message;
        help.className = 'form-text text-danger';
    },

    clearError(field) {
        field.classList.remove('is-invalid');
        const help = document.getElementById(field.id + '_help');
        if (help) help.textContent = '';
    },

    createHelpElement(field) {
        const div = document.createElement('div');
        div.id = field.id + '_help';
        div.className = 'form-text text-danger';
        field.parentNode.appendChild(div);
        return div;
    },

    validate(field, rules) {
        const value = field.value.trim();

        if (!value && rules.required) {
            this.showError(field, rules.requiredMsg || 'Toto pole je povinné');
            return false;
        }

        if (!value && !rules.required) {
            this.clearError(field);
            return true;
        }

        if (rules.regex && !rules.regex.test(value)) {
            this.showError(field, rules.regexMsg);
            return false;
        }

        if (rules.custom && !rules.custom(value, field)) {
            this.showError(field, rules.customMsg);
            return false;
        }

        this.clearError(field);
        return true;
    }
};

// Spoločné funkcie
function setupField(field, validatorFn, updateBtnFn = null) {
    if (!field) return;

    const handler = () => {
        validatorFn();
        if (updateBtnFn) updateBtnFn();
    };

    field.addEventListener('input', handler);
    field.addEventListener('blur', handler);
}

// Interaktívny zoznam požiadaviek na heslo
function createPasswordRequirements(field, note = '') {
    if (!field) return;

    const reqId = field.id + '_requirements';
    if (document.getElementById(reqId)) return;

    const div = document.createElement('div');
    div.id = reqId;
    div.className = 'form-text mt-1 password-requirements';
    div.style.display = 'none';
    div.innerHTML = (note ? `<div class="small">${note}</div>` : '') + `
        <ul class="mb-0" style="list-style-type: none; padding-left: 0;">
            <li id="${reqId}_length" class="text-danger">✗ Minimálne 8 znakov</li>
            <li id="${reqId}_upper" class="text-danger">✗ Aspoň jedno veľké písmeno</li>
            <li id="${reqId}_number" class="text-danger">✗ Aspoň jednu číslicu</li>
        </ul>`;
    field.parentNode.appendChild(div);

    field.addEventListener('input', function() {
        const v = this.value;
        const requirementsDiv = document.getElementById(reqId);

        if (!v.trim()) {
            requirementsDiv.style.display = 'none';
        } else {
            requirementsDiv.style.display = 'block';
        }

        [{
            id: `${reqId}_length`,
            condition: v.length >= 8,
            text: 'Minimálne 8 znakov'
        }, {
            id: `${reqId}_upper`,
            condition: /[A-Z]/.test(v),
            text: 'Aspoň jedno veľké písmeno'
        }, {
            id: `${reqId}_number`,
            condition: /[0-9]/.test(v),
            text: 'Aspoň jednu číslicu'
        }].forEach(req => {
            const el = document.getElementById(req.id);
            if (!el) return;

            if (req.condition) {
                el.className = 'text-success';
                el.innerHTML = '✓ ' + req.text;
            } else {
                el.className = 'text-danger';
                el.innerHTML = '✗ ' + req.text;
            }
        });
    });

    field.addEventListener('blur', function() {
        const requirementsDiv = document.getElementById(reqId);
        if (!this.value.trim() && requirementsDiv) {
            requirementsDiv.style.display = 'none';
        }
    });

    if (!field.value.trim()) {
        const requirementsDiv = document.getElementById(reqId);
        if (requirementsDiv) {
            requirementsDiv.style.display = 'none';
        }
    }
}

// REZERVAČNÁ LOGIKA - Funkcie pre časové sloty
function getDayOfWeek(dateString) {
    const date = new Date(dateString);
    return date.getDay();
}

function generateTimeSlots(dayOfWeek) {
    let timeSlots = [];
    let note = '';

    // Pondelok - Piatok (1-5): 9:00 - 20:00
    if (dayOfWeek >= 1 && dayOfWeek <= 5) {
        note = 'Pondelok-Piatok: 9:00 - 20:00';
        for (let hour = 9; hour < 20; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                if (hour === 19 && minute >= 30) break;
                const timeStr = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                timeSlots.push(timeStr);
            }
        }
    }
    // Sobota (6): 8:00 - 23:00
    else if (dayOfWeek === 6) {
        note = 'Sobota: 8:00 - 23:00';
        for (let hour = 8; hour < 23; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                if (hour === 22 && minute >= 30) break;
                const timeStr = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                timeSlots.push(timeStr);
            }
        }
    }
    // Nedeľa (0): 8:00 - 23:00
    else if (dayOfWeek === 0) {
        note = 'Nedeľa: 8:00 - 23:00';
        for (let hour = 8; hour < 23; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                if (hour === 22 && minute >= 30) break;
                const timeStr = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                timeSlots.push(timeStr);
            }
        }
    }

    return { timeSlots, note };
}

function populateTimeSlots(dateInput, timeSelect, openingHoursNote) {
    const dateString = dateInput.value;

    if (!dateString) {
        timeSelect.innerHTML = '<option value="">Najprv vyberte dátum</option>';
        if (openingHoursNote) openingHoursNote.textContent = '';
        return;
    }

    const dayOfWeek = getDayOfWeek(dateString);
    const { timeSlots, note } = generateTimeSlots(dayOfWeek);

    let options = '<option value="">Vyberte čas</option>';
    timeSlots.forEach(timeSlot => {
        options += `<option value="${timeSlot}">${timeSlot}</option>`;
    });

    timeSelect.innerHTML = options;
    if (openingHoursNote) openingHoursNote.textContent = note;
    timeSelect.value = '';
}

function updateReservationSummary(dateInput, timeSelect) {
    // Dátum
    if (dateInput && dateInput.value) {
        const date = new Date(dateInput.value);
        const summaryDate = document.getElementById('summaryDate');
        if (summaryDate) {
            summaryDate.textContent = date.toLocaleDateString('sk-SK', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
    }

    // Čas
    if (timeSelect && timeSelect.value) {
        const summaryTime = document.getElementById('summaryTime');
        if (summaryTime) summaryTime.textContent = timeSelect.value;
    }

    // Služba a cena
    const selectedService = document.querySelector('input[name="service_id"]:checked');
    if (selectedService) {
        const serviceName = selectedService.dataset.name;
        const servicePrice = selectedService.dataset.price;

        const summaryService = document.getElementById('summaryService');
        const summaryPrice = document.getElementById('summaryPrice');

        if (summaryService) summaryService.textContent = serviceName;
        if (summaryPrice) summaryPrice.textContent = servicePrice + '€';
    }
}

// FUNKCIE PRE RÔZNE FORMULÁRE
function initRegister() {
    const form = document.getElementById('registerForm');
    if (!form) return;

    const fields = {
        name: document.getElementById('full_name'),
        phone: document.getElementById('phone'),
        email: document.getElementById('email'),
        pass: document.getElementById('password'),
        confirm: document.getElementById('password_confirm')
    };

    const terms = document.querySelector('[name="terms"]');
    const submitBtn = form.querySelector('[type="submit"]');

    createPasswordRequirements(fields.pass);

    const validators = {
        name: () => Validator.validate(fields.name, {
            required: true,
            requiredMsg: 'Meno a priezvisko je povinné',
                        custom: (v) => v.replace(/\s/g, '').length >= 4,
            customMsg: 'Meno musí obsahovať aspoň 4 nemedzerové znaky'
        }),

        phone: () => Validator.validate(fields.phone, {
            required: true,
            requiredMsg: 'Telefónne číslo je povinné',
            custom: (v) => {
                const digits = v.replace(/\D/g, '');
                return digits.length >= 9 && digits.length <= 15 && /^[\d\s\-+()]+$/.test(v);
            },
            customMsg: 'Telefónne číslo musí obsahovať 9-15 číslic a môže obsahovať iba čísla, medzery, +, - a ()'
        }),

        email: () => Validator.validate(fields.email, {
            required: true,
            requiredMsg: 'Email je povinný',
            regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            regexMsg: 'Zadajte platný email (napr. priklad@email.sk)'
        }),

        pass: () => Validator.validate(fields.pass, {
            required: true,
            requiredMsg: 'Heslo je povinné',
            custom: (v) => v.length >= 8 && /[A-Z]/.test(v) && /[0-9]/.test(v)
        }),

        confirm: () => {
            if (!fields.confirm.value) {
                Validator.showError(fields.confirm, 'Potvrdenie hesla je povinné');
                return false;
            }
            if (fields.pass.value !== fields.confirm.value) {
                Validator.showError(fields.confirm, 'Heslá sa nezhodujú');
                return false;
            }
            Validator.clearError(fields.confirm);
            return true;
        },

        terms: () => {
            const help = document.getElementById('terms_help');
            if (!terms.checked) {
                if (help) {
                    help.textContent = 'Musíte súhlasiť so spracovaním osobných údajov';
                    help.className = 'form-text text-danger';
                }
                return false;
            }
            if (help) help.textContent = '';
            return true;
        }
    };

    setupField(fields.name, validators.name, updateBtn);
    setupField(fields.phone, validators.phone, updateBtn);
    setupField(fields.email, validators.email, updateBtn);

    if (fields.pass) {
        fields.pass.addEventListener('input', function() {
            validators.pass();
            validators.confirm();
            updateBtn();
        });
    }

    setupField(fields.confirm, validators.confirm, updateBtn);

    if (terms) {
        terms.addEventListener('change', function() {
            validators.terms();
            updateBtn();
        });
    }

    form.addEventListener('submit', function(e) {
        const isValid = Object.values(validators).every(fn => fn());
        if (!isValid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        } else {
            const reqDiv = document.getElementById('password_requirements');
            if (reqDiv) {
                reqDiv.style.display = 'none';
            }
        }
    });

    function updateBtn() {
        if (!submitBtn) return;
        const isValid = Object.values(validators).every(fn => fn());
        submitBtn.disabled = !isValid;
        submitBtn.classList.toggle('btn-disabled', !isValid);
    }

    Object.values(validators).forEach(fn => fn());
    updateBtn();
}

function initEdit() {
    const form = document.getElementById('editForm');
    if (!form) return;

    const fields = {
        name: document.getElementById('full_name'),
        phone: document.getElementById('phone'),
        email: document.getElementById('email'),
        currentPass: document.getElementById('current_password'),
        newPass: document.getElementById('new_password'),
        confirmPass: document.getElementById('confirm_password')
    };

    const submitBtn = form.querySelector('[type="submit"]');

    createPasswordRequirements(fields.newPass, 'Heslo vyplňte len ak ho chcete zmeniť:');

    const validators = {
        name: () => Validator.validate(fields.name, {
            required: true,
            requiredMsg: 'Meno a priezvisko je povinné',
            custom: (v) => v === '' || v.replace(/\s/g, '').length >= 4,
            customMsg: 'Meno musí obsahovať aspoň 4 nemedzerové znaky'
        }),

        phone: () => Validator.validate(fields.phone, {
            required: true,
            requiredMsg: 'Telefónne číslo je povinné',
            custom: (v) => {
                const digits = v.replace(/\D/g, '');
                return digits.length >= 9 && digits.length <= 15 && /^[\d\s\-+()]+$/.test(v);
            },
            customMsg: 'Telefónne číslo musí obsahovať 9-15 číslic a môže obsahovať iba čísla, medzery, +, - a ()'
        }),

        email: () => Validator.validate(fields.email, {
            required: true,
            requiredMsg: 'Email je povinný',
            regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            regexMsg: 'Zadajte platný email (napr. priklad@email.sk)'
        }),

        currentPass: () => {
            const v = fields.currentPass.value.trim();
            const newPass = fields.newPass.value.trim();

            if (!newPass) {
                Validator.clearError(fields.currentPass);
                return true;
            }

            if (!v) {
                Validator.showError(fields.currentPass, 'Aktuálne heslo je povinné pri zmene hesla');
                return false;
            }

            Validator.clearError(fields.currentPass);
            return true;
        },

        newPass: () => {
            const v = fields.newPass.value;
            if (!v) {
                Validator.clearError(fields.newPass);
                return true;
            }

            if (v.length >= 8 && /[A-Z]/.test(v) && /[0-9]/.test(v)) {
                Validator.clearError(fields.newPass);
                return true;
            }

            return false;
        },

        confirmPass: () => {
            const newPass = fields.newPass.value;
            const confirm = fields.confirmPass.value;

            if (!newPass && !confirm) {
                Validator.clearError(fields.confirmPass);
                return true;
            }

            if (newPass && !confirm) {
                Validator.showError(fields.confirmPass, 'Zadajte potvrdenie hesla');
                return false;
            }

            if (newPass !== confirm) {
                Validator.showError(fields.confirmPass, 'Heslá sa nezhodujú');
                return false;
            }

            Validator.clearError(fields.confirmPass);
            return true;
        }
    };

    setupField(fields.name, validators.name, updateBtn);
    setupField(fields.phone, validators.phone, updateBtn);
    setupField(fields.email, validators.email, updateBtn);

    if (fields.newPass) {
        fields.newPass.addEventListener('input', function() {
            validators.newPass();
            validators.confirmPass();
            validators.currentPass();
            updateBtn();
        });

        fields.newPass.addEventListener('focus', function() {
            const reqDiv = document.getElementById('new_password_requirements');
            if (reqDiv) {
                reqDiv.style.display = 'block';
            }
        });

        fields.newPass.addEventListener('blur', function() {
            const reqDiv = document.getElementById('new_password_requirements');
            if (reqDiv && !this.value.trim()) {
                reqDiv.style.display = 'none';
            }
        });
    }

    setupField(fields.currentPass, validators.currentPass, updateBtn);
    setupField(fields.confirmPass, validators.confirmPass, updateBtn);

    form.addEventListener('submit', function(e) {
        const passwordChange = fields.newPass.value.trim() !== '';
        const isValid =
            validators.name() &&
            validators.phone() &&
            validators.email() &&
            (!passwordChange || (validators.newPass() && validators.confirmPass() && validators.currentPass()));

        if (!isValid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        }
    });

    function updateBtn() {
        if (!submitBtn) return;
        const passwordChange = fields.newPass.value.trim() !== '';
        const isValid =
            validators.name() &&
            validators.phone() &&
            validators.email() &&
            (!passwordChange || (validators.newPass() && validators.confirmPass() && validators.currentPass()));

        submitBtn.disabled = !isValid;
        submitBtn.classList.toggle('btn-disabled', !isValid);
    }

    Object.values(validators).forEach(fn => fn());
    updateBtn();
}

function initReservation() {
    const form = document.getElementById('reservationForm');
    if (!form) return;

    const dateInput = document.getElementById('reservationDate');
    const timeSelect = document.getElementById('timeSelect');
    const serviceRadios = document.querySelectorAll('input[name="service_id"]');
    const openingHoursNote = document.getElementById('openingHoursNote');
    const customerName = document.getElementById('customerName');
    const phone = document.getElementById('phone');
    const email = document.getElementById('email');
    const noteTextarea = document.getElementById('note');
    const submitBtn = form.querySelector('[type="submit"]');

    // Počítadlo znakov pre poznámku
    if (noteTextarea) {
        const noteCounter = document.getElementById('noteCounter');
        if (noteCounter) {
            noteTextarea.addEventListener('input', function() {
                noteCounter.textContent = 70 - this.value.length;
            });
        }
    }

    // Funkcie pre časové sloty
    if (dateInput && timeSelect) {
        dateInput.addEventListener('change', function() {
            populateTimeSlots(dateInput, timeSelect, openingHoursNote);
            updateSummary();
            validators.date();
            validators.dateTimeFuture();
            updateBtn();
        });
    }

    if (timeSelect) {
        timeSelect.addEventListener('change', function() {
            updateSummary();
            validators.time();
            validators.dateTimeFuture();
            updateBtn();
        });
    }

    if (serviceRadios.length > 0) {
        serviceRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                updateSummary();
                validators.service();

                document.querySelectorAll('.service-option').forEach(el => {
                    el.classList.remove('border-gold');
                    el.style.backgroundColor = '';
                });

                if (this.checked) {
                    const parent = this.closest('.service-option');
                    if (parent) {
                        parent.classList.add('border-gold');
                        parent.style.backgroundColor = 'rgba(212, 175, 55, 0.1)';
                    }
                }

                updateBtn();
            });
        });
    }

    // Validátory pre rezerváciu
    const validators = {
        date: () => Validator.validate(dateInput, {
            required: true,
            requiredMsg: 'Dátum je povinný',
            custom: (value) => {
                const selectedDate = new Date(value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const maxDate = new Date();
                maxDate.setDate(today.getDate() + 60);
                return selectedDate >= today && selectedDate <= maxDate;
            },
            customMsg: 'Dátum musí byť v rozsahu od dnes do 60 dní'
        }),

        time: () => {
            if (!timeSelect.value) {
                Validator.showError(timeSelect, 'Čas je povinný');
                return false;
            }

            if (timeSelect.value === '') {
                Validator.showError(timeSelect, 'Vyberte platný čas');
                return false;
            }

            const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
            if (!timeRegex.test(timeSelect.value)) {
                Validator.showError(timeSelect, 'Neplatný formát času');
                return false;
            }

            Validator.clearError(timeSelect);
            return true;
        },

        dateTimeFuture: () => {
            if (!dateInput.value || !timeSelect.value) {
                return true; // Ak nie je dátum alebo čas, nevaliduj
            }

            // Zložiť dátum a čas
            const dateTimeStr = dateInput.value + ' ' + timeSelect.value + ':00';
            const selectedDate = new Date(dateTimeStr);
            const now = new Date();

            if (selectedDate <= now) {
                Validator.showError(timeSelect, 'Tento čas nieje dostupný.');
                return false;
            }

            Validator.clearError(timeSelect);
            return true;
        },

        customerName: () => Validator.validate(customerName, {
            required: true,
            requiredMsg: 'Meno a priezvisko je povinné',
            custom: (v) => v.replace(/\s/g, '').length >= 4,
            customMsg: 'Meno musí obsahovať aspoň 4 nemedzerové znaky'
        }),

        phone: () => {
            const value = phone.value;
            const digits = value.replace(/\D/g, '');

            if (!value.trim()) {
                Validator.showError(phone, 'Telefónne číslo je povinné');
                return false;
            }

            if (digits.length < 9 || digits.length > 15) {
                Validator.showError(phone, 'Telefónne číslo musí obsahovať 9-15 číslic');
                return false;
            }

            if (!/^[\d\s\-+()]+$/.test(value)) {
                Validator.showError(phone, 'Môže obsahovať iba čísla, medzery, +, - a ()');
                return false;
            }

            Validator.clearError(phone);
            return true;
        },

        email: () => {
            const value = email.value.trim();

            if (!value) {
                Validator.showError(email, 'Email je povinný');
                return false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                Validator.showError(email, 'Zadajte platný email (napr. priklad@email.sk)');
                return false;
            }

            Validator.clearError(email);
            return true;
        },

        service: () => {
            const selectedService = Array.from(serviceRadios).find(radio => radio.checked);
            if (!selectedService) {
                document.querySelectorAll('.service-option').forEach(el => {
                    el.classList.add('border-danger');
                });
                return false;
            }
            document.querySelectorAll('.service-option').forEach(el => {
                el.classList.remove('border-danger');
            });
            return true;
        },

        note: () => {
            if (!noteTextarea) return true;

            const note = noteTextarea.value;
            if (note.length > 70) {
                Validator.showError(noteTextarea, 'Poznámka môže mať maximálne 70 znakov');
                return false;
            }

            Validator.clearError(noteTextarea);
            return true;
        }
    };

    // Setup fields
    if (customerName) {
        setupField(customerName, validators.customerName, updateBtn);
    }

    if (phone) {
        setupField(phone, validators.phone, updateBtn);
    }

    if (email) {
        setupField(email, validators.email, updateBtn);
    }

    if (noteTextarea) {
        setupField(noteTextarea, validators.note, updateBtn);
    }

    // Submit handler
    form.addEventListener('submit', function(e) {
        const isValid = Object.values(validators).every(fn => fn());
        if (!isValid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        }
    });

    function updateBtn() {
        if (!submitBtn) return;
        const isValid = Object.values(validators).every(fn => fn());
        submitBtn.disabled = !isValid;
        submitBtn.classList.toggle('btn-disabled', !isValid);
    }

    function updateSummary() {
        if (dateInput && dateInput.value) {
            const date = new Date(dateInput.value);
            const summaryDate = document.getElementById('summaryDate');
            if (summaryDate) {
                summaryDate.textContent = date.toLocaleDateString('sk-SK', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            }
        }

        if (timeSelect && timeSelect.value) {
            const summaryTime = document.getElementById('summaryTime');
            if (summaryTime) summaryTime.textContent = timeSelect.value;
        }

        const selectedService = document.querySelector('input[name="service_id"]:checked');
        if (selectedService) {
            const serviceName = selectedService.dataset.name;
            const servicePrice = selectedService.dataset.price;

            const summaryService = document.getElementById('summaryService');
            const summaryPrice = document.getElementById('summaryPrice');

            if (summaryService) summaryService.textContent = serviceName;
            if (summaryPrice) summaryPrice.textContent = servicePrice + '€';
        }
    }

    // Inicializácia
    if (dateInput && dateInput.value) {
        populateTimeSlots(dateInput, timeSelect, openingHoursNote);
    } else {
        const today = new Date().toISOString().split('T')[0];
        if (dateInput) {
            dateInput.value = today;
            populateTimeSlots(dateInput, timeSelect, openingHoursNote);
        }
    }

    Object.values(validators).forEach(fn => fn());
    updateBtn();
    updateSummary();
}

// ADMIN - Vytvorenie používateľa
// ADMIN - Vytvorenie používateľa
function initAdminUserCreate() {
    const form = document.getElementById('createUserForm');
    if (!form) {
        console.warn('Formulár createUserForm sa nenašiel');
        return;
    }

    const fields = {
        name: document.getElementById('name'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        password: document.getElementById('password'),
        permissions: document.getElementById('permissions')
    };

    const submitBtn = form.querySelector('[type="submit"]');

    // password poziadavky
    if (fields.password) {
        createPasswordRequirements(fields.password, 'Heslo pre používateľa:');
    }

    const validators = {
        name: () => Validator.validate(fields.name, {
            required: true,
            requiredMsg: 'Meno a priezvisko je povinné',
            custom: (v) => v.replace(/\s/g, '').length >= 4,
            customMsg: 'Meno musí obsahovať aspoň 4 nemedzerové znaky'
        }),

        email: () => Validator.validate(fields.email, {
            required: true,
            requiredMsg: 'Email je povinný',
            regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            regexMsg: 'Zadajte platný email (napr. priklad@email.sk)'
        }),

        phone: () => Validator.validate(fields.phone, {
            required: true,
            requiredMsg: 'Telefónne číslo je povinné',
            custom: (v) => {
                const digits = v.replace(/\D/g, '');
                return digits.length >= 9 && digits.length <= 15 && /^[\d\s\-+()]+$/.test(v);
            },
            customMsg: 'Telefónne číslo musí obsahovať 9-15 číslic a môže obsahovať iba čísla, medzery, +, - a ()'
        }),

        password: () => Validator.validate(fields.password, {
            required: true,
            requiredMsg: 'Heslo je povinné',
            custom: (v) => v.length >= 8 && /[A-Z]/.test(v) && /[0-9]/.test(v)
        }),

        permissions: () => {
            const value = fields.permissions.value;
            if (!value) {
                Validator.showError(fields.permissions, 'Rola je povinná');
                return false;
            }
            Validator.clearError(fields.permissions);
            return true;
        }
    };

    // setup
    setupField(fields.name, validators.name, updateBtn);
    setupField(fields.email, validators.email, updateBtn);
    setupField(fields.phone, validators.phone, updateBtn);
    setupField(fields.permissions, validators.permissions, updateBtn);

    // pass
    if (fields.password) {
        fields.password.addEventListener('input', function() {
            validators.password();
            updateBtn();
        });

        fields.password.addEventListener('focus', function() {
            const reqDiv = document.getElementById('password_requirements');
            if (reqDiv) {
                reqDiv.style.display = 'block';
            }
        });

        fields.password.addEventListener('blur', function() {
            const reqDiv = document.getElementById('password_requirements');
            if (reqDiv && !this.value.trim()) {
                reqDiv.style.display = 'none';
            }
        });
    }

    // Submit handler
    form.addEventListener('submit', function(e) {
        const isValid = Object.values(validators).every(fn => fn());
        if (!isValid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        } else {
            const reqDiv = document.getElementById('password_requirements');
            if (reqDiv) {
                reqDiv.style.display = 'none';
            }
        }
    });

    function updateBtn() {
        if (!submitBtn) return;
        const isValid = Object.values(validators).every(fn => fn());
        submitBtn.disabled = !isValid;
        submitBtn.classList.toggle('btn-disabled', !isValid);
    }

    // init
    Object.values(validators).forEach(fn => fn());
    updateBtn();
}

/// ADMIN - Vytvorenie služby
function initAdminServiceCreate() {
    const form = document.getElementById('createServiceForm');
    if (!form) {
        console.warn('Formulár createServiceForm sa nenašiel');
        return;
    }

    const fields = {
        title: document.getElementById('title'),
        price: document.getElementById('price'),
        duration: document.getElementById('duration'),
        description: document.getElementById('description')
    };

    const submitBtn = form.querySelector('[type="submit"]');

    const validators = {
        title: () => Validator.validate(fields.title, {
            required: true,
            requiredMsg: 'Názov služby je povinný',
            custom: (v) => v.replace(/\s/g, '').length >= 2,
            customMsg: 'Názov musí obsahovať aspoň 2 nemedzerové znaky'
        }),

        price: () => {
            const value = fields.price.value.trim();

            // Vytvoríme div pre chybovú správu, ak neexistuje
            let helpDiv = document.getElementById('price_help');
            if (!helpDiv) {
                helpDiv = document.createElement('div');
                helpDiv.id = 'price_help';
                helpDiv.className = 'form-text text-danger';
                fields.price.parentNode.parentNode.appendChild(helpDiv);
            }

            if (!value) {
                fields.price.classList.add('is-invalid');
                helpDiv.textContent = 'Cena je povinná';
                return false;
            }

            const num = parseFloat(value);
            if (isNaN(num) || num <= 0) {
                fields.price.classList.add('is-invalid');
                helpDiv.textContent = 'Cena musí byť kladné číslo';
                return false;
            }

            if (num > 10000) {
                fields.price.classList.add('is-invalid');
                helpDiv.textContent = 'Cena môže byť maximálne 10 000 €';
                return false;
            }

            fields.price.classList.remove('is-invalid');
            helpDiv.textContent = '';
            return true;
        },

        duration: () => {
            const value = fields.duration.value.trim();

            // Vytvoríme div pre chybovú správu, ak neexistuje
            let helpDiv = document.getElementById('duration_help');
            if (!helpDiv) {
                helpDiv = document.createElement('div');
                helpDiv.id = 'duration_help';
                helpDiv.className = 'form-text text-danger';
                fields.duration.parentNode.parentNode.appendChild(helpDiv);
            }

            if (!value) {
                fields.duration.classList.add('is-invalid');
                helpDiv.textContent = 'Trvanie je povinné';
                return false;
            }

            const num = parseInt(value);
            if (isNaN(num) || num <= 0) {
                fields.duration.classList.add('is-invalid');
                helpDiv.textContent = 'Trvanie musí byť kladné celé číslo';
                return false;
            }

            if (num > 480) {
                fields.duration.classList.add('is-invalid');
                helpDiv.textContent = 'Trvanie môže byť maximálne 480 minút (8 hodín)';
                return false;
            }

            fields.duration.classList.remove('is-invalid');
            helpDiv.textContent = '';
            return true;
        },

        description: () => Validator.validate(fields.description, {
            required: true,
            requiredMsg: 'Popis je povinný',
            custom: (v) => v.trim().length > 0,
            customMsg: 'Zadajte popis služby'
        })
    };

    // Setup polí
    setupField(fields.title, validators.title, updateBtn);
    setupField(fields.description, validators.description, updateBtn);

    // Špeciálne spracovanie pre cenu a trvanie
    if (fields.price) {
        fields.price.addEventListener('input', () => {
            validators.price();
            updateBtn();
        });
        fields.price.addEventListener('blur', () => {
            validators.price();
            updateBtn();
        });
    }

    if (fields.duration) {
        fields.duration.addEventListener('input', () => {
            validators.duration();
            updateBtn();
        });
        fields.duration.addEventListener('blur', () => {
            validators.duration();
            updateBtn();
        });
    }

    // Submit handler
    form.addEventListener('submit', function(e) {
        const isValid = Object.values(validators).every(fn => fn());
        if (!isValid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        }
    });

    function updateBtn() {
        if (!submitBtn) return;
        const isValid = Object.values(validators).every(fn => fn());
        submitBtn.disabled = !isValid;
        submitBtn.classList.toggle('btn-disabled', !isValid);
    }

    // Inicializácia
    Object.values(validators).forEach(fn => fn());
    updateBtn();
}

// ADMIN - Vytvorenie barbera
function initAdminBarberCreate() {
    const form = document.getElementById('createBarberForm');
    if (!form) {
        console.warn('Formulár createBarberForm sa nenašiel');
        return;
    }

    const fields = {
        name: document.getElementById('name'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        password: document.getElementById('password'),
        bio: document.getElementById('bio'),
        photo_url: document.getElementById('photo_url')
    };

    const submitBtn = form.querySelector('[type="submit"]');

    // Password requirements
    if (fields.password) {
        createPasswordRequirements(fields.password, 'Heslo pre barbera:');
    }

    const validators = {
        name: () => Validator.validate(fields.name, {
            required: true,
            requiredMsg: 'Meno a priezvisko je povinné',
            custom: (v) => v.replace(/\s/g, '').length >= 4,
            customMsg: 'Meno musí obsahovať aspoň 4 nemedzerové znaky'
        }),

        email: () => Validator.validate(fields.email, {
            required: true,
            requiredMsg: 'Email je povinný',
            regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            regexMsg: 'Zadajte platný email (napr. priklad@email.sk)'
        }),

        phone: () => Validator.validate(fields.phone, {
            required: true,
            requiredMsg: 'Telefónne číslo je povinné',
            custom: (v) => {
                const digits = v.replace(/\D/g, '');
                return digits.length >= 9 && digits.length <= 15 && /^[\d\s\-+()]+$/.test(v);
            },
            customMsg: 'Telefónne číslo musí obsahovať 9-15 číslic a môže obsahovať iba čísla, medzery, +, - a ()'
        }),

        password: () => Validator.validate(fields.password, {
            required: true,
            requiredMsg: 'Heslo je povinné',
            custom: (v) => v.length >= 8 && /[A-Z]/.test(v) && /[0-9]/.test(v)
        }),

        bio: () => Validator.validate(fields.bio, {
            required: true,
            requiredMsg: 'Bio je povinné',
            custom: (v) => v.trim().length > 0,
            customMsg: 'Zadajte bio barbera'
        }),

        photo_url: () => {
            const value = fields.photo_url.value.trim();
            if (!value) {
                Validator.showError(fields.photo_url, 'URL fotky je povinná');
                return false;
            }

            // Voliteľne: validácia formátu URL
            if (!value.startsWith('http://') && !value.startsWith('https://')) {
                Validator.showError(fields.photo_url, 'URL musí začínať s http:// alebo https://');
                return false;
            }

            Validator.clearError(fields.photo_url);
            return true;
        }
    };

    // Setup polí
    setupField(fields.name, validators.name, updateBtn);
    setupField(fields.email, validators.email, updateBtn);
    setupField(fields.phone, validators.phone, updateBtn);
    setupField(fields.bio, validators.bio, updateBtn);
    setupField(fields.photo_url, validators.photo_url, updateBtn);

    // Password field - špeciálne spracovanie
    if (fields.password) {
        fields.password.addEventListener('input', function() {
            validators.password();
            updateBtn();
        });

        fields.password.addEventListener('focus', function() {
            const reqDiv = document.getElementById('password_requirements');
            if (reqDiv) {
                reqDiv.style.display = 'block';
            }
        });

        fields.password.addEventListener('blur', function() {
            const reqDiv = document.getElementById('password_requirements');
            if (reqDiv && !this.value.trim()) {
                reqDiv.style.display = 'none';
            }
        });
    }

    // Submit handler
    form.addEventListener('submit', function(e) {
        const isValid = Object.values(validators).every(fn => fn());
        if (!isValid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        } else {
            const reqDiv = document.getElementById('password_requirements');
            if (reqDiv) {
                reqDiv.style.display = 'none';
            }
        }
    });

    function updateBtn() {
        if (!submitBtn) return;
        const isValid = Object.values(validators).every(fn => fn());
        submitBtn.disabled = !isValid;
        submitBtn.classList.toggle('btn-disabled', !isValid);
    }

    // Inicializácia
    Object.values(validators).forEach(fn => fn());
    updateBtn();
}

// Hlavná inicializačná funkcia
document.addEventListener('DOMContentLoaded', function() {
    // Zisti, ktorý formulár je na stránke a inicializuj príslušnú funkciu
    if (document.getElementById('registerForm')) initRegister();        //ked das do view id="register form, spusti sa initregister
    if (document.getElementById('editForm')) initEdit();
    if (document.getElementById('reservationForm')) initReservation();

    //admin formulare
    if (document.getElementById('createUserForm')) initAdminUserCreate();
    if (document.getElementById('createServiceForm')) initAdminServiceCreate();
    if (document.getElementById('createBarberForm')) initAdminBarberCreate();
});