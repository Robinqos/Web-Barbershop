/*
// public/js/script.js

document.addEventListener('DOMContentLoaded', function() {
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

    // spolocne funkcie- pripoji event listenery na polia
    //okamzite kontroluje format
    function setupField(field, validatorFn, updateBtnFn = null) {
        if (!field) return;

        const handler = () => {
            validatorFn();
            if (updateBtnFn) updateBtnFn();
        };

        field.addEventListener('input', handler);
        field.addEventListener('blur', handler);
    }

    //interaktivny zoznam poziadaviek na heslo
    function createPasswordRequirements(field, note = '') {
        if (!field) return;

        // Skontrolovať, či už existujú požiadavky pre toto pole
        const reqId = field.id + '_requirements';
        if (document.getElementById(reqId)) return;

        const div = document.createElement('div');
        div.id = reqId;
        div.className = 'form-text mt-1 password-requirements';
        div.style.display = 'none'; // Štandardne skryté
        div.innerHTML = (note ? `<div class="small">${note}</div>` : '') + `
            <ul class="mb-0" style="list-style-type: none; padding-left: 0;">
                <li id="${reqId}_length" class="text-danger">✗ Minimálne 8 znakov</li>
                <li id="${reqId}_upper" class="text-danger">✗ Aspoň jedno veľké písmeno</li>
                <li id="${reqId}_number" class="text-danger">✗ Aspoň jednu číslicu</li>
            </ul>`;
        field.parentNode.appendChild(div);

        // Event listener pre zobrazenie/skrytie poziadaviek
        field.addEventListener('input', function() {
            const v = this.value;
            const requirementsDiv = document.getElementById(reqId);

            // Ak je pole prazdne, skry
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

        // Skryt poziadavky pri strate focusu, ak je pole prazdne
        field.addEventListener('blur', function() {
            const requirementsDiv = document.getElementById(reqId);
            if (!this.value.trim() && requirementsDiv) {
                requirementsDiv.style.display = 'none';
            }
        });

        // Skryt poziadavky pri nacitani, ak je pole prázdne
        if (!field.value.trim()) {
            const requirementsDiv = document.getElementById(reqId);
            if (requirementsDiv) {
                requirementsDiv.style.display = 'none';
            }
        }
    }

    //REGISTRACNY FORMULAR
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

        // vzdy zobrazit poziadavky na heslo
        createPasswordRequirements(fields.pass);

        // Validacia poli
        const validators = {
            name: () => Validator.validate(fields.name, {
                custom: (v) => v === '' || v.replace(/\s/g, '').length >= 2,
                customMsg: 'Meno musí obsahovať aspoň 2 ne-medzerové znaky'
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

        // Event listenery
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

        // Submit handler - reset poziadaviek po uspesnom odoslani
        form.addEventListener('submit', function(e) {
            const isValid = Object.values(validators).every(fn => fn());
            if (!isValid) {
                e.preventDefault();
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.focus();
            } else {
                // Po úspešnom odoslaní skryť požiadavky na heslo
                const reqDiv = document.getElementById('password_requirements');
                if (reqDiv) {
                    reqDiv.style.display = 'none';
                }
            }
        });

        // aktualizacia tlacidla
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

    // EDIT FORMULAR
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

        // zobrazit len ak sa meni heslo
        createPasswordRequirements(fields.newPass, 'Heslo vyplňte len ak ho chcete zmeniť:');

        // Validacia poli
        const validators = {
            name: () => Validator.validate(fields.name, {
                custom: (v) => v === '' || v.replace(/\s/g, '').length >= 2,
                customMsg: 'Meno musí obsahovať aspoň 2 ne-medzerové znaky'
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

        // Event listenery
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

            // zobrazit ked klikne
            fields.newPass.addEventListener('focus', function() {
                const reqDiv = document.getElementById('new_password_requirements');
                if (reqDiv) {
                    reqDiv.style.display = 'block';
                }
            });

            // skryt ak je prazdne
            fields.newPass.addEventListener('blur', function() {
                const reqDiv = document.getElementById('new_password_requirements');
                if (reqDiv && !this.value.trim()) {
                    reqDiv.style.display = 'none';
                }
            });
        }

        setupField(fields.currentPass, validators.currentPass, updateBtn);
        setupField(fields.confirmPass, validators.confirmPass, updateBtn);

        // Submit handler
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

        // tlacidlo aktualizacia
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

        // inicializacia
        Object.values(validators).forEach(fn => fn());
        updateBtn();
    }

    // SPUSTENIE
    if (document.getElementById('registerForm')) initRegister();
    if (document.getElementById('editForm')) initEdit();
});
*/
