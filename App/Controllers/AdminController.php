<?php

namespace App\Controllers;

use App\Models\Reservation;
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
            $where[] = 'status = "pending"';  // Len pending
            $params[] = $today;
        } elseif ($filter === 'upcoming') {
            $where[] = 'reservation_date > NOW()';
            $where[] = 'status = "pending"';
        }
        // ak nieje filter, ukaze vsetky aj zrusene

        $whereClause = $where ? implode(' AND ', $where) : null;

        $reservations = \App\Models\Reservation::getAll(
            $whereClause,
            $params,
            'reservation_date ASC'
        );

        $services = \App\Models\Service::getAll(null, [], 'title ASC');

        return $this->html([
            'reservations' => $reservations,
            'filter' => $filter,
            'services' => $services
        ], 'reservations');
    }
    /**
     * AJAX endpoint na aktualizáciu rezervácie
     */
    public function updateReservationAjax(Request $request): Response
    {
        if (!$request->isAjax()) {
            return $this->json(['success' => false, 'message' => 'Iba AJAX požiadavky']);
        }

        //json z Request triedy
        try {
            $data = $request->json();  //toto vrati uz decode json
        } catch (\JsonException $e) {
            return $this->json(['success' => false, 'message' => 'Neplatný JSON vstup']);
        }

        $id = isset($data->id) ? (int)$data->id : 0;
        $field = isset($data->field) ? $data->field : '';
        $value = isset($data->value) ? $data->value : '';

        $reservation = Reservation::getOne($id);

        if (!$reservation) {
            return $this->json(['success' => false, 'message' => 'Rezervácia neexistuje']);
        }

        $errors = [];

        switch ($field) {
            case 'guest_email':
                $reservation->setGuestEmail($value);
                break;

            case 'guest_phone':
                $reservation->setGuestPhone($value);
                break;

            case 'guest_name':  // PRIDAŤ!
                $reservation->setGuestName($value);
                break;

            case 'service_id':  // PRIDAŤ!
                $serviceId = (int)$value;
                if ($serviceId <= 0) {
                    $errors[] = 'Neplatná služba';
                } else {
                    $reservation->setServiceId($serviceId);
                }
                break;

            case 'reservation_date':
                if (empty($value)) {
                    $errors[] = 'Dátum je povinný';
                } else {
                    if (\DateTime::createFromFormat('Y-m-d\TH:i', $value) === false) {
                        $errors[] = 'Neplatný formát dátumu';
                    } else {
                        $reservation->setReservationDate($value);
                    }
                }
                break;

            case 'status':
                $allowedStatuses = ['pending', 'completed', 'cancelled'];
                if (!in_array($value, $allowedStatuses)) {
                    $errors[] = 'Neplatný status';
                } else {
                    $reservation->setStatus($value);
                }
                break;

            case 'note':
                $reservation->setNote($value);
                break;

            default:
                return $this->json(['success' => false, 'message' => 'Neplatné pole: ' . $field]);
        }

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors]);
        }

        $reservation->save();

        // vrat aktualizovane
        return $this->json([
            'success' => true,
            'message' => 'Rezervácia aktualizovaná',
            'reservation' => [
                'id' => $reservation->getId(),
                'customer_name' => $reservation->getCustomerName(),
                'formatted_date' => $reservation->getFormattedReservationDate(),
                'status' => $reservation->getStatus(),
                'status_badge' => $this->getStatusBadge($reservation->getStatus()),
                'service_title' => $reservation->getService() ? $reservation->getService()->getTitle() : 'N/A'
            ]
        ]);
    }

    /**
     * Rychla akcia
     */
    public function completeReservation(Request $request): Response
    {
        $id = (int) $request->value('id');
        $reservation = Reservation::getOne($id);

        if ($reservation && $reservation->isPending()) {
            $reservation->setStatus('completed');
            $reservation->save();
        }

        return $this->redirect($this->url('admin.showReservations'));
    }

    /**
     * Rychla akcia
     */
    public function cancelReservation(Request $request): Response
    {
        $id = (int) $request->value('id');
        $reservation = Reservation::getOne($id);

        if ($reservation && $reservation->isPending()) {
            $reservation->setStatus('cancelled');
            $reservation->save();
        }

        return $this->redirect($this->url('admin.showReservations'));
    }

    //helper metoda
    private function getStatusBadge(string $status): string
    {
        $badges = [
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];

        return $badges[$status] ?? 'secondary';
    }
}
