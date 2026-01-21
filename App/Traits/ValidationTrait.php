<?php

namespace App\Traits;

use App\Models\User;

trait ValidationTrait
{
    private function validateFullName(?string $full_name, bool $required = false, bool $isForAdmin = false): ?string
    {
        if ($required && (empty($full_name) || trim($full_name) === '')) {
            return $isForAdmin ? "Meno a priezvisko je povinné" : "Neplatné údaje";
        }

        if (!empty($full_name) && trim($full_name) !== '') {
            $trimmed = trim($full_name);
            $trimmed = preg_replace('/\s+/', ' ', $trimmed);

            if (strlen(str_replace(' ', '', $trimmed)) < 4) {
                return $isForAdmin ? "Meno a priezvisko musí obsahovať aspoň 4 znaky (bez medzier)" : "Neplatné údaje";
            }
        }

        return null;
    }

    private function validatePhone(?string $phone, bool $required = true, bool $isForAdmin = false): ?string
    {
        if ($required && empty($phone)) {
            return $isForAdmin ? "Telefónne číslo je povinné" : "Neplatné údaje";
        }

        if (!empty($phone)) {
            $phone = trim($phone);
            $clean_phone = preg_replace('/[^0-9]/', '', $phone);
            $digit_count = strlen($clean_phone);

            if ($digit_count < 9 || $digit_count > 15) {
                return $isForAdmin ? "Telefónne číslo musí obsahovať 9 až 15 číslic" : "Neplatné údaje";
            }

            if (!preg_match('/^[\d\s\-+()]+$/', $phone)) {
                return $isForAdmin ? "Telefónne číslo obsahuje nepovolené znaky" : "Neplatné údaje";
            }
        }

        return null;
    }

    private function validateEmail(?string $email, bool $required = true, bool $checkUnique = true, int $excludeUserId = 0, bool $isForAdmin = false): ?string
    {
        if ($required && empty($email)) {
            return $isForAdmin ? "Email je povinný" : "Neplatné údaje";
        }

        if (!empty($email)) {
            $email = trim($email);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $isForAdmin ? "Neplatný formát emailu" : "Neplatné údaje";
            } elseif ($checkUnique) {
                $existingUser = User::getOneByEmail($email);
                if ($existingUser && $existingUser->getId() !== $excludeUserId) {
                    return $isForAdmin ? "Email už je používaný iným používateľom" : "Neplatné údaje";
                }
            }
        }

        return null;
    }

    private function validatePassword(?string $password, bool $required = true, bool $isForAdmin = false): ?string
    {
        if ($required && empty($password)) {
            return $isForAdmin ? "Heslo je povinné" : "Neplatné údaje";
        }

        if (!empty($password)) {
            if (strlen($password) < 8) {
                return $isForAdmin ? "Heslo musí mať aspoň 8 znakov" : "Neplatné údaje";
            }

            if (!preg_match('/[A-Z]/', $password)) {
                return $isForAdmin ? "Heslo musí obsahovať aspoň jedno veľké písmeno" : "Neplatné údaje";
            }

            if (!preg_match('/[0-9]/', $password)) {
                return $isForAdmin ? "Heslo musí obsahovať aspoň jednu číslicu" : "Neplatné údaje";
            }
        }

        return null;
    }

    private function validatePasswordConfirm(?string $password, ?string $password_confirm,
                                             bool $required = true, bool $isForAdmin = false): ?string
    {
        if ($required && empty($password_confirm)) {
            return $isForAdmin ? "Potvrdenie hesla je povinné" : "Neplatné údaje";
        }

        if (!empty($password_confirm) && $password !== $password_confirm) {
            return $isForAdmin ? "Heslá sa nezhodujú" : "Neplatné údaje";
        }

        return null;
    }

    private function validateTerms(?string $terms, bool $required = true): ?string
    {
        if ($required && (!isset($terms) || $terms !== 'on')) {
            return "Musíte súhlasiť so spracovaním osobných údajov";
        }

        return null;
    }

    private function getUserBadgeClass($permissions): string
    {
        $badges = [
            User::ROLE_CUSTOMER => 'info',
            User::ROLE_BARBER => 'primary',
            User::ROLE_ADMIN => 'warning'
        ];

        return $badges[$permissions] ?? 'secondary';
    }
}