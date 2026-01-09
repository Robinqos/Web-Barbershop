<?php

namespace App\Controllers;

use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Reservation;

class ReservationController extends BaseController
{
    public function create(Request $request): Response
    {
        $services = \App\Models\Service::getAll();
        $user = $this->app->getAuthenticator()->getUser();

        return $this->html([
            'services' => $services,
            'user' => $user
        ]);
    }

    public function index(Request $request): Response
    {
        if (!$this->app->getAppUser()->isLoggedIn()) {
            return $this->redirect($this->url("auth.login"));
        }

        $user = $this->app->getAuthenticator()->getUser();
        $reservations = \App\Models\Reservation::getAll(
            'user_id = ?',
            [$user->getId()],
            'reservation_date DESC'
        );

        return $this->html(compact('reservations'));
    }

    public function store(Request $request): Response
    {
        if (!$request->hasValue('submit')) {
            return $this->redirect($this->url("reservation.create"));
        }

        $errors = [];

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

            $errorMessage = 'Formulár obsahuje chyby. Skontrolujte zadané údaje.';

            return $this->html([
                'services' => $services,
                'user' => $user,
                'error' => $errorMessage,
                'old' => $this->app->getRequest()->post()
            ], 'create');
        }

        $reservation = new Reservation();
        $reservation->setServiceId((int)$request->value('service_id'));
        $reservation->setReservationDate($reservationDate);
        $reservation->setStatus(Reservation::STATUS_PENDING);
        $reservation->setNote($note);

        date_default_timezone_set('Europe/Bratislava');
        $reservation->setCreatedAt(date('Y-m-d H:i:s'));

        // host/prihlaseny
        if ($isLoggedIn) {
            $user = $this->app->getAuthenticator()->getUser();
            $reservation->setUserId($user->getId());
            //todo:mozno pridat to, aby sa userove udaje skopirovali do rezervacie
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
        $reservation = \App\Models\Reservation::getOne($id);

        if (!$reservation) {
            return $this->redirect($this->url("home.index"));
        }

        $service = $reservation->getService();

        return $this->html([
            'reservation' => $reservation,
            'service' => $service
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

        $user = $this->app->getAuthenticator()->getUser();

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
}