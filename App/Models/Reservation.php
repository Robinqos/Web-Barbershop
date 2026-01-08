<?php

namespace App\Models;

use Framework\Core\Model;

class Reservation extends Model
{
    protected int $id;
    protected ?int $user_id;  // NULL pre hosti
    protected int $service_id;
    protected string $reservation_date;
    protected string $created_at;
    protected string $status;  // 'pending', 'cancelled', 'completed'
    protected ?string $guest_name;
    protected ?string $guest_email;
    protected ?string $guest_phone;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getServiceId(): int
    {
        return $this->service_id;
    }

    public function setServiceId(int $service_id): void
    {
        $this->service_id = $service_id;
    }

    public function getReservationDate(): string
    {
        return $this->reservation_date;
    }

    public function setReservationDate(string $reservation_date): void
    {
        $this->reservation_date = $reservation_date;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getGuestName(): ?string
    {
        return $this->guest_name;
    }

    public function setGuestName(?string $guest_name): void
    {
        $this->guest_name = $guest_name;
    }

    public function getGuestEmail(): ?string
    {
        return $this->guest_email;
    }

    public function setGuestEmail(?string $guest_email): void
    {
        $this->guest_email = $guest_email;
    }

    public function getGuestPhone(): ?string
    {
        return $this->guest_phone;
    }

    public function setGuestPhone(?string $guest_phone): void
    {
        $this->guest_phone = $guest_phone;
    }

    // Pomocne


     //ci je od hosta
    public function isGuestReservation(): bool
    {
        return $this->user_id === null && $this->guest_name !== null;
    }


    //ci je od prihlaseneho
    public function isUserReservation(): bool
    {
        return $this->user_id !== null;
    }

    //zikanie mena
    public function getCustomerName(): string
    {
        if ($this->isUserReservation()) {
            try {
                $user = User::getOne($this->user_id);
            } catch (\Exception $e) {
                $user = null;
            }
            return $user ? $user->getFullname() : 'Neznámy používateľ';
        }

        return $this->guest_name ?? 'Anonymný hosť';
    }

    //ziskanie sluzby pre reservaciu
    public function getService(): ?Service
    {
        return Service::getOne($this->service_id);
    }

    //ziskanie pouzivatela pre rezervaciu
    public function getUser(): ?User
    {
        if ($this->user_id === null) {
            return null;
        }
        return User::getOne($this->user_id);
    }

    //format datumu
    public function getFormattedReservationDate(): string
    {
        $date = new \DateTime($this->reservation_date);
        return $date->format('d.m.Y H:i');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function cancel(): void
    {
        $this->status = self::STATUS_CANCELLED;
        $this->save();
    }
}