<?php

namespace App\Controllers;

use App\Models\Barber;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\User;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class AdminController
 *
 * This controller manages admin-related actions within the application.It extends the base controller functionality
 * provided by BaseController.
 *
 * @package App\Controllers
 */
class AdminController extends BaseController
{
    //todo:mozno do traitu dat spolocne funkcie
    /**
     * Authorizes actions in this controller.
     *
     * This method checks if the user is logged in, allowing or denying access to specific actions based
     * on the authentication state.
     *
     * @param string $action The name of the action to authorize.
     * @return bool Returns true if the user is logged in; false otherwise.
     */
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->user->isLoggedIn()) {
            return false;
        }

        $userModel = $this->app->getAuthenticator()->getUser();

        if (!$userModel) {
            return false;
        }
        //admin = 2
        return $userModel->getPermissions() >= User::ROLE_ADMIN;
    }

    /**
     * Displays the index page of the admin panel.
     *
     * This action requires authorization. It returns an HTML response for the admin dashboard or main page.
     *
     * @return Response Returns a response object containing the rendered HTML.
     */
    public function index(Request $request): Response
    {
        $userModel = $this->app->getAuthenticator()->getUser();

        $today = date('Y-m-d');

        $todayReservations = \App\Models\Reservation::getAll(
            'DATE(reservation_date) = ? AND status = "pending"',
            [$today],
            'reservation_date ASC'
        );

        $upcomingReservations = \App\Models\Reservation::getAll(
            'reservation_date > NOW() AND status = "pending"',
            [],
            'reservation_date ASC',
            10
        );

        $totalReservations = count(\App\Models\Reservation::getAll());
        $totalServices = count(\App\Models\Service::getAll());
        $totalUsers = count(\App\Models\User::getAll());
        $totalBarbers = count(\App\Models\User::getAll('permissions = ?', [User::ROLE_BARBER]));

        return $this->html([
            'user' => $userModel,
            'todayReservations' => $todayReservations,
            'upcomingReservations' => $upcomingReservations,
            'totalReservations' => $totalReservations,
            'totalServices' => $totalServices,
            'totalUsers' => $totalUsers,
            'totalBarbers' => $totalBarbers
        ]);
    }
    /**
     * Univerzalny AJAX endpoint
     */
    public function updateAjax(Request $request): Response
    {
        if (!$request->isAjax()) {
            return $this->json(['success' => false, 'message' => 'Iba AJAX požiadavky']);
        }

        try {
            $data = $request->json();
        } catch (\JsonException $e) {
            return $this->json(['success' => false, 'message' => 'Neplatný JSON vstup']);
        }

        $id = isset($data->id) ? (int)$data->id : 0;
        $field = isset($data->field) ? $data->field : '';
        $value = isset($data->value) ? $data->value : '';
        $entity = isset($data->entity) ? $data->entity : 'reservation';

        switch ($entity) {
            case 'reservation':
                return $this->updateReservation($id, $field, $value);

            case 'user':
                return $this->updateUser($id, $field, $value);

            case 'service':
                return $this->updateService($id, $field, $value);
            case 'barber':
                return $this->updateBarber($id, $field, $value);

            default:
                return $this->json(['success' => false, 'message' => 'Neplatná entita: ' . $entity]);
        }
    }
    //zobraz rezervacie
    public function showReservations(Request $request): Response
    {
        $filter = $request->value('filter');
        $where = [];
        $params = [];

        // Filtre
        if ($filter === 'today') {
            $today = date('Y-m-d');
            $where[] = 'DATE(reservation_date) = ?';
            $where[] = 'status = "pending"';  // Len pending
            $params[] = $today;
        } elseif ($filter === 'upcoming') {
            $where[] = 'reservation_date > NOW()';
            $where[] = 'status = "pending"';
        }
        // ak nieje filter, ukaze vsetky aj zrusene

        $whereClause = $where ? implode(' AND ', $where) : null;

        $reservations = \App\Models\Reservation::getAll(
            $whereClause,
            $params,
            'reservation_date ASC'
        );

        $services = Service::getAll(null, [], 'title ASC');
        $barbers = User::getAll('permissions = ?', [User::ROLE_BARBER], 'fullname ASC');

        return $this->html([
            'reservations' => $reservations,
            'filter' => $filter,
            'services' => $services,
            'barbers' => $barbers
        ], 'reservations');
    }
    /**
     * AJAX endpoint na aktualizáciu rezervácie
     */
    public function updateReservation($id, $field, $value): Response
    {
        $reservation = Reservation::getOne($id);

        if (!$reservation) {
            return $this->json(['success' => false, 'message' => 'Rezervácia neexistuje']);
        }

        $errors = [];

        switch ($field) {
            case 'guest_email':
                if (empty($value) || trim($value) === '') {
                    $errors[] = 'Email hosťa je povinný';
                } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Neplatný formát emailu hosťa';
                } else {
                    $reservation->setGuestEmail(trim($value));
                }
                break;
            case 'barber_id':
                $barberId = (int)$value;

                if ($barberId === 0) {
                    $reservation->setBarberId(null);
                    $reservation->save();
                    return $this->json([
                        'success' => true,
                        'message' => 'Rezervácia aktualizovaná',
                        'value' => '<span class="text-muted">Nepriradený</span>',
                        'dataValue' => '0'
                    ]);
                } else {
                    $barberModel = Barber::getOne($barberId);

                    if (!$barberModel) {
                        $errors[] = 'Barber neexistuje v systéme';
                    } elseif (!$barberModel->getIsActive()) {
                        $errors[] = 'Tento barber je neaktívny a nemôže prijímať rezervácie';
                    } else {
                        $reservation->setBarberId($barberId);
                        $reservation->save();

                        return $this->json([
                            'success' => true,
                            'message' => 'Rezervácia aktualizovaná',
                            'value' => $barberModel->getName(),
                            'barberId' => $barberId
                        ]);
                    }
                }
                break;
            case 'guest_phone':
                $validationError = $this->validatePhone($value, true);
                if ($validationError) {
                    $errors[] = str_replace('Telefónne číslo', 'Telefónne číslo hosťa', $validationError);
                } else {
                    $reservation->setGuestPhone(trim($value));
                }
                break;

            case 'guest_name':
                $validationError = $this->validateFullName($value, true);
                if ($validationError) {
                    $errors[] = str_replace('Meno a priezvisko', 'Meno hosťa', $validationError);
                } else {
                    $reservation->setGuestName(trim($value));
                }
                break;

            case 'service_id':
                $serviceId = (int)$value;
                if ($serviceId <= 0) {
                    $errors[] = 'Neplatná služba';
                } else {
                    // ci existuje
                    $service = \App\Models\Service::getOne($serviceId);
                    if (!$service) {
                        $errors[] = 'Služba s ID ' . $serviceId . ' neexistuje';
                    } else {
                        $reservation->setServiceId($serviceId);
                        $reservation->save();

                        return $this->json([
                            'success' => true,
                            'message' => 'Rezervácia aktualizovaná',
                            'value' => $service->getTitle(),
                            'serviceId' => $serviceId
                        ]);
                    }
                }
                break;

            case 'reservation_date':
                if (empty($value)) {
                    $errors[] = 'Dátum a čas je povinný';
                } else {
                    if (\DateTime::createFromFormat('Y-m-d\TH:i', $value) === false) {
                        $errors[] = 'Neplatný formát dátumu a času. Použite formát: RRRR-MM-DD HH:MM';
                    } else {
                        // je v minulosti?
                        $dateTime = new \DateTime($value);
                        $now = new \DateTime();
                        if ($dateTime < $now) {
                            $errors[] = 'Dátum a čas nemôže byť v minulosti';
                        } else {
                            $reservation->setReservationDate($value);
                            $reservation->save();

                            return $this->json([
                                'success' => true,
                                'message' => 'Rezervácia aktualizovaná',
                                'value' => $reservation->getFormattedReservationDate()
                            ]);
                        }
                    }
                }
                break;

            case 'status':
                $allowedStatuses = ['pending', 'completed', 'cancelled'];
                if (!in_array($value, $allowedStatuses)) {
                    $errors[] = 'Neplatný status. Povolené hodnoty: pending, completed, cancelled';
                } else {
                    $reservation->setStatus($value);
                }
                break;

            case 'note':
                // nie je poviina ale max dlzka
                $trimmedValue = trim($value);
                if (strlen($trimmedValue) > 70) {
                    $errors[] = 'Poznámka môže mať maximálne 70 znakov';
                } else {
                    $reservation->setNote($trimmedValue ?: null);
                }
                break;

            default:
                return $this->json(['success' => false, 'message' => 'Neplatné pole: ' . $field]);
        }

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors]);
        }

        $reservation->save();

        // vrat aktualizovane
        return $this->json([
            'success' => true,
            'message' => 'Rezervácia aktualizovaná',
            'badgeClass' => $this->getStatusBadge($reservation->getStatus())
        ]);
    }

    //helper metoda
    private function getStatusBadge(string $status): string
    {
        $badges = [
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];

        return $badges[$status] ?? 'secondary';
    }

    /**
     * Vymazanie rezervacie (len ak je cancelled)
     */
    public function deleteReservation(Request $request): Response
    {
        $id = (int) $request->value('id');
        $reservation = Reservation::getOne($id);

        if (!$reservation) {
            return $this->redirect($this->url('admin.showReservations'));
        }

        if ($reservation->getStatus() === 'cancelled') {
            $reservation->delete();
        }

        return $this->redirect($this->url('admin.showReservations'));
    }

    ////////////////////////////////SERVICES/////////////////////////////////
    public function services(Request $request): Response
    {
        $services = Service::getAll(null, [], 'title ASC');
        return $this->html(['services' => $services], 'services');
    }
    private function updateService($id, $field, $value)
    {
        $service = Service::getOne($id);
        if (!$service) {
            return $this->json(['success' => false, 'message' => 'Služba neexistuje']);
        }

        $errors = [];

        switch ($field) {
            case 'title':
                if (empty($value) || trim($value) === '') {
                    $errors[] = 'Názov služby je povinný';
                } else {
                    $trimmedValue = trim($value);
                    if (strlen($trimmedValue) < 2) {
                        $errors[] = 'Názov služby musí mať aspoň 2 znaky';
                    } elseif (strlen($trimmedValue) > 100) {
                        $errors[] = 'Názov služby môže mať maximálne 100 znakov';
                    } else {
                        $service->setTitle($trimmedValue);
                    }
                }
                break;

            case 'description':
                $trimmedValue = trim($value);
                if (strlen($trimmedValue) > 500) {
                    $errors[] = 'Popis služby môže mať maximálne 500 znakov';
                } else {
                    $service->setDescription($trimmedValue ?: null);
                }
                break;

            case 'price':
                $price = (int)$value;
                if ($price <= 0) {
                    $errors[] = 'Cena musí byť kladné číslo';
                } elseif ($price > 10000) {
                    $errors[] = 'Cena môže byť maximálne 10 000 €';
                } else {
                    $service->setPrice($price);
                }
                break;

            case 'duration':
                $duration = (int)$value;
                if ($duration <= 0) {
                    $errors[] = 'Trvanie musí byť kladné číslo';
                } elseif ($duration > 480) {
                    $errors[] = 'Trvanie môže byť maximálne 480 minút (8 hodín)';
                } else {
                    $service->setDuration($duration);
                }
                break;

            default:
                return $this->json(['success' => false, 'message' => 'Neplatné pole: ' . $field]);
        }

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors]);
        }

        $service->save();

        return $this->json([
            'success' => true,
            'message' => 'Služba aktualizovaná'
        ]);
    }

    public function createService(Request $request): Response
    {
        if ($request->isPost()) {
            $errors = [];

            // validacie
            $title = $request->value('title');
            if (empty($title) || trim($title) === '') {
                $errors['title'] = 'Názov služby je povinný';
            } else {
                $trimmedTitle = trim($title);
                if (strlen($trimmedTitle) < 2) {
                    $errors['title'] = 'Názov služby musí mať aspoň 2 znaky';
                } elseif (strlen($trimmedTitle) > 100) {
                    $errors['title'] = 'Názov služby môže mať maximálne 100 znakov';
                }
            }

            $description = $request->value('description');
            if (!empty($description)) {
                $trimmedDescription = trim($description);
                if (strlen($trimmedDescription) > 500) {
                    $errors['description'] = 'Popis služby môže mať maximálne 500 znakov';
                }
            }

            $price = $request->value('price');
            if (empty($price) || trim($price) === '') {
                $errors['price'] = 'Cena je povinná';
            } else {
                $priceValue = (int)$price;
                if ($priceValue <= 0) {
                    $errors['price'] = 'Cena musí byť kladné číslo';
                } elseif ($priceValue > 10000) {
                    $errors['price'] = 'Cena môže byť maximálne 10 000 €';
                }
            }

            $duration = $request->value('duration');
            if (empty($duration) || trim($duration) === '') {
                $errors['duration'] = 'Trvanie je povinné';
            } else {
                $durationValue = (int)$duration;
                if ($durationValue <= 0) {
                    $errors['duration'] = 'Trvanie musí byť kladné číslo';
                } elseif ($durationValue > 480) {
                    $errors['duration'] = 'Trvanie môže byť maximálne 480 minút (8 hodín)';
                }
            }

            // ak chyby, vrat povodny
            if (!empty($errors)) {
                return $this->html([
                    'errors' => $errors,
                    'formData' => [
                        'title' => $title,
                        'description' => $description,
                        'price' => $price,
                        'duration' => $duration
                    ]
                ], 'service-create');
            }

            $service = new Service();
            $service->setTitle(trim($title));
            $service->setDescription(!empty($description) ? trim($description) : null);
            $service->setPrice((int)$price);
            $service->setDuration((int)$duration);
            $service->save();

            return $this->redirect($this->url('admin.services'));
        }

        return $this->html([], 'service-create');
    }

    public function deleteService(Request $request): Response
    {
        $id = (int) $request->value('id');
        $service = Service::getOne($id);

        if ($service) {
            //su aktivne nejake?
            $activeReservations = Reservation::getCount(
                'service_id = ? AND status = "pending"',
                [$id]
            );

            if ($activeReservations > 0) {
                //todo:mozno flash message
                return $this->redirect($this->url('admin.services'));
            }

            $service->delete();
        }

        return $this->redirect($this->url('admin.services'));
    }

    ////////////////////////////////////////USERS///////////////////////////////////////////////////

    public function users(Request $request): Response
    {
        $users = User::getAll(null, [], 'created_at DESC');
        return $this->html(['users' => $users], 'users');
    }
    public function createUser(Request $request): Response
    {
        if ($request->isPost()) {
            $errors = [];

            // Validacie
            if ($error = $this->validateFullName($request->value('name'), true)) {
                $errors['name'] = $error;
            }

            if ($error = $this->validateEmailForAdmin($request->value('email'), 0, true)) {
                $errors['email'] = $error;
            }

            if ($error = $this->validatePhone($request->value('phone'), true)) {
                $errors['phone'] = $error;
            }

            if ($error = $this->validatePassword($request->value('password'), true)) {
                $errors['password'] = $error;
            }

            $permissions = (int)$request->value('permissions');
            $allowedPermissions = [User::ROLE_CUSTOMER, User::ROLE_BARBER, User::ROLE_ADMIN];
            if (!in_array($permissions, $allowedPermissions)) {
                $errors['permissions'] = 'Neplatná rola';
            }

            if (!empty($errors)) {
                return $this->html(['errors' => $errors], 'user-create');
            }

            $user = new User();
            $user->setFullName($request->value('name'));
            $user->setEmail($request->value('email'));
            $user->setPhone($request->value('phone'));
            $user->setPassword($request->value('password'));
            $user->setPermissions($permissions);
            $user->setCreatedAt(date('Y-m-d H:i:s'));
            $user->save();

            return $this->redirect($this->url('admin.users'));
        }

        return $this->html([], 'user-create');
    }

    public function deleteUser(Request $request): Response
    {
        $id = (int) $request->value('id');
        $user = User::getOne($id);

        // nevymaz sameho seba
        $currentUser = $this->app->getAuthenticator()->getUser();
        if ($currentUser && $currentUser->getId() === $id) {
            return $this->redirect($this->url('admin.users'));
        }

        if ($user) {
            // ma rezervacie?
            $activeReservations = Reservation::getCount(
                'user_id = ? AND status = "pending"',
                [$id]
            );

            if ($activeReservations > 0) {
                //todo:mozno flash message
                return $this->redirect($this->url('admin.users'));
            }

            $user->delete();
        }

        return $this->redirect($this->url('admin.users'));
    }

    private function updateUser($id, $field, $value)
    {
        $user = User::getOne($id);
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Používateľ neexistuje']);
        }

        $errors = [];
        $displayValue = $value;

        switch ($field) {
            case 'name':
                $validationError = $this->validateFullName($value, true);
                if ($validationError) {
                    $errors[] = $validationError;
                } else {
                    $user->setFullName(trim($value));
                    $displayValue = trim($value);
                }
                break;

            case 'email':
                $validationError = $this->validateEmailForAdmin($value, $id, true);
                if ($validationError) {
                    $errors[] = $validationError;
                } else {
                    $user->setEmail(trim($value));
                    $displayValue = trim($value);
                }
                break;

            case 'phone':
                $validationError = $this->validatePhone($value, true);
                if ($validationError) {
                    $errors[] = $validationError;
                } else {
                    $user->setPhone(trim($value));
                    $displayValue = trim($value);
                }
                break;

            case 'permissions':
                $allowedPermissions = [User::ROLE_CUSTOMER, User::ROLE_BARBER, User::ROLE_ADMIN];
                if (!in_array((int)$value, $allowedPermissions)) {
                    $errors[] = 'Neplatná rola. Povolené hodnoty: 0 (Zákazník), 1 (Barber), 2 (Admin)';
                } else {
                    $user->setPermissions((int)$value);
                    $roleMap = [
                        User::ROLE_CUSTOMER => 'Zákazník',
                        User::ROLE_BARBER => 'Barber',
                        User::ROLE_ADMIN => 'Admin'
                    ];
                    $displayValue = $roleMap[(int)$value] ?? 'Neznáma';
                    $badgeClass = $this->getUserBadgeClass((int)$value);
                }
                break;

            default:
                return $this->json(['success' => false, 'message' => 'Neplatné pole: ' . $field]);
        }

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors]);
        }

        $user->save();

        $response = [
            'success' => true,
            'message' => 'Používateľ aktualizovaný',
            'value' => $displayValue
        ];

        if ($field === 'permissions') {
            $response['badgeClass'] = $badgeClass ?? $this->getUserBadgeClass($user->getPermissions());
        }

        return $this->json($response);
    }
    /////////////////////////////////////////////////BARBERS////////////////////////////////////////////////////

    public function barbers(Request $request): Response
    {
        $barbers = Barber::getAll(null, [], 'created_at DESC');
        return $this->html(['barbers' => $barbers], 'barbers');
    }

    public function createBarber(Request $request): Response
    {
        if ($request->isPost()) {
            $errors = [];

            // Validácie textových polí (tvoje pôvodné)
            if ($error = $this->validateFullName($request->value('name'), true)) {
                $errors['name'] = $error;
            }

            if ($error = $this->validateEmailForAdmin($request->value('email'), 0, true)) {
                $errors['email'] = $error;
            }

            if ($error = $this->validatePhone($request->value('phone'), true)) {
                $errors['phone'] = $error;
            }

            if ($error = $this->validatePassword($request->value('password'), true)) {
                $errors['password'] = $error;
            }

            $bio = trim($request->value('bio'));
            if (empty($bio)) {
                $errors['bio'] = 'Bio je povinné';
            } elseif (strlen($bio) < 10) {
                $errors['bio'] = 'Bio musí mať aspoň 10 znakov';
            } elseif (strlen($bio) > 500) {
                $errors['bio'] = 'Bio môže mať maximálne 500 znakov';
            }

            $photoFile = $request->file('photo');
            $photoPath = null;

            if (!$photoFile || !$photoFile->isOk()) {
                $errors['photo'] = 'Fotka je povinná';
            } else {
                // kontrola typu fokty
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $mimeType = $photoFile->getType();

                if (!in_array($mimeType, $allowedTypes)) {
                    $errors['photo'] = 'Nepodporovaný formát obrázka. Povolené: JPEG, PNG, GIF, WebP.';
                }

                // max 2mb
                if ($photoFile->getSize() > 2 * 1024 * 1024) {
                    $errors['photo'] = 'Obrázok je príliš veľký. Maximálna veľkosť: 2MB.';
                }

                if (empty($errors['photo'])) {
                    $tmpPath = $photoFile->getFileTempPath();
                    if (file_exists($tmpPath)) {
                        list($width, $height) = getimagesize($tmpPath);



                        if ($width < 200 || $height < 200) {
                            $errors['photo'] = 'Obrázok má príliš nízke rozlíšenie. Minimálne: 200x200px.';
                        }
                    }
                }

                // vytvori unique nazov
                if (empty($errors['photo'])) {
                    $originalName = $photoFile->getName();
                    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                    $safeName = 'barber_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($extension);

                    // adresar
                    $uploadDir = __DIR__ . '/../../public/uploads/barbers/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0775, true);
                    }

                    $uploadPath = $uploadDir . $safeName;

                    // ulozenie
                    if ($photoFile->store($uploadPath)) {
                        $photoPath = '/uploads/barbers/' . $safeName;
                    } else {
                        $errors['photo'] = 'Nepodarilo sa uložiť súbor. Skontrolujte práva adresára.';
                    }
                }
            }

            if (!empty($errors)) {
                return $this->html(['errors' => $errors], 'barber-create');
            }

            // Vytvorenie používateľa (tvoja pôvodná logika)
            $user = new User();
            $user->setFullName($request->value('name'));
            $user->setEmail($request->value('email'));
            $user->setPhone($request->value('phone'));
            $user->setPassword($request->value('password'));
            $user->setPermissions(User::ROLE_BARBER);
            $user->setCreatedAt(date('Y-m-d H:i:s'));
            $user->save();

            // Vytvorenie barbera
            $barber = new Barber();
            $barber->setUserId($user->getId());
            $barber->setBio($bio);
            $barber->setPhotoPath($photoPath); // Uložíme cestu k fotke
            $barber->setIsActive((bool)$request->value('is_active', true));
            $barber->setCreatedAt(date('Y-m-d H:i:s'));
            $barber->save();

            return $this->redirect($this->url('admin.barbers'));
        }
        return $this->html([], 'barber-create');
    }
    public function deleteBarber(Request $request): Response
    {
        $id = (int) $request->value('id');
        $barber = \App\Models\Barber::getOne($id);

        if ($barber) {
            // CASCADE delete - vymaže aj používateľa
            $user = User::getOne($barber->getUserId());
            if ($user) {
                $user->delete();
            }
        }

        return $this->redirect($this->url('admin.barbers'));
    }

    /**
     * AJAX update pre barbera
     */
    private function updateBarber($id, $field, $value)
    {
        $barber = Barber::getOne($id);
        if (!$barber) {
            return $this->json(['success' => false, 'message' => 'Barber neexistuje']);
        }

        $errors = [];
        $displayValue = $value;
        $badgeClass = null;

        if ($field === 'user_id') {
            return $this->json(['success' => false, 'message' => 'Pole user_id nie je editovateľné']);
        }

        switch ($field) {
            case 'bio':
                $trimmedValue = trim($value);
                if (empty($trimmedValue)) {
                    $errors[] = 'Bio je povinné';
                } elseif (strlen($trimmedValue) > 500) {
                    $errors[] = 'Bio môže mať maximálne 500 znakov';
                } else {
                    $barber->setBio($trimmedValue);
                    $displayValue = $trimmedValue;
                }
                break;

            case 'photo_path':
                $trimmedValue = trim($value);
                if (empty($trimmedValue)) {
                    $errors[] = 'Cesta fotky je povinná';
                } elseif (!filter_var($trimmedValue, FILTER_VALIDATE_URL)) {
                    $errors[] = 'Neplatná URL adresa';
                } elseif (strlen($trimmedValue) > 255) {
                    $errors[] = 'URL môže mať maximálne 255 znakov';
                } else {
                    $barber->setPhotoPath($trimmedValue);
                    $displayValue = $trimmedValue;
                }
                break;

            case 'is_active':
                $intValue = (int)$value;
                if (!in_array($intValue, [0, 1])) {
                    $errors[] = 'Neplatná hodnota. Povolené: 0 (neaktívny), 1 (aktívny)';
                } else {
                    $barber->setIsActive((bool)$intValue);
                    $displayValue = $intValue ? 'Aktívny' : 'Neaktívny';
                    $badgeClass = $intValue ? 'success' : 'danger';
                }
                break;

            default:
                // ak je pole pouzivatela
                $userId = $barber->getUserId();
                $user = User::getOne($userId);
                if (!$user) {
                    return $this->json(['success' => false, 'message' => 'Používateľ neexistuje']);
                }

                switch ($field) {
                    case 'name':
                        $validationError = $this->validateFullName($value, true);
                        if ($validationError) {
                            $errors[] = $validationError;
                        } else {
                            $user->setFullName(trim($value));
                            $displayValue = trim($value);
                        }
                        break;

                    case 'email':
                        $validationError = $this->validateEmailForAdmin($value, $userId, true);
                        if ($validationError) {
                            $errors[] = $validationError;
                        } else {
                            $user->setEmail(trim($value));
                            $displayValue = trim($value);
                        }
                        break;

                    case 'phone':
                        $validationError = $this->validatePhone($value, true);
                        if ($validationError) {
                            $errors[] = $validationError;
                        } else {
                            $user->setPhone(trim($value));
                            $displayValue = trim($value);
                        }
                        break;

                    default:
                        return $this->json(['success' => false, 'message' => 'Nepodporované pole: ' . $field]);
                }

                if (empty($errors)) {
                    $user->save();
                }
                break;
        }

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors]);
        }

        if (!in_array($field, ['name', 'email', 'phone'])) {
            $barber->save();
        }

        return $this->json([
            'success' => true,
            'message' => 'Barber aktualizovaný',
            'value' => $displayValue,
            'badgeClass' => $badgeClass
        ]);
    }
    public function uploadBarberPhoto(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url('admin.barbers'));
        }

        $barberId = (int) $request->value('barber_id');
        $barber = Barber::getOne($barberId);

        if (!$barber) {
            return $this->redirect($this->url('admin.barbers'));
        }

        $photoFile = $request->file('photo');

        if (!$photoFile || !$photoFile->isOk()) {
            return $this->redirect($this->url('admin.barbers'));
        }

        // typ fotky
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = $photoFile->getType();

        if (!in_array($mimeType, $allowedTypes)) {
            return $this->redirect($this->url('admin.barbers'));
        }

        if ($photoFile->getSize() > 2 * 1024 * 1024) {
            return $this->redirect($this->url('admin.barbers'));
        }

        $tmpPath = $photoFile->getFileTempPath();
        if (file_exists($tmpPath)) {
            $imageInfo = @getimagesize($tmpPath);

            if ($imageInfo === false) {
                return $this->redirect($this->url('admin.barbers'));
            }

            list($width, $height) = $imageInfo;

            if ($width < 200 || $height < 200) {
                return $this->redirect($this->url('admin.barbers'));
            }
        }

        $originalName = $photoFile->getName();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = 'barber_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($extension);

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
        }
        return $this->redirect($this->url('admin.barbers'));
    }
    ///////////////////////////////////////////////////////////VALIDACIE

    private function validateFullName(?string $full_name, bool $required = false): ?string
    {
        if ($required && (empty($full_name) || trim($full_name) === '')) {
            return "Meno a priezvisko je povinné";
        }

        if (!empty($full_name) && trim($full_name) !== '') {
            $trimmed = trim($full_name);
            $trimmed = preg_replace('/\s+/', ' ', $trimmed);

            if (strlen(str_replace(' ', '', $trimmed)) < 4) {
                return "Meno a priezvisko musí obsahovať aspoň 4 znaky (bez medzier)";
            }
        }

        return null;
    }

    private function validatePhone(?string $phone, bool $required = true): ?string
    {
        if ($required && empty($phone)) {
            return "Telefónne číslo je povinné";
        }

        if (!empty($phone)) {
            $phone = trim($phone);
            $clean_phone = preg_replace('/[^0-9]/', '', $phone);
            $digit_count = strlen($clean_phone);

            if ($digit_count < 9 || $digit_count > 15) {
                return "Telefónne číslo musí obsahovať 9 až 15 číslic";
            }

            if (!preg_match('/^[\d\s\-+()]+$/', $phone)) {
                return "Telefónne číslo obsahuje nepovolené znaky";
            }
        }

        return null;
    }

    private function validateEmailForAdmin(?string $email, int $userId, bool $required = true): ?string
    {
        if ($required && empty($email)) {
            return "Email je povinný";
        }

        if (!empty($email)) {
            $email = trim($email);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "Neplatný formát emailu";
            } else {
                $existingUser = User::getOneByEmail($email);
                if ($existingUser && $existingUser->getId() !== $userId) {
                    return "Email už je používaný iným používateľom";
                }
            }
        }

        return null;
    }
    private function validatePassword(?string $password, bool $required = true): ?string
    {
        if ($required && empty($password)) {
            return "Heslo je povinné";
        }

        if (!empty($password)) {
            if (strlen($password) < 8) {
                return "Heslo musí mať aspoň 8 znakov";
            }

            if (!preg_match('/[A-Z]/', $password)) {
                return "Heslo musí obsahovať aspoň jedno veľké písmeno";
            }

            if (!preg_match('/[0-9]/', $password)) {
                return "Heslo musí obsahovať aspoň jednu číslicu";
            }
        }

        return null;
    }

    private function getUserBadgeClass($permissions)
    {
        $badges = [
            User::ROLE_CUSTOMER => 'info',
            User::ROLE_BARBER => 'primary',
            User::ROLE_ADMIN => 'warning'
        ];

        return $badges[$permissions] ?? 'secondary';
    }
}
