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
    protected string $created_at;
    protected  $last_login;

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
        $this->password = $password;
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

    /**
     * @return mixed
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $date): void
    {
        $this->created_at = $date;
    }

    /**
     * @param mixed $created_at
     */

    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @param mixed $last_login
     */
    public function setLastLogin($last_login): void
    {
        $this->last_login = $last_login;
    }


}