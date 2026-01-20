<?php

namespace App\Models;

use Framework\Core\Model;

class Gallery extends Model
{
    protected int $id;
    protected ?int $barber_id = null;
    protected string $photo_path;
    protected ?string $services = null;  // popis sluzieb
    protected string $created_at;
    protected bool $is_active = true;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getBarberId(): ?int
    {
        return $this->barber_id;
    }

    public function setBarberId(?int $barber_id): void
    {
        $this->barber_id = $barber_id;
    }

    public function getPhotoPath(): string
    {
        return $this->photo_path;
    }

    public function setPhotoPath(string $photo_path): void
    {
        $this->photo_path = $photo_path;
    }

    public function getServices(): ?string
    {
        return $this->services;
    }

    public function setServices(?string $services): void
    {
        $this->services = $services;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function getBarberName(): string
    {
        if ($this->barber_id === null) {
            return 'Bývalý barber';
        }

        $barber = Barber::getOne($this->barber_id);
        if ($barber) {
            return $barber->getName();
        }
        return 'Neznámy barber';
    }

    public static function getActiveItems(int $limit = 12): array
    {
        return self::getAll('is_active = 1', [], 'created_at DESC', $limit);
    }

    public static function getByBarberId(int $barberId): array
    {
        return self::getAll('barber_id = ? AND is_active = 1', [$barberId], 'created_at DESC');
    }
}