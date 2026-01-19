<?php

namespace App\Models;

use Framework\Core\Model;

class Review extends Model
{
    protected int $id;
    protected int $user_id;
    protected int $barber_id;
    protected ?int $reservation_id;
    protected int $rating; // 1-5 hviezd
    protected string $created_at;

    public static function getAverageRating(int $barberId): float
    {
        $reviews = self::getAll('barber_id = ?', [$barberId]);

        if (empty($reviews)) {
            return 0.0;
        }

        $total = 0;
        foreach ($reviews as $review) {
            $total += $review->getRating();
        }

        return round($total / count($reviews), 1);
    }

    public static function getCountByBarberId(int $barberId): int
    {
        return self::getCount('barber_id = ?', [$barberId]);
    }

    public static function hasUserReviewedReservation(int $reservationId): bool
    {
        return self::getOne('reservation_id = ?', [$reservationId]) !== null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getBarberId(): int
    {
        return $this->barber_id;
    }

    public function setBarberId(int $barber_id): void
    {
        $this->barber_id = $barber_id;
    }

    public function getReservationId(): ?int
    {
        return $this->reservation_id;
    }

    public function setReservationId(?int $reservation_id): void
    {
        $this->reservation_id = $reservation_id;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }
        $this->rating = $rating;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUser(): ?User
    {
        return User::getOne($this->user_id);
    }

    public function getBarber(): ?Barber
    {
        return Barber::getOne($this->barber_id);
    }

    public function getReservation(): ?Reservation
    {
        if ($this->reservation_id === null) {
            return null;
        }
        return Reservation::getOne($this->reservation_id);
    }

    public function getFormattedCreatedAt(): string
    {
        $date = new \DateTime($this->created_at);
        return $date->format('d.m.Y H:i');
    }

    public function getStarRating(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public static function getReviewForReservation(int $reservationId): ?self
    {
        return self::getOne('reservation_id = ?', [$reservationId]);
    }

    public static function getReviewByUserAndBarber(int $userId, int $barberId): ?self
    {
        return self::getOne('user_id = ? AND barber_id = ? AND reservation_id IS NULL', [$userId, $barberId]);
    }

    public static function getReviewForReservationOrUserBarber(int $reservationId, int $userId, int $barberId): ?self
    {
        $review = self::getOne('reservation_id = ?', [$reservationId]);

        if (!$review) {
            $review = self::getOne('user_id = ? AND barber_id = ? AND reservation_id IS NULL', [$userId, $barberId]);
        }
        return $review;
    }


}