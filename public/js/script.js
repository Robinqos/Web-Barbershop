// public/js/script.js - OPRAVENÁ VERZIA

document.addEventListener('DOMContentLoaded', function() {
    // REGISTRAČNÝ FORMULÁR
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        initRegisterValidation();
    }
});

function initRegisterValidation() {
    const form = document.getElementById('registerForm');
    if (!form) return;

    // Získanie referencií na prvky
    const fullNameInput = document.getElementById('full_name');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirm');
    const termsCheckbox = document.querySelector('[name="terms"]');
    const submitBtn = form.querySelector('[type="submit"]');

    // Ak existuje pole na heslo, pridáme požiadavky
    if (passwordInput && !document.getElementById('passwordRequirements')) {
        const requirementsDiv = document.createElement('div');
        requirementsDiv.id = 'passwordRequirements';
        requirementsDiv.className = 'form-text mt-1';
        requirementsDiv.innerHTML = `
            <ul class="mb-0" style="list-style-type: none; padding-left: 0;">
                <li id="reqLength" class="text-danger">✗ Minimálne 8 znakov</li>
                <li id="reqUpper" class="text-danger">✗ Aspoň jedno veľké písmeno</li>
                <li id="reqNumber" class="text-danger">✗ Aspoň jednu číslicu</li>
            </ul>
        `;
        passwordInput.parentNode.appendChild(requirementsDiv);
    }

    // Event listenery
    if (fullNameInput) {
        fullNameInput.addEventListener('input', validateFullName);
        fullNameInput.addEventListener('blur', validateFullName);
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            validatePhone();
            updateSubmitButton();
        });
        phoneInput.addEventListener('blur', function() {
            validatePhone();
            updateSubmitButton();
        });
    }

    if (emailInput) {
        emailInput.addEventListener('input', function() {
            validateEmail();
            updateSubmitButton();
        });
        emailInput.addEventListener('blur', function() {
            validateEmail();
            updateSubmitButton();
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            validatePassword();
            updatePasswordRequirements(this.value);
            validatePasswordConfirm(); // Aktualizovať potvrdenie hesla
            updateSubmitButton();
        });
        passwordInput.addEventListener('blur', function() {
            validatePassword();
            updateSubmitButton();
        });
    }

    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('input', function() {
            validatePasswordConfirm();
            updateSubmitButton();
        });
        passwordConfirmInput.addEventListener('blur', function() {
            validatePasswordConfirm();
            updateSubmitButton();
        });
    }

    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', function() {
            validateTerms();
            updateSubmitButton(); // Pridané - aktualizuje tlačidlo hneď po zmene
        });
    }

    // Validácia celého formulára pri odoslaní
    form.addEventListener('submit', function(event) {
        let isFormValid = true;

        if (phoneInput && !validatePhone()) isFormValid = false;
        if (emailInput && !validateEmail()) isFormValid = false;
        if (passwordInput && !validatePassword()) isFormValid = false;
        if (passwordConfirmInput && !validatePasswordConfirm()) isFormValid = false;
        if (termsCheckbox && !validateTerms()) isFormValid = false;
        // Meno a priezvisko nie je povinné, ale ak je vyplnené, musí byť validné
        if (fullNameInput && fullNameInput.value.trim() !== '' && !validateFullName()) isFormValid = false;

        if (!isFormValid) {
            event.preventDefault();
            event.stopPropagation();

            // Zamerať prvé chybné pole
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
            }
        } else {
            console.log('Formulár je validný, odosielam...');
        }
    });

    // VALIDAČNÉ FUNKCIE
    function validateFullName() {
        const originalValue = fullNameInput.value;
        const name = originalValue.trim();
        const helpElement = document.getElementById('full_name_help');

        // Pole nie je povinné, ak je prázdne, je OK
        if (!name) {
            clearError(fullNameInput, helpElement);
            return true;
        }

        // Kontrola, či nie je len z medzier (musí obsahovať aspoň 2 ne-medzerové znaky)
        const nonSpaceChars = name.replace(/\s/g, '');
        if (nonSpaceChars.length < 2) {
            showError(fullNameInput, helpElement, 'Meno musí obsahovať aspoň 2 ne-medzerové znaky');
            return false;
        }

        clearError(fullNameInput, helpElement);
        return true;
    }

    function validatePhone() {
        const phone = phoneInput.value.trim();
        const helpElement = document.getElementById('phone_help');

        if (!phone) {
            showError(phoneInput, helpElement, 'Telefónne číslo je povinné');
            return false;
        }

        const cleanValue = phone.replace(/[\s\-\(\)]/g, '');
        const digitCount = cleanValue.replace(/\D/g, '').length;

        if (digitCount < 9 || digitCount > 15) {
            showError(phoneInput, helpElement, 'Telefónne číslo musí obsahovať 9-15 číslic');
            return false;
        }

        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        if (!phoneRegex.test(phone)) {
            showError(phoneInput, helpElement, 'Telefón môže obsahovať iba čísla, medzery, +, - a ()');
            return false;
        }

        clearError(phoneInput, helpElement);
        return true;
    }

    function validateEmail() {
        const email = emailInput.value.trim();
        const helpElement = document.getElementById('email_help');

        if (!email) {
            showError(emailInput, helpElement, 'Email je povinný');
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError(emailInput, helpElement, 'Zadajte platný email (napr. priklad@email.sk)');
            return false;
        }

        clearError(emailInput, helpElement);
        return true;
    }

    function validatePassword() {
        const password = passwordInput.value;
        const helpElement = document.getElementById('password_help');

        if (!password) {
            showError(passwordInput, helpElement, 'Heslo je povinné');
            return false;
        }

        // Kontrola jednotlivých požiadaviek
        const isLengthValid = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);

        if (!isLengthValid || !hasUpper || !hasNumber) {
            // Nezobrazujeme všeobecnú chybovú správu, iba požiadavky
            clearError(passwordInput, helpElement);
            return false;
        }

        clearError(passwordInput, helpElement);
        return true;
    }

    function validatePasswordConfirm() {
        const password = passwordInput ? passwordInput.value : '';
        const confirmPassword = passwordConfirmInput.value;
        const helpElement = document.getElementById('password_confirm_help');

        if (!confirmPassword) {
            showError(passwordConfirmInput, helpElement, 'Potvrdenie hesla je povinné');
            return false;
        }

        if (password !== confirmPassword) {
            showError(passwordConfirmInput, helpElement, 'Heslá sa nezhodujú');
            return false;
        }

        clearError(passwordConfirmInput, helpElement);
        return true;
    }

    function validateTerms() {
        const helpElement = document.getElementById('terms_help');

        if (!termsCheckbox.checked) {
            if (helpElement) {
                helpElement.textContent = 'Musíte súhlasiť so spracovaním osobných údajov';
                helpElement.classList.remove('text-muted');
                helpElement.classList.add('text-danger');
            }
            return false;
        }

        if (helpElement) {
            helpElement.textContent = '';
            helpElement.classList.remove('text-danger');
            helpElement.classList.add('text-muted');
        }
        return true;
    }

    // POMOCNÉ FUNKCIE
    function showError(field, helpElement, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');

        if (helpElement) {
            helpElement.textContent = message;
            helpElement.classList.remove('text-muted');
            helpElement.classList.add('text-danger');
        } else {
            // Vytvoriť help element ak neexistuje
            const errorDiv = document.createElement('div');
            errorDiv.id = field.id + '_help';
            errorDiv.className = 'form-text text-danger';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }
    }

    function clearError(field, helpElement) {
        field.classList.remove('is-invalid');
        field.classList.remove('is-valid'); // Nechceme zelené okienko

        if (helpElement) {
            helpElement.textContent = '';
            helpElement.classList.remove('text-danger');
            helpElement.classList.add('text-muted');
        }
    }

    function updatePasswordRequirements(value) {
        const reqLength = document.getElementById('reqLength');
        const reqUpper = document.getElementById('reqUpper');
        const reqNumber = document.getElementById('reqNumber');

        if (!reqLength || !reqUpper || !reqNumber) return;

        // Aktualizácia jednotlivých požiadaviek
        updateRequirement(reqLength, value.length >= 8, 'Minimálne 8 znakov');
        updateRequirement(reqUpper, /[A-Z]/.test(value), 'Aspoň jedno veľké písmeno');
        updateRequirement(reqNumber, /[0-9]/.test(value), 'Aspoň jednu číslicu');

        function updateRequirement(element, condition, text) {
            if (condition) {
                element.classList.remove('text-danger');
                element.classList.add('text-success');
                element.innerHTML = '✓ ' + text;
            } else {
                element.classList.remove('text-success');
                element.classList.add('text-danger');
                element.innerHTML = '✗ ' + text;
            }
        }
    }

    function updateSubmitButton() {
        if (!submitBtn) return;

        // Skontrolovať všetky povinné podmienky
        let allValid = true;

        // Telefón
        if (phoneInput) {
            const phone = phoneInput.value.trim();
            if (!phone) {
                allValid = false;
            } else {
                const cleanValue = phone.replace(/[\s\-\(\)]/g, '');
                const digitCount = cleanValue.replace(/\D/g, '').length;
                const phoneRegex = /^[\d\s\-\+\(\)]+$/;

                if (digitCount < 9 || digitCount > 15 || !phoneRegex.test(phone)) {
                    allValid = false;
                }
            }
        }

        // Email
        if (emailInput) {
            const email = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                allValid = false;
            }
        }

        // Heslo
        if (passwordInput) {
            const password = passwordInput.value;
            if (!password || password.length < 8 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                allValid = false;
            }
        }

        // Potvrdenie hesla
        if (passwordConfirmInput) {
            const password = passwordInput ? passwordInput.value : '';
            const confirmPassword = passwordConfirmInput.value;
            if (!confirmPassword || password !== confirmPassword) {
                allValid = false;
            }
        }

        // Súhlas
        if (termsCheckbox && !termsCheckbox.checked) {
            allValid = false;
        }

        // Meno (nie je povinné, ale ak je vyplnené, musí byť validné)
        if (fullNameInput && fullNameInput.value.trim() !== '') {
            const name = fullNameInput.value.trim();
            const nonSpaceChars = name.replace(/\s/g, '');
            if (nonSpaceChars.length < 2) {
                allValid = false;
            }
        }

        // Aktualizovať stav tlačidla
        if (!allValid) {
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-disabled');
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-disabled');
        }
    }

    // Inicializácia - skontrolovať už vyplnené hodnoty
    if (fullNameInput && fullNameInput.value.trim()) validateFullName();
    if (phoneInput && phoneInput.value.trim()) validatePhone();
    if (emailInput && emailInput.value.trim()) validateEmail();
    if (passwordInput && passwordInput.value) {
        validatePassword();
        updatePasswordRequirements(passwordInput.value);
    }
    if (passwordConfirmInput && passwordConfirmInput.value) validatePasswordConfirm();
    if (termsCheckbox) validateTerms();

    // Aktualizovať tlačidlo pri načítaní
    updateSubmitButton();
}