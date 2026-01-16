<?php

namespace App\Models;

use Framework\Core\Model;

class Barber extends Model
{
    protected int $id;
    protected int $user_id;
    protected ?string $bio;
    protected ?string $photo_url;
    protected int $is_active;
    protected string $created_at;

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

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photo_url;
    }

    public function setPhotoUrl(?string $photo_url): void
    {
        $this->photo_url = $photo_url;
    }

    public function getIsActive(): int
    {
        return $this->is_active;
    }

    public function setIsActive(int $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    // pomocne
    public function getUser(): ?User
    {
        return User::getOne($this->user_id);
    }
    public static function getOneByUserId(int $userId): ?self
    {
        return self::getOne('user_id = ?', [$userId]);
    }

    public function getName(): string
    {
        $user = $this->getUser();
        return $user ? $user->getFullname() : 'Neznámy barber';
    }

    public function getEmail(): string
    {
        $user = $this->getUser();
        return $user ? $user->getEmail() : '';
    }

    public function getPhone(): string
    {
        $user = $this->getUser();
        return $user ? $user->getPhone() : '';
    }

    public static function getByUserId(int $user_id): ?self
    {
        $barbers = self::getAll('user_id = ?', [$user_id]);
        return !empty($barbers) ? $barbers[0] : null;
    }
    public static function getActiveBarbers(): array
    {
        return static::getAll('is_active = ?', [1]);
    }
}