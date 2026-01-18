<?php

namespace App\Controllers;

use App\Models\Barber;
use App\Models\Reservation;
use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class BarberController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->user->isLoggedIn()) {
            return false;
        }

        $userModel = $this->app->getAuthenticator()->getUser();

        if (!$userModel) {
            return false;
        }

        return $userModel->getPermissions() === User::ROLE_BARBER;
    }

    public function editProfile(Request $request): Response
    {
        $user = $this->app->getAuthenticator()->getUser();
        $barber = Barber::getByUserId($user->getId());

        if (!$barber) {
            return $this->redirect($this->url("barber.index"));
        }

        return $this->html([
            'user' => $user,
            'barber' => $barber
        ], 'edit');
    }

    // spracovanie zmien profilu
    public function updateProfile(Request $request): Response
    {
        $user = $this->app->getAuthenticator()->getUser();
        $barber = Barber::getByUserId($user->getId());

        if (!$barber) {
            return $this->redirect($this->url("barber.index"));
        }

        if (!$request->isPost()) {
            return $this->redirect($this->url("barber.editProfile"));
        }

        $errors = [];

        $fullname = trim($request->value('fullname'));
        if (empty($fullname)) {
            $errors['fullname'] = 'Meno a priezvisko je povinné';
        } elseif (strlen(str_replace(' ', '', $fullname)) < 4) {
            $errors['fullname'] = 'Meno a priezvisko musí obsahovať aspoň 4 znaky (bez medzier)';
        }

        $email = trim($request->value('email'));
        if (empty($email)) {
            $errors['email'] = 'Email je povinný';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Neplatný formát emailu';
        } else {
            $existingUser = User::getOneByEmail($email);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                $errors['email'] = 'Email už je používaný iným používateľom';
            }
        }

        $phone = trim($request->value('phone'));
        if (!empty($phone)) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            $digitCount = strlen($cleanPhone);

            if ($digitCount < 9 || $digitCount > 15) {
                $errors['phone'] = 'Telefónne číslo musí obsahovať 9 až 15 číslic';
            }
        }

        $bio = trim($request->value('bio'));
        if (!empty($bio) && strlen($bio) > 500) {
            $errors['bio'] = 'Bio môže mať maximálne 500 znakov';
        }

        if (!empty($errors)) {
            return $this->html([
                'errors' => $errors,
                'user' => $user,
                'barber' => $barber
            ], 'edit');
        }

        $user->setFullName($fullname);
        $user->setEmail($email);
        $user->setPhone($phone ?: null);
        $user->save();

        $barber->setBio($bio ?: null);
        $barber->save();

        return $this->redirect($this->url("barber.index"));
    }

    public function uploadPhoto(Request $request): Response
    {
        $user = $this->app->getAuthenticator()->getUser();
        $barber = Barber::getByUserId($user->getId());

        if (!$barber) {
            return $this->redirect($this->url("barber.index"));
        }

        if (!$request->isPost()) {
            return $this->redirect($this->url("barber.index"));
        }

        $photoFile = $request->file('photo');

        if (!$photoFile || !$photoFile->isOk()) {
            return $this->redirect($this->url("barber.index"));
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = $photoFile->getType();

        if (!in_array($mimeType, $allowedTypes)) {
            return $this->redirect($this->url("barber.index"));
        }

        if ($photoFile->getSize() > 2 * 1024 * 1024) {
            return $this->redirect($this->url("barber.index"));
        }

        $tmpPath = $photoFile->getFileTempPath();
        if (file_exists($tmpPath)) {
            $imageInfo = @getimagesize($tmpPath);

            if ($imageInfo === false) {
                return $this->redirect($this->url("barber.index"));
            }

            list($width, $height) = $imageInfo;

            if ($width < 200 || $height < 200) {
                return $this->redirect($this->url("barber.index"));
            }
        }

        $originalName = $photoFile->getName();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = 'barber_' . $barber->getId() . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($extension);

        $uploadDir = __DIR__ . '/../../public/uploads/barbers/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $uploadPath = $uploadDir . $safeName;

        if ($photoFile->store($uploadPath)) {
            $photoPath = '/uploads/barbers/' . $safeName;

            $oldPhotoPath = $barber->getPhotoPath();
            if ($oldPhotoPath && file_exists(__DIR__ . '/../../public' . $oldPhotoPath)) {
                @unlink(__DIR__ . '/../../public' . $oldPhotoPath);
            }

            $barber->setPhotoPath($photoPath);
            $barber->save();

            // $_SESSION['flash_message'] = 'Fotka bola úspešne nahraná';
        } else {
            // $_SESSION['flash_error'] = 'Nepodarilo sa uložiť súbor';
        }

        return $this->redirect($this->url("barber.index"));
    }
    //todo:obmedzit velkost fotky
    public function index(Request $request): Response
    {
        $user = $this->app->getAuthenticator()->getUser();

        // barber z tohoto pouzivatela
        $barber = Barber::getByUserId($user->getId());

        if (!$barber) {
            return $this->html([
                'error' => 'Barber profil nebol nájdený',
                'user' => $user
            ]);
        }

        // vsetky tohoto barbera
        $today = date('Y-m-d');

        // dnes
        $todayReservations = Reservation::getAll(
            'barber_id = ? AND DATE(reservation_date) = ? AND status = "pending"',
            [$barber->getId(), $today],
            'reservation_date ASC'
        );

        // nadchadzajuec
        $upcomingReservations = Reservation::getAll(
            'barber_id = ? AND reservation_date > NOW() AND status = "pending"',
            [$barber->getId()],
            'reservation_date ASC',
            10
        );

        $allReservations = Reservation::getAll(
            'barber_id = ? AND status IN ("pending", "completed")',
            [$barber->getId()],
            'reservation_date DESC'
        );

        $totalReservations = count($allReservations);
        $todayReservationsCount = count($todayReservations);

        return $this->html([
            'user' => $user,
            'barber' => $barber,
            'todayReservations' => $todayReservations,
            'upcomingReservations' => $upcomingReservations,
            'allReservations' => $allReservations,
            'totalReservations' => $totalReservations,
            'todayReservationsCount' => $todayReservationsCount
        ], 'index');
    }

    public function cancelReservation(Request $request): Response
    {
        $id = (int) $request->value('id');
        $reservation = Reservation::getOne($id);

        $user = $this->app->getAuthenticator()->getUser();
        $barber = Barber::getByUserId($user->getId());

        if (!$reservation || !$barber) {
            return $this->redirect($this->url("barber.index"));
        }

        // ci mu patria
        if ($reservation->getBarberId() !== $barber->getId()) {
            return $this->redirect($this->url("barber.index"));
        }

        // iba cakajuce mozu by zrusene
        if (!$reservation->isPending()) {
            return $this->redirect($this->url("barber.index"));
        }

        $reservation->cancel();

        return $this->redirect($this->url("barber.index"));
    }

    public function completeReservation(Request $request): Response
    {
        $id = (int) $request->value('id');
        $reservation = Reservation::getOne($id);

        $user = $this->app->getAuthenticator()->getUser();
        $barber = Barber::getByUserId($user->getId());

        if (!$reservation || !$barber) {
            return $this->redirect($this->url("barber.index"));
        }

        // ci mu patria
        if ($reservation->getBarberId() !== $barber->getId()) {
            return $this->redirect($this->url("barber.index"));
        }

        // iba pending mozu by cancelled
        if (!$reservation->isPending()) {
            return $this->redirect($this->url("barber.index"));
        }

        $reservation->setStatus('completed');
        $reservation->save();

        return $this->redirect($this->url("barber.index"));
    }
    public function toggleActivation(Request $request): Response
    {
        $user = $this->app->getAuthenticator()->getUser();
        $barber = Barber::getByUserId($user->getId());

        if (!$barber) {
            return $this->redirect($this->url("barber.index"));
        }

        // ci nema ziadne nadchadzajuce rezervacie
        if ($barber->getIsActive()) {
            $upcomingReservations = Reservation::getAll(
                'barber_id = ? AND reservation_date > NOW() AND status = "pending"',
                [$barber->getId()]
            );

            if (!empty($upcomingReservations)) {
                //todo:potvrdenie ze zrusil
                return $this->redirect($this->url("barber.index"));
            }
        }

        // Prepneme stav
        $barber->setIsActive(!$barber->getIsActive());
        $barber->save();

        return $this->redirect($this->url("barber.index"));
    }
}