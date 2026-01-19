<?php

namespace App\Controllers;

use App\Models\Barber;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class HomeController
 * Handles actions related to the home page and other public actions.
 *
 * This controller includes actions that are accessible to all users, including a default landing page and a contact
 * page. It provides a mechanism for authorizing actions based on user permissions.
 *
 * @package App\Controllers
 */
class HomeController extends BaseController
{
    /**
     * Authorizes controller actions based on the specified action name.
     *
     * In this implementation, all actions are authorized unconditionally.
     *
     * @param string $action The action name to authorize.
     * @return bool Returns true, allowing all actions.
     */
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Displays the default home page.
     *
     * This action serves the main HTML view of the home page.
     *
     * @return Response The response object containing the rendered HTML for the home page.
     */
    public function index(Request $request): Response
    {
        // sluzby
        $services = Service::getAll();

        // aktivnych barberov
        $barbers = Barber::getAll('is_active = 1', [], 'created_at DESC');

        // barberi s doplnkovymi udajmi
        $barbersWithDetails = [];

        // vsetky recenzie
        $allReviews = Review::getAll();
        $barberRatings = [];
        $barberCounts = [];

        foreach ($allReviews as $review) {
            $barberId = $review->getBarberId();
            if (!isset($barberRatings[$barberId])) {
                $barberRatings[$barberId] = [];
                $barberCounts[$barberId] = 0;
            }
            $barberRatings[$barberId][] = $review->getRating();
            $barberCounts[$barberId]++;
        }

        // kazdeho barbera check
        foreach ($barbers as $barber) {
            $user = User::getOne($barber->getUserId());
            if (!$user) {
                continue;
            }

            // priemer
            $ratings = $barberRatings[$barber->getId()] ?? [];
            $averageRating = empty($ratings) ? 0 : array_sum($ratings) / count($ratings);
            $reviewCount = $barberCounts[$barber->getId()] ?? 0;

            // hviezdickz
            $fullStars = floor($averageRating);
            $halfStar = $averageRating - $fullStars >= 0.5;
            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

            $starRating =
                str_repeat('★', $fullStars) .
                ($halfStar ? '⯪' : '') .
                str_repeat('☆', $emptyStars);

            // bar s udajmi do pola
            $barbersWithDetails[] = [
                'barber' => $barber,
                'user' => $user,
                'averageRating' => $averageRating,
                'reviewCount' => $reviewCount,
                'starRating' => $starRating,
                'formattedRating' => number_format($averageRating, 1)
            ];
        }

        //poslat do pohladu
        return $this->html([
            'services' => $services,
            'barbers' => $barbersWithDetails
        ]);
    }

    /**
     * Displays the contact page.
     *
     * This action serves the HTML view for the contact page, which is accessible to all users without any
     * authorization.
     *
     * @return Response The response object containing the rendered HTML for the contact page.
     */
    public function contact(Request $request): Response
    {
        return $this->html();
    }
}
