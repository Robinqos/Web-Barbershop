<?php

namespace App\Controllers;

use App\Models\Barber;
use App\Models\Service;
use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Reservation;

class ReservationController extends BaseController
{
    public function create(Request $request): Response
    {
        $services = Service::getAll();
        $user = $this->app->getAuthenticator()->getUser();

        $barbers = Barber::getAll('is_active = ?', [1], 'id ASC');
        return $this->html([
            'services' => $services,
            'user' => $user,
            'barbers' => $barbers
        ]);
    }

    public function index(Request $request): Response
    {
        if (!$this->app->getAppUser()->isLoggedIn()) {
            return $this->redirect($this->url("auth.login"));
        }

        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();

        if (!$identity instanceof User) {
            return $this->redirect($this->url("auth.login"));
        }

        $user = $identity;
        $reservations = \App\Models\Reservation::getAll(
            'user_id = ?',
            [$user->getId()],
            'reservation_date DESC'
        );

        return $this->html(compact('reservations'));
    }
    // AJAX
    public function getOccupiedTimes(Request $request): Response
    {
        $barberId = (int) $request->value('barber_id');
        $date = $request->value('date');

        if (!$barberId || !$date) {
            return $this->json(['error' => 'Missing parameters', 'debug' => ['barber_id' => $barberId, 'date' => $date]]);
        }

        // rezervacia barbera v tej den
        $reservations = Reservation::getAll(
            'barber_id = ? AND DATE(reservation_date) = ? AND status IN (?, ?)',
            [$barberId, $date, Reservation::STATUS_PENDING, Reservation::STATUS_COMPLETED]
        );


        $occupiedTimes = [];

        foreach ($reservations as $reservation) {
            $reservationDate = new \DateTime($reservation->getReservationDate());
            $timeSlot = $reservationDate->format('H:i');

            //pridaj cas rezervacie
            $occupiedTimes[] = $timeSlot;

            // ak trva 60 tak aj dalsi slot
            $service = $reservation->getService();
            if ($service && $service->getDuration() == 60) {
                $nextTime = (clone $reservationDate)->modify('+30 minutes');
                $nextTimeSlot = $nextTime->format('H:i');
                $occupiedTimes[] = $nextTimeSlot;
            }
        }

        // odstrani duplikaty
        $occupiedTimes = array_unique($occupiedTimes);
        sort($occupiedTimes);

        return $this->json($occupiedTimes);
    }

    public function store(Request $request): Response
    {
        if (!$request->hasValue('submit')) {
            return $this->redirect($this->url("reservation.create"));
        }

        $errors = [];

        $barberId = $request->value('barber_id');
        if (!$barberId) {
            $errors[] = 'Neplatné údaje';
        } else {
            $barber = \App\Models\Barber::getOne($barberId);
            if (!$barber || !$barber->getIsActive()) {
                $errors[] = 'Neplatné údaje';
            }
        }

        // validacia
        if (!$request->value('service_id')) {
            $errors[] = 'Neplatné údaje';
        }

        if (!$request->value('date') || !$request->value('time')) {
            $errors[] = 'Neplatné údaje';
        }

        // datum + cas
        $reservationDate = $request->value('date') . ' ' . $request->value('time') . ':00';

        // kontrola datumu a udajov
        if (strtotime($reservationDate) < time()) {
            $errors[] = 'Neplatné údaje';
        }
        //obsadenost barbera
        if ($barberId && $request->value('date') && $request->value('time') && $request->value('service_id')) {
            $service = Service::getOne((int)$request->value('service_id'));
            $serviceDuration = $service ? $service->getDuration() : 30;

            // ci je obsadeny vtedy
            $occupied = $this->isBarberOccupied(
                $barberId,
                $reservationDate,
                $serviceDuration
            );

            if ($occupied) {
                $errors[] = 'Barber je v tomto čase už obsadený. Vyberte iný čas.';
            }
        }

        $isLoggedIn = $this->app->getAppUser()->isLoggedIn();

        if (!$isLoggedIn) {
            if (empty($request->value('guest_name'))) {
                $errors[] = 'Neplatné údaje';
            }else {
                $name = trim($request->value('guest_name'));
                if (strlen(str_replace(' ', '', $name)) < 4) {
                    $errors[] = 'Neplatné údaje';
                }
            }
            if (empty($request->value('guest_phone'))) {
                $errors[] = 'Neplatné údaje';
            } else {
                // Kontrola telefónu (9-15 číslic)
                $phone = trim($request->value('guest_phone'));
                $digits = preg_replace('/\D/', '', $phone);
                if (strlen($digits) < 9 || strlen($digits) > 15) {
                    $errors[] = 'Neplatné údaje';
                }
            }

            if (empty($request->value('guest_email'))) {
                $errors[] = 'Neplatné údaje';
            } elseif (!filter_var($request->value('guest_email'), FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Neplatné údaje';
            }
        }

        $note = $request->value('note') ?? '';
        if (strlen($note) > 70) {
            $errors[] = 'Neplatné údaje';
        }

        if (!empty($errors)) {
            $services = \App\Models\Service::getAll();
            $user = $this->app->getAuthenticator()->getUser();
            $barbers = Barber::getAll('is_active = ?', [1], 'created_at ASC');

            $errorMessage = 'Formulár obsahuje chyby. Skontrolujte zadané údaje.';

            return $this->html([
                'services' => $services,
                'user' => $user,
                'barbers' => $barbers,
                'error' => $errorMessage,
                'old' => $this->app->getRequest()->post()
            ], 'create');
        }

        $reservation = new Reservation();
        $reservation->setServiceId((int)$request->value('service_id'));
        $reservation->setBarberId((int)$barberId);
        $reservation->setReservationDate($reservationDate);
        $reservation->setStatus(Reservation::STATUS_PENDING);
        $reservation->setNote($note);

        date_default_timezone_set('Europe/Bratislava');
        $reservation->setCreatedAt(date('Y-m-d H:i:s'));

        // host/prihlaseny
        if ($isLoggedIn) {
            $user = $this->app->getAuthenticator()->getUser();
            $reservation->setUserId($user->getId());
        } else {
            $reservation->setUserId(null);
            $reservation->setGuestName($request->value('guest_name'));
            $reservation->setGuestPhone($request->value('guest_phone'));
            $reservation->setGuestEmail($request->value('guest_email'));
        }

        $reservation->save();

        return $this->redirect($this->url("reservation.confirm", ['id' => $reservation->getId()]));
    }

    public function confirm(Request $request): Response
    {
        $id = (int) $request->value('id');
        $reservation = Reservation::getOne($id);

        if (!$reservation) {
            return $this->redirect($this->url("home.index"));
        }

        $service = $reservation->getService();
        $barber = $reservation->getBarber();

        return $this->html([
            'reservation' => $reservation,
            'service' => $service,
            'barber' => $barber
        ]);
    }

    public function cancel(Request $request): Response
    {
        $id = (int) $request->value('id');
        $reservation = \App\Models\Reservation::getOne($id);

        if (!$reservation) {
            return $this->redirect($this->url("home.index"));
        }

        if (!$this->app->getAppUser()->isLoggedIn()) {
            return $this->redirect($this->url("auth.login"));
        }

        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();

        if (!$identity instanceof User) {
            return $this->redirect($this->url("auth.login"));
        }

        $user = $identity;

        // kontrola ci su jeho
        if ($reservation->getUserId() !== $user->getId()) {
            return $this->redirect($this->url("reservation.index"));
        }

        // kontrola stavu
        if (!$reservation->isPending()) {
            return $this->redirect($this->url("reservation.index"));
        }

        $reservation->cancel();

        return $this->redirect($this->url("auth.index"));
    }

    private function isBarberOccupied(int $barberId, string $reservationDateTime, int $serviceDuration): bool
    {
        $reservationTime = new \DateTime($reservationDateTime);
        $reservationEnd = (clone $reservationTime)->modify("+{$serviceDuration} minutes");

        // rezervacie v dany den
        $reservations = Reservation::getAll(
            'barber_id = ? AND DATE(reservation_date) = ? AND status IN (?, ?)',
            [
                $barberId,
                $reservationTime->format('Y-m-d'),
                Reservation::STATUS_PENDING,
                Reservation::STATUS_COMPLETED
            ]
        );

        foreach ($reservations as $reservation) {
            $existingStart = new \DateTime($reservation->getReservationDate());
            $existingService = $reservation->getService();
            $existingEnd = (clone $existingStart)->modify("+{$existingService->getDuration()} minutes");

            // prekrytie casov
            if ($reservationTime < $existingEnd && $reservationEnd > $existingStart) {
                return true; //obsadeny
            }
        }

        return false; // barber volny
    }
}