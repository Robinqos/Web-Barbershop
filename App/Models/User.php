<?php

namespace App\Models;

use Cassandra\Date;
use Framework\Core\IIdentity;
use Framework\Core\Model;
use MongoDB\BSON\Timestamp;

class User extends Model implements IIdentity
{
    protected int $id;
    protected ?string $fullname;
    protected string $email;
    protected string $password;
    protected string $phone;
    protected int $permissions;
    protected ?string $created_at;
    protected ?string $last_login;
    public const ROLE_ADMIN = 2;
    public const ROLE_BARBER = 1;
    public const ROLE_CUSTOMER = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(?string $fullname): void
    {
        $this->fullname = $fullname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPermissions(): int
    {
        return $this->permissions;
    }

    public function setPermissions(int $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $date): void
    {
        $this->created_at = $date;
    }

    public function getLastLogin(): ?string
    {
        return $this->last_login;
    }

    public function setLastLogin($last_login): void
    {
        $this->last_login = $last_login;
    }

    public static function getOneByEmail(string $email): ?User
    {
        $users = self::getAll('email = ?', [$email]);
        return !empty($users) ? $users[0] : null;
    }

}
