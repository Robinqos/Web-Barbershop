<?php

namespace App\Controllers;

use App\Models\Gallery;
use App\Models\User;
use App\Models\Barber;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class GalleryController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->user->isLoggedIn()) {
            return false;
        }

        //ziskaj identitu
        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();

        // ci je user model
        if (!$identity instanceof User) {
            return false;
        }

        // Iba barber/admin
        return $identity->getPermissions() === \App\Models\User::ROLE_BARBER ||
            $identity->getPermissions() === \App\Models\User::ROLE_ADMIN;
    }

    public function index(Request $request): Response
    {
        return $this->redirect($this->url('home.index'));
    }

    public function store(Request $request): Response
    {
        if (!$request->isPost() || !$this->user->isLoggedIn()) {
            return $this->redirect($this->url('home.index'));
        }

        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();
        if (!$identity instanceof User) {
            return $this->redirect($this->url("auth.login"));
        }
        $user = $identity;

        if ($user->getPermissions() === User::ROLE_BARBER) {
            $barber = Barber::getByUserId($user->getId());
            if (!$barber) return $this->redirect($this->url('home.index'));
            $barberId = $barber->getId();
        }
        elseif ($user->getPermissions() === User::ROLE_ADMIN) {
            $barberId = (int) $request->value('barber_id');
            if (!$barberId) return $this->redirect($this->url('home.index'));
        } else {
            return $this->redirect($this->url('home.index'));
        }

        $photo = $request->file('photo');
        $services = trim($request->value('services', ''));

        if (!$photo || !$photo->isOk()) {
            return $this->redirect($this->url('home.index'));
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = $photo->getType();
        if (!in_array($mimeType, $allowedTypes)) {
            return $this->redirect($this->url('home.index'));
        }

        // max 5mb
        if ($photo->getSize() > 7 * 1024 * 1024) {
            return $this->redirect($this->url('home.index'));
        }

        $tmpPath = $photo->getFileTempPath();
        if (file_exists($tmpPath)) {
            $imageInfo = @getimagesize($tmpPath);

            if ($imageInfo === false) {
                return $this->redirect($this->url('home.index'));
            }

            list($width, $height) = $imageInfo;
            if ($width < 200 || $height < 200) {
                return $this->redirect($this->url('home.index'));
            }
        } else {
            return $this->redirect($this->url('home.index'));
        }

        // nazov fokty
        $originalName = $photo->getName();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = 'gallery_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($extension);

        $uploadDir = __DIR__ . '/../../public/uploads/gallery/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadPath = $uploadDir . $safeName;

        if ($photo->store($uploadPath)) {
            $photoPath = '/uploads/gallery/' . $safeName;
        } else {
            return $this->redirect($this->url('home.index'));
        }

        $galleryItem = new Gallery();
        $galleryItem->setBarberId($barberId);
        $galleryItem->setPhotoPath($photoPath);
        $galleryItem->setServices($services ?: null);
        $galleryItem->setCreatedAt(date('Y-m-d H:i:s'));
        $galleryItem->save();

        return $this->redirect($this->url('home.index'));
    }

    public function delete(Request $request): Response
    {
        if (!$this->user->isLoggedIn()) {
            return $this->redirect($this->url('home.index'));
        }

        $id = (int) $request->value('id');
        $galleryItem = Gallery::getOne($id);

        if (!$galleryItem) {
            return $this->redirect($this->url('home.index'));
        }

        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();
        if (!$identity instanceof User) {
            return $this->redirect($this->url("auth.login"));
        }
        $user = $identity;

        if ($user->getPermissions() === User::ROLE_BARBER) {
            $barber = Barber::getByUserId($user->getId());
            if (!$barber || $galleryItem->getBarberId() !== $barber->getId()) {
                return $this->redirect($this->url('home.index'));
            }
        } elseif ($user->getPermissions() !== User::ROLE_ADMIN) {
            return $this->redirect($this->url('home.index'));
        }

        $filePath = __DIR__ . '/../../public' . $galleryItem->getPhotoPath();
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $galleryItem->delete();

        return $this->redirect($this->url('home.index'));
    }
}