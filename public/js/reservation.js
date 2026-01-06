/*
// public/js/reservation.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reservationForm');
    const dateInput = document.getElementById('reservationDate');
    const timeSelect = document.getElementById('timeSelect');
    const serviceRadios = document.querySelectorAll('input[name="service_id"]');
    const openingHoursNote = document.getElementById('openingHoursNote');

    // Funkcia na získanie dňa v týždni z dátumu (0 = nedeľa, 1 = pondelok, ... 6 = sobota)
    function getDayOfWeek(dateString) {
        const date = new Date(dateString);
        return date.getDay();
    }

    // Funkcia na generovanie časov podľa dňa v týždni
    function generateTimeSlots(dayOfWeek) {
        let timeSlots = [];
        let note = '';

        // Pondelok - Piatok (1-5): 9:00 - 20:00
        if (dayOfWeek >= 1 && dayOfWeek <= 5) {
            note = 'Pondelok-Piatok: 9:00 - 20:00';
            // Generovanie časov každých 30 minút od 9:00 do 19:30
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
            // Generovanie časov každých 30 minút od 8:00 do 22:30
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
            // Generovanie časov každých 30 minút od 8:00 do 22:30
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

    // Funkcia na naplnenie časov podľa vybraného dátumu
    function populateTimeSlots(dateString) {
        if (!dateString) {
            timeSelect.innerHTML = '<option value="">Najprv vyberte dátum</option>';
            if (openingHoursNote) openingHoursNote.textContent = '';
            return;
        }

        const dayOfWeek = getDayOfWeek(dateString);
        const { timeSlots, note } = generateTimeSlots(dayOfWeek);

        // Naplnenie selectu časovými slotmi
        let options = '<option value="">Vyberte čas</option>';

        timeSlots.forEach(timeSlot => {
            options += `<option value="${timeSlot}">${timeSlot}</option>`;
        });

        timeSelect.innerHTML = options;
        if (openingHoursNote) openingHoursNote.textContent = note;

        // Reset výberu
        timeSelect.value = '';
        updateSummary();
    }

    // Funkcia na aktualizáciu zhrnutia
    function updateSummary() {
        // Dátum
        if (dateInput && dateInput.value) {
            const date = new Date(dateInput.value);
            document.getElementById('summaryDate').textContent =
                date.toLocaleDateString('sk-SK', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }

        // Čas
        if (timeSelect && timeSelect.value) {
            document.getElementById('summaryTime').textContent = timeSelect.value;
        }

        // Služba a cena
        const selectedService = document.querySelector('input[name="service_id"]:checked');
        if (selectedService) {
            const serviceName = selectedService.dataset.name;
            const servicePrice = selectedService.dataset.price;

            document.getElementById('summaryService').textContent = serviceName;
            document.getElementById('summaryPrice').textContent = servicePrice + '€';
        }
    }

    // Event listenery
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            populateTimeSlots(this.value);
            updateSummary();
        });
    }

    if (timeSelect) {
        timeSelect.addEventListener('change', updateSummary);
    }

    if (serviceRadios.length > 0) {
        serviceRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                updateSummary();

                // Pridanie vizuálneho efektu pre výber služby
                document.querySelectorAll('.service-option').forEach(el => {
                    el.classList.remove('border-gold');
                    el.style.backgroundColor = '';
                });

                // Pridať označenie vybranej službe
                if (this.checked) {
                    const parent = this.closest('.service-option');
                    if (parent) {
                        parent.classList.add('border-gold');
                        parent.style.backgroundColor = 'rgba(212, 175, 55, 0.1)';
                    }
                }
            });
        });
    }

    // Form validation
    if (form) {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            let firstInvalid = null;

            // Check required fields
            if (!dateInput.value) {
                dateInput.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalid) firstInvalid = dateInput;
            } else {
                dateInput.classList.remove('is-invalid');
            }

            if (!timeSelect.value) {
                timeSelect.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalid) firstInvalid = timeSelect;
            } else {
                timeSelect.classList.remove('is-invalid');
            }

            const selectedService = document.querySelector('input[name="service_id"]:checked');
            if (!selectedService) {
                document.querySelectorAll('input[name="service_id"]').forEach(radio => {
                    const parent = radio.closest('.service-option');
                    if (parent) parent.classList.add('border-danger');
                });
                isValid = false;
            } else {
                document.querySelectorAll('input[name="service_id"]').forEach(radio => {
                    const parent = radio.closest('.service-option');
                    if (parent) parent.classList.remove('border-danger');
                });
            }

            if (!isValid) {
                event.preventDefault();
                if (firstInvalid) firstInvalid.focus();
            }
        });
    }

    // Inicializácia - ak je dátum už vybraný (napr. pri refresh)
    if (dateInput && dateInput.value) {
        populateTimeSlots(dateInput.value);
    } else {
        // Nastaviť dnešný dátum ako predvolený
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
        populateTimeSlots(today);
    }

    // Inicializácia zhrnutia
    updateSummary();
});*/
