<?php

namespace App\Controllers;

use App\Models\Reservation;
use App\Models\Review;
use App\Models\Barber;
use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class ReviewController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        return $this->user->isLoggedIn();
    }

    public function index(Request $request): Response
    {
        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();
        if (!$identity instanceof User) {
            return $this->redirect($this->url("auth.login"));
        }
        $user = $identity;

        $reviews = Review::getAll(
            'user_id = ?',
            [$user->getId()],
            'created_at DESC'
        );

        return $this->html([
            'reviews' => $reviews
        ], 'index');
    }

    public function store(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url("auth.index"));
        }

        $reservationId = (int) $request->value('reservation_id');
        $rating = (int) $request->value('rating');

        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();
        if (!$identity instanceof User) {
            return $this->redirect($this->url("auth.login"));
        }
        $user = $identity;
        $reservation = Reservation::getOne($reservationId);

        $error = null;

        if (!$reservation) {
            $error = 'Rezervácia neexistuje';
        } elseif ($reservation->getUserId() !== $user->getId()) {
            $error = 'Táto rezervácia vám nepatrí';
        } elseif (!$reservation->isCompleted()) {
            $error = 'Môžete hodnotiť iba dokončené rezervácie';
        } elseif (!$reservation->getBarberId()) {
            $error = 'Rezervácia nemá priradeného barbera';
        } elseif ($rating < 1 || $rating > 5) {
            $error = 'Hodnotenie musí byť medzi 1 a 5 hviezdičkami';
        }

        if (!$error && Review::hasUserReviewedReservation($reservationId)) {
            $error = 'Túto rezerváciu ste už ohodnotili';
        }

        if ($error) {
            return $this->redirect($this->url("auth.index") . "?error=" . urlencode($error));
        }

        $review = new Review();
        $review->setUserId($user->getId());
        $review->setBarberId($reservation->getBarberId());
        $review->setReservationId($reservationId);
        $review->setRating($rating);

        date_default_timezone_set('Europe/Bratislava');
        $review->setCreatedAt(date('Y-m-d H:i:s'));
        $review->save();

        return $this->redirect($this->url("auth.index"));
    }

    public function forBarber(Request $request): Response
    {
        $barberId = (int) $request->value('barber_id');
        $barber = Barber::getOne($barberId);

        if (!$barber) {
            return $this->redirect($this->url("home.index"));
        }

        $reviews = Review::getAll(
            'barber_id = ?',
            [$barberId],
            'created_at DESC'
        );

        $averageRating = Review::getAverageRating($barberId);
        $reviewCount = Review::getCountByBarberId($barberId);

        return $this->html([
            'barber' => $barber,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount
        ], 'barber-reviews');
    }
}