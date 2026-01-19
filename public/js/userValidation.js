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
        //ak je prazdne a povinne, zobraz iba ak nieco user spravil
        if (!value && rules.required) {
            //ci uz malo focus alebo input
            if (field.dataset.interacted === 'true') {
                this.showError(field, rules.requiredMsg || 'Toto pole je povinné');
                return false;
            }
            // nezobrazuj chybu
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

    // nastavim enteracted na false
    field.dataset.interacted = 'false';

    const handler = () => {
        // 1.input/focusout
        field.dataset.interacted = 'true';
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

async function populateTimeSlots(dateInput, timeSelect, openingHoursNote, barberId = null, serviceDuration = 30) {
    const dateString = dateInput.value;

    if (!dateString) {
        timeSelect.innerHTML = '<option value="">Najprv vyberte dátum</option>';
        if (openingHoursNote) openingHoursNote.textContent = '';
        return;
    }

    const dayOfWeek = getDayOfWeek(dateString);
    const { timeSlots, note } = generateTimeSlots(dayOfWeek);

    let options = '<option value="">Vyberte čas</option>';

    // ak je barber a datum, nacitaj obsadene
    let occupiedTimes = [];
    if (barberId && dateString) {
        occupiedTimes = await fetchOccupiedTimes(barberId, dateString);
    }

    // kazdy slot check
    timeSlots.forEach(timeSlot => {
        let isOccupied;

        // 60min=2sloty
        if (serviceDuration === 60) {
            const nextSlot = getNextTimeSlot(timeSlot);
            const twoSlotsOccupied = occupiedTimes.includes(timeSlot) || occupiedTimes.includes(nextSlot);

            // ci existuje dalsi slot
            const nextSlotExists = timeSlots.includes(nextSlot);
            isOccupied = twoSlotsOccupied || !nextSlotExists;
        } else {
            //30min = 1slot
            isOccupied = occupiedTimes.includes(timeSlot);
        }

        const disabledText = isOccupied ? ' (obsadené)' : '';
        options += `<option value="${timeSlot}" ${isOccupied ? 'disabled' : ''}>${timeSlot}${disabledText}</option>`;
    });

    timeSelect.innerHTML = options;
    if (openingHoursNote) openingHoursNote.textContent = note;
    timeSelect.value = '';
}

// helper method na dalsi slot
function getNextTimeSlot(timeSlot) {
    const [hours, minutes] = timeSlot.split(':').map(Number);
    let nextHours = hours;
    let nextMinutes = minutes + 30;

    if (nextMinutes >= 60) {
        nextHours += 1;
        nextMinutes = 0;
    }

    return `${String(nextHours).padStart(2, '0')}:${String(nextMinutes).padStart(2, '0')}`;
}

// nacitanie obsadenych casov barbera
async function fetchOccupiedTimes(barberId, date) {
    try {
        const response = await fetch(`/?c=reservation&a=getOccupiedTimes&barber_id=${barberId}&date=${date}`);
        if (!response.ok) {
            throw new Error('Failed to fetch occupied times');
        }
        const occupiedTimes = await response.json();
        return occupiedTimes || [];
    } catch (error) {
        console.error('Error fetching occupied times:', error);
        return [];
    }
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
    const barberRadios = document.querySelectorAll('input[name="barber_id"]');
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

    // ziskanie vybratych hodnot
    function getSelectedValues() {
        const selectedBarber = document.querySelector('input[name="barber_id"]:checked');
        const selectedService = document.querySelector('input[name="service_id"]:checked');

        return {
            barberId: selectedBarber ? selectedBarber.value : null,
            serviceDuration: selectedService ? parseInt(selectedService.dataset.duration) : 30
        };
    }

    // casy s obsadenostou
    async function updateTimeSlots() {
        const { barberId, serviceDuration } = getSelectedValues();
        await populateTimeSlots(dateInput, timeSelect, openingHoursNote, barberId, serviceDuration);
    }

    // Funkcie pre časové sloty
    if (dateInput && timeSelect) {
        dateInput.addEventListener('change', async function() {
            await updateTimeSlots();
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
            radio.addEventListener('change', async function() {
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

                // casy podla trvania sluzby
                await updateTimeSlots();
                updateBtn();
            });
        });
    }

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
            const value = timeSelect.value;
            if (!value && timeSelect.dataset.interacted === 'true') {
                Validator.showError(timeSelect, 'Čas je povinný');
                return false;
            }

            if (!value) {
                return false;
            }

            // ci nieje cas disabled
            const selectedOption = timeSelect.options[timeSelect.selectedIndex];
            if (selectedOption && selectedOption.disabled) {
                Validator.showError(timeSelect, 'Tento čas je už obsadený. Vyberte iný čas.');
                return false;
            }

            const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
            if (!timeRegex.test(value)) {
                Validator.showError(timeSelect, 'Neplatný formát času');
                return false;
            }

            Validator.clearError(timeSelect);
            return true;
        },
        barber: () => {
            const selectedBarber = Array.from(barberRadios).find(radio => radio.checked);
            if (!selectedBarber) {
                // ckeck ci uz klikol na radiobutt
                let anyBarberInteracted = false;
                barberRadios.forEach(radio => {
                    if (radio.dataset.interacted === 'true') anyBarberInteracted = true;
                });

                if (anyBarberInteracted) {
                    document.querySelectorAll('.barber-option').forEach(el => {
                        el.classList.add('border-danger');
                    });
                    return false;
                }
                return false;
            }
            document.querySelectorAll('.barber-option').forEach(el => {
                el.classList.remove('border-danger');
            });
            return true;
        },

        dateTimeFuture: () => {
            if (!dateInput.value || !timeSelect.value) {
                return true;
            }

            // spoj
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

        phone: () => Validator.validate(phone, {
            required: true,
            requiredMsg: 'Telefónne číslo je povinné',
            custom: (v) => {
                const digits = v.replace(/\D/g, '');
                return digits.length >= 9 && digits.length <= 15 && /^[\d\s\-+()]+$/.test(v);
            },
            customMsg: 'Telefónne číslo musí obsahovať 9-15 číslic a môže obsahovať iba čísla, medzery, +, - a ()'
        }),

        email: () => Validator.validate(email, {
            required: true,
            requiredMsg: 'Email je povinný',
            regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            regexMsg: 'Zadajte platný email (napr. priklad@email.sk)'
        }),

        service: () => {
            const selectedService = Array.from(serviceRadios).find(radio => radio.checked);
            if (!selectedService) {
                // montrola ci niektory radio nebol uz zapnuty
                let anyRadioInteracted = false;
                serviceRadios.forEach(radio => {
                    if (radio.dataset.interacted === 'true') anyRadioInteracted = true;
                });

                if (anyRadioInteracted) {
                    document.querySelectorAll('.service-option').forEach(el => {
                        el.classList.add('border-danger');
                    });
                }
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
            if (note.length > 70 && noteTextarea.dataset.interacted === 'true') {
                Validator.showError(noteTextarea, 'Poznámka môže mať maximálne 70 znakov');
                return false;
            }

            Validator.clearError(noteTextarea);
            return true;
        }
    };

    // event listener pre barbera
    if (barberRadios.length > 0) {
        barberRadios.forEach(radio => {
            radio.addEventListener('change', async function() {
                // oznaci vsetky ako interacted
                barberRadios.forEach(r => r.dataset.interacted = 'true');

                validators.barber();
                updateSummary();
                updateBtn();

                // vizualna zmena vybraneho barbera
                document.querySelectorAll('.barber-option').forEach(el => {
                    el.classList.remove('border-gold');
                    el.style.backgroundColor = '';
                });

                if (this.checked) {
                    const parent = this.closest('.barber-option');
                    if (parent) {
                        parent.classList.add('border-gold');
                        parent.style.backgroundColor = 'rgba(212, 175, 55, 0.1)';
                    }
                }

                // aktualuzuj casove sloty
                await updateTimeSlots();
            });
        });
    }

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
        //barber
        const selectedBarber = document.querySelector('input[name="barber_id"]:checked');
        if (selectedBarber) {
            const barberName = selectedBarber.dataset.barberName;
            const summaryBarber = document.getElementById('summaryBarber');
            if (summaryBarber) summaryBarber.textContent = barberName;
        }

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
        const today = new Date().toISOString().split('T')[0];
        if (!dateInput.value) {
            dateInput.value = today;
        }
        // pocakaj na update time slots
        setTimeout(async () => {
            await updateTimeSlots();
        }, 100);
    } else {
        const today = new Date().toISOString().split('T')[0];
        if (dateInput) {
            dateInput.value = today;
            setTimeout(async () => {
                await updateTimeSlots();
            }, 100);
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
    if (!form) return;

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

        price: () => Validator.validate(fields.price, {
            required: true,
            requiredMsg: 'Cena je povinná',
            custom: (v) => {
                const num = parseFloat(v);
                return !isNaN(num) && num > 0 && num <= 10000;
            },
            customMsg: 'Cena musí byť kladné číslo maximálne do 10 000 €'
        }),

        duration: () => Validator.validate(fields.duration, {
            required: true,
            requiredMsg: 'Trvanie je povinné',
            custom: (v) => {
                const num = parseInt(v);
                return !isNaN(num) && num > 0 && num <= 480;
            },
            customMsg: 'Trvanie musí byť kladné celé číslo maximálne 480 minút (8 hodín)'
        }),

        description: () => Validator.validate(fields.description, {
            required: true,
            requiredMsg: 'Popis je povinný',
            custom: (v) => v.trim().length > 0,
            customMsg: 'Zadajte popis služby'
        })
    };

    // Setup polí - všetky používajú setupField()
    setupField(fields.title, validators.title, updateBtn);
    setupField(fields.price, validators.price, updateBtn);
    setupField(fields.duration, validators.duration, updateBtn);
    setupField(fields.description, validators.description, updateBtn);

    // Submit handler - PÔVODNÝ
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

    // Inicializácia - NEVOĽAŤ validátory, iba updateBtn!
    updateBtn();
}

// ADMIN - Vytvorenie barbera
// ADMIN - Vytvorenie barbera - OPRAVENÉ
function initAdminBarberCreate() {
    const form = document.getElementById('createBarberForm');
    if (!form) return;

    const fields = {
        name: document.getElementById('name'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        password: document.getElementById('password'),
        bio: document.getElementById('bio'),
        photo: document.getElementById('photo')
    };

    const submitBtn = form.querySelector('[type="submit"]');

    if (fields.password) {
        createPasswordRequirements(fields.password, 'Heslo pre barbera:');
    }
    if (fields.bio) {
        const bioCounter = document.createElement('div');
        bioCounter.className = 'form-text text-muted';
        bioCounter.id = 'bioCounter';
        bioCounter.textContent = '0/500 znakov';
        fields.bio.parentNode.appendChild(bioCounter);

        fields.bio.addEventListener('input', function() {
            bioCounter.textContent = this.value.length + '/500 znakov';
        });
    }
    if (fields.photo) {
        const previewDiv = document.createElement('div');
        previewDiv.id = 'photoPreview';
        previewDiv.className = 'mt-3 text-center';
        previewDiv.style.display = 'none';
        previewDiv.innerHTML = '<img id="previewImage" src="" alt="Náhľad" class="img-thumbnail" style="max-width: 200px;">';
        fields.photo.parentNode.appendChild(previewDiv);

        fields.photo.addEventListener('change', function() {
            const file = this.files[0];
            const previewImg = document.getElementById('previewImage');

            if (file && previewImg) {
                //validacia klient
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    Validator.showError(this, 'Nepodporovaný formát. Povolené: JPG, PNG, GIF, WebP.');
                    this.value = '';
                    previewDiv.style.display = 'none';
                    updateBtn();
                    return;
                }

                if (file.size > maxSize) {
                    Validator.showError(this, 'Súbor je príliš veľký. Maximálna veľkosť: 2MB.');
                    this.value = '';
                    previewDiv.style.display = 'none';
                    updateBtn();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewDiv.style.display = 'block';
                    Validator.clearError(fields.photo);
                    updateBtn();
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.style.display = 'none';
                updateBtn();
            }
        });
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
            custom: (v) => {
                const trimmed = v.trim();
                return trimmed.length >= 10 && trimmed.length <= 500;
            },
            customMsg: 'Bio musí mať 10-500 znakov'
        }),

        photo: () => {
            if (!fields.photo) return true;

            if (!fields.photo.files || fields.photo.files.length === 0) {
                Validator.showError(fields.photo, 'Fotka je povinná');
                return false;
            }

            const file = fields.photo.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 2 * 1024 * 1024;

            if (!allowedTypes.includes(file.type)) {
                Validator.showError(fields.photo, 'Nepodporovaný formát. Povolené: JPG, PNG, GIF, WebP.');
                return false;
            }

            if (file.size > maxSize) {
                Validator.showError(fields.photo, 'Súbor je príliš veľký. Maximálna veľkosť: 2MB.');
                return false;
            }

            Validator.clearError(fields.photo);
            return true;
        }
    };

    // Setup polí - všetky používajú setupField()
    setupField(fields.name, validators.name, updateBtn);
    setupField(fields.email, validators.email, updateBtn);
    setupField(fields.phone, validators.phone, updateBtn);
    setupField(fields.bio, validators.bio, updateBtn);
    setupField(fields.password, validators.password, updateBtn);

    // file input handler
    if (fields.photo) {
        fields.photo.dataset.interacted = 'false';

        fields.photo.addEventListener('change', function() {
            this.dataset.interacted = 'true';
            validators.photo();
            updateBtn();
        });

        fields.photo.addEventListener('blur', function() {
            this.dataset.interacted = 'true';
            validators.photo();
            updateBtn();
        });
    }

    // Submit handler - PÔVODNÝ
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

    updateBtn();
}

// pridavanie recenzie userom
function initReviewForms() {
    const forms = document.querySelectorAll('form[action*="review.store"]');

    if (forms.length === 0) {
        return;
    }

    forms.forEach(form => {
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }

            // valid vyber hdnotenia
            const select = this.querySelector('select[name="rating"]');
            if (!select || select.value === '') {
                e.preventDefault();
                alert('Prosím vyberte hodnotenie.');
                select.focus();
                return;
            }

            // Disable submit button
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '...';
                submitButton.classList.remove('btn-outline-success');
                submitButton.classList.add('btn-secondary');
            }

            isSubmitting = true;
        });
    });

    // chybove spravy
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    if (error) {
        alert(decodeURIComponent(error));
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

// Hlavná inicializačná funkcia
document.addEventListener('DOMContentLoaded', function() {

    if (document.getElementById('registerForm')) initRegister();
    if (document.getElementById('editForm')) initEdit();
    if (document.getElementById('reservationForm')) initReservation();

    //admin formulare
    if (document.getElementById('createUserForm')) initAdminUserCreate();
    if (document.getElementById('createServiceForm')) initAdminServiceCreate();
    if (document.getElementById('createBarberForm')) initAdminBarberCreate();

    // init formulare na hodnotenie
    initReviewForms();
});