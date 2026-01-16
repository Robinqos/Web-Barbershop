<?php

namespace App\Controllers;

use App\Models\Barber;
use App\Models\Reservation;
use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class BarberController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->user->isLoggedIn()) {
            return false;
        }

        $userModel = $this->app->getAuthenticator()->getUser();

        if (!$userModel) {
            return false;
        }

        return $userModel->getPermissions() === User::ROLE_BARBER;
    }

    public function index(Request $request): Response
    {
        $user = $this->app->getAuthenticator()->getUser();

        // barber z tohoto pouzivatela
        $barber = Barber::getByUserId($user->getId());

        if (!$barber) {
            return $this->html([
                'error' => 'Barber profil nebol nájdený',
                'user' => $user
            ]);
        }

        // vsetky tohoto barbera
        $today = date('Y-m-d');

        // dnes
        $todayReservations = Reservation::getAll(
            'barber_id = ? AND DATE(reservation_date) = ? AND status = "pending"',
            [$barber->getId(), $today],
            'reservation_date ASC'
        );

        // nadchadzajuec
        $upcomingReservations = Reservation::getAll(
            'barber_id = ? AND reservation_date > NOW() AND status = "pending"',
            [$barber->getId()],
            'reservation_date ASC',
            10
        );

        $allReservations = Reservation::getAll(
            'barber_id = ? AND status IN ("pending", "completed")',
            [$barber->getId()],
            'reservation_date DESC'
        );

        $totalReservations = count($allReservations);
        $todayReservationsCount = count($todayReservations);

        return $this->html([
            'user' => $user,
            'barber' => $barber,
            'todayReservations' => $todayReservations,
            'upcomingReservations' => $upcomingReservations,
            'allReservations' => $allReservations,
            'totalReservations' => $totalReservations,
            'todayReservationsCount' => $todayReservationsCount
        ], 'index');
    }

    public function cancelReservation(Request $request): Response
    {
        $id = (int) $request->value('id');
        $reservation = Reservation::getOne($id);

        $user = $this->app->getAuthenticator()->getUser();
        $barber = Barber::getByUserId($user->getId());

        if (!$reservation || !$barber) {
            return $this->redirect($this->url("barber.index"));
        }

        // ci mu patria
        if ($reservation->getBarberId() !== $barber->getId()) {
            return $this->redirect($this->url("barber.index"));
        }

        // iba cakajuce mozu by zrusene
        if (!$reservation->isPending()) {
            return $this->redirect($this->url("barber.index"));
        }

        $reservation->cancel();

        return $this->redirect($this->url("barber.index"));
    }

    public function completeReservation(Request $request): Response
    {
        $id = (int) $request->value('id');
        $reservation = Reservation::getOne($id);

        $user = $this->app->getAuthenticator()->getUser();
        $barber = Barber::getByUserId($user->getId());

        if (!$reservation || !$barber) {
            return $this->redirect($this->url("barber.index"));
        }

        // ci mu patria
        if ($reservation->getBarberId() !== $barber->getId()) {
            return $this->redirect($this->url("barber.index"));
        }

        // iba pending mozu by cancelled
        if (!$reservation->isPending()) {
            return $this->redirect($this->url("barber.index"));
        }

        $reservation->setStatus('completed');
        $reservation->save();

        return $this->redirect($this->url("barber.index"));
    }
}