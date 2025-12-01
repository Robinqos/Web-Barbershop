<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array $services */
/** @var array $barbers */
/** @var string|null $error */
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="cb-service-card">
                <h2 class="cb-gold-text text-center mb-4">📅 Rezervácia termínu</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= $link->url('reservation.store') ?>" class="needs-validation" novalidate>
                    <div class="row g-4">

                        <!-- KROK 1: VÝBER DATUMU -->
                        <div class="col-lg-6">
                            <div class="cb-step-section p-4 rounded">
                                <h4 class="cb-gold-text mb-3">
                                    <span class="cb-step-number">1</span> Vyberte dátum
                                </h4>

                                <div class="mb-3">
                                    <label class="form-label cb-gold-text fw-bold">Dátum *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar3"></i>
                                        </span>
                                        <input type="date"
                                               id="reservationDate"
                                               name="date"
                                               class="form-control cb-form-control"
                                               min="<?= date('Y-m-d') ?>"
                                               max="<?= date('Y-m-d', strtotime('+60 days')) ?>"
                                               required
                                               onchange="loadAvailableTimes()">
                                        <div class="invalid-feedback">
                                            Prosím vyberte platný dátum.
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Rezervácie možné na 60 dní dopredu
                                    </small>
                                </div>

                                <!-- MINI KALENDÁR -->
                                <div class="calendar-preview mt-3">
                                    <div class="row g-1 mb-2">
                                        <div class="col text-center"><strong class="cb-gold-text">Po</strong></div>
                                        <div class="col text-center"><strong class="cb-gold-text">Ut</strong></div>
                                        <div class="col text-center"><strong class="cb-gold-text">St</strong></div>
                                        <div class="col text-center"><strong class="cb-gold-text">Št</strong></div>
                                        <div class="col text-center"><strong class="cb-gold-text">Pi</strong></div>
                                        <div class="col text-center"><strong class="cb-gold-text">So</strong></div>
                                        <div class="col text-center text-danger"><strong class="cb-gold-text">Ne</strong></div>
                                    </div>
                                    <div id="miniCalendar" class="row g-1"></div>
                                </div>
                            </div>
                        </div>

                        <!-- KROK 2: VÝBER ČASU A SLUŽBY -->
                        <div class="col-lg-6">
                            <div class="cb-step-section p-4 rounded">
                                <h4 class="cb-gold-text mb-3">
                                    <span class="cb-step-number">2</span> Vyberte čas a službu
                                </h4>

                                <!-- Barber -->
                                <div class="mb-4">
                                    <label class="form-label cb-gold-text fw-bold">Barber *</label>
                                    <div class="row g-3" id="barberSelection">
                                        <?php foreach ($barbers as $barberName => $barberData): ?>
                                            <div class="col-md-6">
                                                <div class="barber-card" data-barber="<?= htmlspecialchars($barberName) ?>">
                                                    <input type="radio"
                                                           name="barber"
                                                           value="<?= htmlspecialchars($barberName) ?>"
                                                           id="barber_<?= md5($barberName) ?>"
                                                           class="d-none"
                                                           required>
                                                    <label for="barber_<?= md5($barberName) ?>"
                                                           class="d-block p-3 rounded border cb-barber-card">
                                                        <div class="d-flex align-items-center">
                                                            <div class="barber-avatar me-3">
                                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                                                     style="width: 50px; height: 50px;">
                                                                    <span class="text-white fw-bold"><?= substr($barberName, 0, 1) ?></span>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-1 cb-gold-text"><?= htmlspecialchars($barberName) ?></h5>
                                                                <small class="text-muted"><?= $barberData['experience'] ?? '5+ rokov skúseností' ?></small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="invalid-feedback">
                                        Prosím vyberte barbera.
                                    </div>
                                </div>

                                <!-- Časový slot -->
                                <div class="mb-4">
                                    <label class="form-label cb-gold-text fw-bold">Časový slot *</label>
                                    <div id="timeSlots" class="row g-2">
                                        <!-- Časy sa načítajú dynamicky po výbere dátumu -->
                                        <div class="col-12 text-center py-3">
                                            <p class="text-muted">Najprv vyberte dátum</p>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        Prosím vyberte čas.
                                    </div>
                                </div>

                                <!-- Služba -->
                                <div class="mb-4">
                                    <label class="form-label cb-gold-text fw-bold">Služba *</label>
                                    <div class="row g-3" id="serviceSelection">
                                        <?php foreach ($services as $serviceName => $serviceData): ?>
                                            <div class="col-md-6">
                                                <div class="service-card">
                                                    <input type="radio"
                                                           name="service"
                                                           value="<?= htmlspecialchars($serviceName) ?>"
                                                           id="service_<?= md5($serviceName) ?>"
                                                           class="d-none"
                                                           required
                                                           data-duration="<?= $serviceData['duration'] ?? 30 ?>">
                                                    <label for="service_<?= md5($serviceName) ?>"
                                                           class="d-block p-3 rounded border cb-service-option">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-1 cb-gold-text"><?= htmlspecialchars($serviceName) ?></h6>
                                                                <small class="text-muted">
                                                                    <?= $serviceData['duration'] ?? 30 ?> min
                                                                </small>
                                                            </div>
                                                            <div>
                                                                <span class="cb-price fs-5"><?= $serviceData['price'] ?? 15 ?>€</span>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="invalid-feedback">
                                        Prosím vyberte službu.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KROK 3: DETALY REZERVÁCIE -->
                        <div class="col-12">
                            <div class="cb-step-section p-4 rounded">
                                <h4 class="cb-gold-text mb-3">
                                    <span class="cb-step-number">3</span> Detaily rezervácie
                                </h4>

                                <!-- Kontaktné informácie -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label cb-gold-text fw-bold">Meno *</label>
                                        <input type="text"
                                               name="customer_name"
                                               class="form-control cb-form-control"
                                               value="<?= $this->user->getName() ?>"
                                               readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label cb-gold-text fw-bold">Telefón *</label>
                                        <input type="tel"
                                               name="phone"
                                               class="form-control cb-form-control"
                                               pattern="[0-9]{10,12}"
                                               placeholder="0918123456"
                                               required>
                                        <div class="invalid-feedback">
                                            Zadajte platné telefónne číslo (10-12 číslic).
                                        </div>
                                    </div>
                                </div>

                                <!-- Poznámka -->
                                <div class="mb-4">
                                    <label class="form-label cb-gold-text fw-bold">Poznámka (voliteľné)</label>
                                    <textarea name="notes"
                                              class="form-control cb-form-control"
                                              rows="3"
                                              placeholder="Špeciálne požiadavky, preferencie, alergie..."></textarea>
                                </div>

                                <!-- Zhrnutie -->
                                <div class="reservation-summary bg-dark p-4 rounded mb-4">
                                    <h5 class="cb-gold-text mb-3">Zhrnutie rezervácie</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Dátum:</strong> <span id="summaryDate" class="text-muted">Nie je vybrané</span></p>
                                            <p><strong>Čas:</strong> <span id="summaryTime" class="text-muted">Nie je vybraný</span></p>
                                            <p><strong>Barber:</strong> <span id="summaryBarber" class="text-muted">Nie je vybraný</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Služba:</strong> <span id="summaryService" class="text-muted">Nie je vybraná</span></p>
                                            <p><strong>Trvanie:</strong> <span id="summaryDuration" class="text-muted">-</span></p>
                                            <p><strong>Cena:</strong> <span id="summaryPrice" class="cb-price">0€</span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tlačidlá -->
                                <div class="d-flex justify-content-between">
                                    <a href="<?= $link->url('home.index') ?>" class="btn btn-secondary">
                                        ← Späť na domov
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            Resetovať
                                        </button>
                                        <button type="submit" name="submit" class="btn cb-btn-gold">
                                            ✅ Potvrdiť rezerváciu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Štýly pre rezervačný formulár */
    .cb-step-section {
        background-color: var(--cb-card-bg);
        border: 1px solid var(--cb-card-border);
        border-left: 4px solid var(--cb-gold);
    }

    .cb-step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background-color: var(--cb-gold);
        color: var(--cb-bg);
        border-radius: 50%;
        margin-right: 10px;
        font-weight: bold;
    }

    .cb-form-control {
        background-color: var(--cb-card-bg);
        border: 1px solid var(--cb-card-border);
        color: var(--cb-text);
        transition: all 0.3s ease;
    }

    .cb-form-control:focus {
        background-color: var(--cb-card-bg);
        border-color: var(--cb-gold);
        color: var(--cb-text);
        box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
    }

    .cb-barber-card {
        background-color: var(--cb-card-bg);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .cb-barber-card:hover {
        transform: translateY(-2px);
        border-color: var(--cb-gold) !important;
    }

    .cb-barber-card.selected {
        border-color: var(--cb-gold) !important;
        background-color: rgba(212, 175, 55, 0.1);
    }

    .cb-service-option {
        background-color: var(--cb-card-bg);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .cb-service-option:hover {
        transform: translateY(-2px);
        border-color: var(--cb-gold) !important;
    }

    .cb-service-option.selected {
        border-color: var(--cb-gold) !important;
        background-color: rgba(212, 175, 55, 0.1);
    }

    .time-slot {
        padding: 8px 12px;
        background-color: var(--cb-card-bg);
        border: 1px solid var(--cb-card-border);
        border-radius: 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .time-slot:hover {
        border-color: var(--cb-gold);
        transform: translateY(-1px);
    }

    .time-slot.selected {
        background-color: rgba(212, 175, 55, 0.2);
        border-color: var(--cb-gold);
        color: var(--cb-gold);
    }

    .time-slot.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #333;
    }

    .calendar-day {
        padding: 8px;
        text-align: center;
        background-color: var(--cb-card-bg);
        border: 1px solid transparent;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .calendar-day:hover {
        border-color: var(--cb-gold);
    }

    .calendar-day.selected {
        background-color: rgba(212, 175, 55, 0.2);
        border-color: var(--cb-gold);
        color: var(--cb-gold);
    }

    .calendar-day.disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .barber-avatar {
        width: 50px;
        height: 50px;
    }

    .reservation-summary {
        border: 1px solid var(--cb-card-border);
    }
</style>

<script>
    // Načítanie Bootstrap Icons
    document.head.insertAdjacentHTML('beforeend',
        '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">');

    // Vytvorenie mini kalendára
    function generateMiniCalendar() {
        const container = document.getElementById('miniCalendar');
        container.innerHTML = '';

        const today = new Date();
        const currentMonth = today.getMonth();
        const currentYear = today.getFullYear();

        // Prvý deň v mesiaci
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);

        // Počet dní v mesiaci
        const daysInMonth = lastDay.getDate();

        // Prvý deň v týždni (0 = nedeľa)
        let firstDayIndex = firstDay.getDay();
        if (firstDayIndex === 0) firstDayIndex = 7; // nedeľa posledná

        // Vytvorenie kalendára
        let days = '';

        // Prázdne dni pred prvým dňom
        for (let i = 1; i < firstDayIndex; i++) {
            days += '<div class="col calendar-day disabled"></div>';
        }

        // Dni v mesiaci
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = day === today.getDate() && currentMonth === today.getMonth();
            const isPast = new Date(dateStr) < new Date(today.setHours(0,0,0,0));
            const isWeekend = new Date(currentYear, currentMonth, day).getDay() === 0; // nedeľa

            let dayClass = 'calendar-day';
            if (isToday) dayClass += ' selected';
            if (isPast || isWeekend) dayClass += ' disabled';

            days += `<div class="col ${dayClass}"
                     onclick="${isPast || isWeekend ? '' : `selectDate('${dateStr}')`}"
                     ${isPast || isWeekend ? 'style="opacity:0.5"' : ''}>
                    ${day}
                 </div>`;
        }

        container.innerHTML = days;

        // Nastavíme dnešný dátum ako predvolený
        const todayStr = today.toISOString().split('T')[0];
        document.getElementById('reservationDate').value = todayStr;
        updateSummary();
    }

    // Výber dátumu
    function selectDate(dateStr) {
        document.getElementById('reservationDate').value = dateStr;
        generateMiniCalendar();
        loadAvailableTimes();
        updateSummary();
    }

    // Načítanie dostupných časov
    function loadAvailableTimes() {
        const dateInput = document.getElementById('reservationDate');
        const timeSlots = document.getElementById('timeSlots');

        if (!dateInput.value) {
            timeSlots.innerHTML = '<div class="col-12 text-center py-3"><p class="text-muted">Najprv vyberte dátum</p></div>';
            return;
        }

        // Kontrola víkendu
        const selectedDate = new Date(dateInput.value);
        const isSunday = selectedDate.getDay() === 0;

        if (isSunday) {
            timeSlots.innerHTML = '<div class="col-12 text-center py-3"><p class="text-danger">Nedeľa - zatvorené</p></div>';
            return;
        }

        // Generovanie časov od 9:00 do 20:00 po 30 minútach
        const times = [];
        const startHour = 9;
        const endHour = 20;

        for (let hour = startHour; hour < endHour; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                if (hour === endHour - 1 && minute >= 30) break; // posledný čas 19:30
                const timeStr = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                times.push(timeStr);
            }
        }

        // Zobrazenie časov
        let html = '';
        times.forEach(time => {
            // Náhodne "obsadíme" niektoré časy pre realističnosť
            const isBooked = Math.random() > 0.7;

            html += `
            <div class="col-6 col-md-3">
                <div class="time-slot ${isBooked ? 'disabled' : ''}"
                     onclick="${isBooked ? '' : `selectTime('${time}')`}">
                    ${time}
                    ${isBooked ? '<br><small class="text-danger">obsadené</small>' : ''}
                </div>
            </div>
        `;
        });

        timeSlots.innerHTML = html;
    }

    // Výber času
    function selectTime(time) {
        // Odstrániť výber zo všetkých časov
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });

        // Pridať výber aktuálnemu
        event.target.classList.add('selected');
        updateSummary();
    }

    // Výber barbera
    document.querySelectorAll('.barber-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.cb-barber-card').forEach(bc => {
                bc.classList.remove('selected');
            });
            this.querySelector('.cb-barber-card').classList.add('selected');
            updateSummary();
        });
    });

    // Výber služby
    document.querySelectorAll('.service-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.cb-service-option').forEach(so => {
                so.classList.remove('selected');
            });
            this.querySelector('.cb-service-option').classList.add('selected');
            updateSummary();
        });
    });

    // Aktualizácia zhrnutia
    function updateSummary() {
        const dateInput = document.getElementById('reservationDate');
        const selectedBarber = document.querySelector('input[name="barber"]:checked');
        const selectedService = document.querySelector('input[name="service"]:checked');
        const selectedTime = document.querySelector('.time-slot.selected');

        // Dátum
        if (dateInput.value) {
            const date = new Date(dateInput.value);
            document.getElementById('summaryDate').textContent =
                date.toLocaleDateString('sk-SK', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }

        // Čas
        document.getElementById('summaryTime').textContent =
            selectedTime ? selectedTime.textContent.split('<')[0] : 'Nie je vybraný';

        // Barber
        document.getElementById('summaryBarber').textContent =
            selectedBarber ? selectedBarber.value : 'Nie je vybraný';

        // Služba
        if (selectedService) {
            const serviceName = selectedService.value;
            const serviceDuration = selectedService.dataset.duration;
            const servicePrice = selectedService.closest('.service-card').querySelector('.cb-price').textContent;

            document.getElementById('summaryService').textContent = serviceName;
            document.getElementById('summaryDuration').textContent = `${serviceDuration} minút`;
            document.getElementById('summaryPrice').textContent = servicePrice;
        } else {
            document.getElementById('summaryService').textContent = 'Nie je vybraná';
            document.getElementById('summaryDuration').textContent = '-';
            document.getElementById('summaryPrice').textContent = '0€';
        }
    }

    // Form validation
    (function () {
        'use strict'

        const forms = document.querySelectorAll('.needs-validation')

        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })

        // Realtime validation
        document.querySelectorAll('.cb-form-control').forEach(input => {
            input.addEventListener('input', () => {
                if (input.checkValidity()) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                } else {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                }
            });
        });
    })()

    // Inicializácia po načítaní stránky
    document.addEventListener('DOMContentLoaded', function() {
        generateMiniCalendar();
        loadAvailableTimes();

        // Nastavíme change event pre dátum
        document.getElementById('reservationDate').addEventListener('change', function() {
            generateMiniCalendar();
            loadAvailableTimes();
            updateSummary();
        });

        // Nastavíme summary na real-time aktualizáciu
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('change', updateSummary);
        });
    });
</script>