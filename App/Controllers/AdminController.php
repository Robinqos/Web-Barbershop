<?php

namespace App\Controllers;

use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class AdminController
 *
 * This controller manages admin-related actions within the application.It extends the base controller functionality
 * provided by BaseController.
 *
 * @package App\Controllers
 */
class AdminController extends BaseController
{
    /**
     * Authorizes actions in this controller.
     *
     * This method checks if the user is logged in, allowing or denying access to specific actions based
     * on the authentication state.
     *
     * @param string $action The name of the action to authorize.
     * @return bool Returns true if the user is logged in; false otherwise.
     */
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->user->isLoggedIn()) {
            return false;
        }

        $userModel = $this->app->getAuthenticator()->getUser();

        if (!$userModel) {
            return false;
        }
        //admin = 2
        return $userModel->getPermissions() >= User::ROLE_ADMIN;
    }

    /**
     * Displays the index page of the admin panel.
     *
     * This action requires authorization. It returns an HTML response for the admin dashboard or main page.
     *
     * @return Response Returns a response object containing the rendered HTML.
     */
    public function index(Request $request): Response
    {
        $userModel = $this->app->getAuthenticator()->getUser();

        $today = date('Y-m-d');

        $todayReservations = \App\Models\Reservation::getAll(
            'DATE(reservation_date) = ? AND status = "pending"',
            [$today],
            'reservation_date ASC'
        );

        $upcomingReservations = \App\Models\Reservation::getAll(
            'reservation_date > NOW() AND status = "pending"',
            [],
            'reservation_date ASC',
            10
        );

        $totalReservations = count(\App\Models\Reservation::getAll());
        $totalServices = count(\App\Models\Service::getAll());
        $totalUsers = count(\App\Models\User::getAll());
        $totalBarbers = count(\App\Models\User::getAll('permissions = ?', [User::ROLE_BARBER]));

        return $this->html([
            'user' => $userModel,
            'todayReservations' => $todayReservations,
            'upcomingReservations' => $upcomingReservations,
            'totalReservations' => $totalReservations,
            'totalServices' => $totalServices,
            'totalUsers' => $totalUsers,
            'totalBarbers' => $totalBarbers
        ]);
    }
    //zobraz rezervacie
    public function showReservations(Request $request): Response
    {
        $filter = $request->value('filter');
        $where = [];
        $params = [];

        // Filtre
        if ($filter === 'today') {
            $today = date('Y-m-d');
            $where[] = 'DATE(reservation_date) = ?';
            $params[] = $today;
        } elseif ($filter === 'upcoming') {
            $where[] = 'reservation_date > NOW()';
        }

        $whereClause = $where ? implode(' AND ', $where) : null;

        $reservations = \App\Models\Reservation::getAll(
            $whereClause,
            $params,
            'reservation_date DESC'
        );

        return $this->html([
            'reservations' => $reservations,
            'filter' => $filter
        ], 'reservations');
    }
}
