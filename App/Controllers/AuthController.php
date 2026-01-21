<?php

namespace App\Controllers;

use App\Models\Reservation;
use App\Models\Review;
use App\Models\User;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;
use App\Traits\ValidationTrait;

/**
 * Class AuthController
 *
 * This controller handles authentication actions such as login, logout, and redirection to the login page. It manages
 * user sessions and interactions with the authentication system.
 *
 * @package App\Controllers
 */
class AuthController extends BaseController
{
    use ValidationTrait;

    public function index(Request $request): Response
    {
        if (!($this->app->getAppUser()->isLoggedIn())) {
            return $this->redirect($this->url("auth.login"));
        }

        // ziskame user
        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();

        if (!$identity instanceof User) {
            return $this->redirect($this->url("auth.login"));
        }

        $user = $identity;

        if ($user->getPermissions() >= User::ROLE_ADMIN) {
            return $this->redirect($this->url("admin.index"));
        }

        if ($user->getPermissions() === User::ROLE_BARBER) {
            return $this->redirect($this->url("barber.index"));
        }

        // prihlaseny user = nacitaj rezervacie
        $reservations = Reservation::getAll(
            'user_id = ? AND status IN (?, ?, ?)',
            [$user->getId(), 'pending', 'completed', 'canceled'],
            'reservation_date DESC'
        );

        // recenzie
        $userReviews = Review::getAll(
            'user_id = ?',
            [$user->getId()]
        );

        // mapovanie
        $reviewMap = [];
        foreach ($userReviews as $review) {
            if ($review->getReservationId()) {
                $reviewMap[$review->getReservationId()] = $review;
            }
        }

        return $this->html(compact('reservations', 'reviewMap'));
    }

    public function login(Request $request): Response
    {
        $logged = null;
        if ($request->hasValue('submit')) {
            $logged = $this->app->getAuthenticator()->login($request->value('email'), $request->value('password'));
            if ($logged) {
                $identity = $this->app->getAuthenticator()->getUser()->getIdentity();

                if ($identity instanceof \App\Models\User) {
                    $user = $identity;
                    date_default_timezone_set('Europe/Bratislava');
                    $user->setLastLogin(date('Y-m-d H:i:s'));
                    $user->save();

                    if ($user->getPermissions() >= User::ROLE_ADMIN) {
                        return $this->redirect($this->url("admin.index"));
                    } else {
                        return $this->redirect($this->url("auth.index"));
                    }
                }
            }
        }
        $message = $logged === false ? 'Nesprávne prihlasovacie údaje!' : null;
        return $this->html(compact("message"));
    }

    /**
     * Logs out the current user.
     *
     * This action terminates the user's session and redirects them to a view. It effectively clears any authentication
     * tokens or session data associated with the user.
     *
     * @return ViewResponse The response object that renders the logout view.
     */
    public function logout(Request $request): Response
    {
        $this->app->getAuthenticator()->logout();
        return $this->redirect($this->url("auth.login"));
    }

    //MOJ SERVER JE CONTROLLER
    //najprv rob kontroly na servery
    public function register(Request $request): Response
    {
        $formData = $this->app->getRequest()->post();

        if (isset($formData['submit'])) {
            $errors = [];

            if ($error = $this->validateFullName($formData['full_name'] ?? null, true, false)) {
                $errors['full_name'] = 'Neplatné údaje';
            }

            if ($error = $this->validatePhone($formData['phone'] ?? null, true, false)) {
                $errors['phone'] = 'Neplatné údaje';
            }

            if ($error = $this->validateEmail($formData['email'] ?? null, true, true, 0, false)) {
                $errors['email'] = 'Neplatné údaje';
            }

            if ($error = $this->validatePassword($formData['password'] ?? null, true, false)) {
                $errors['password'] = 'Neplatné údaje';
            }

            if ($error = $this->validatePasswordConfirm($formData['password'] ?? null,$formData['password_confirm'] ?? null,
                true,
                false
            )) {
                $errors['password_confirm'] = $error;
            }

            if ($error = $this->validateTerms($formData['terms'] ?? null, true)) {
                $errors['terms'] = 'Musíte súhlasiť so spracovaním osobných údajov';
            }

            if (!empty($errors)) {
                return $this->html([
                    'errors' => $errors,
                    'formData' => $formData
                ]);
            }

            $user = new User();

            if (!empty($formData['full_name'])) {
                $user->setFullname(trim($formData['full_name']));
            }

            $user->setEmail(trim($formData['email']));
            $user->setPassword($formData['password']);
            $user->setPhone(trim($formData['phone']));
            $user->setPermissions(0);

            date_default_timezone_set('Europe/Bratislava');
            $user->setCreatedAt(date('Y-m-d H:i:s'));
            $user->setLastLogin(null);

            $user->save();

            return $this->redirect($this->url("auth.login"));
        }

        return $this->html();
    }

    public function edit(Request $request): Response
    {
        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();
        if (!$identity instanceof \App\Models\User) {
            return $this->redirect($this->url("auth.login"));
        }
        $user = $identity; //aktualne prihlaseny user
        return $this->html(compact('user'));
    }

    public function update(Request $request): Response
    {
        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();
        if (!$identity instanceof User) {
            return $this->redirect($this->url("auth.login"));
        }
        $user = $identity;
        $formData = $request->post();

        if (isset($formData['submit'])) {
            $errors = [];

            // check full name
            if ($error = $this->validateFullName($formData['full_name'] ?? null, true, false)) {
                $errors['full_name'] = $error;
            }

            // check phone
            if ($error = $this->validatePhone($formData['phone'] ?? null, true, false)) {
                $errors['phone'] = $error;
            }

            // check email
            if ($error = $this->validateEmail(
                $formData['email'] ?? null,
                true,
                false,
                $user->getId(),
                false
            )) {
                $errors['email'] = $error;
            } else {
                // kontrola unique
                $existingUser = User::getOneByEmail($formData['email']);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $errors['email'] = "Neplatné údaje";
                }
            }

            $newPassword = $formData['new_password'] ?? null;
            $currentPassword = $formData['current_password'] ?? null;
            $confirmPassword = $formData['confirm_password'] ?? null;

            if (!empty($newPassword)) {
                if ($error = $this->validatePassword($newPassword, true, false)) {
                    $errors['new_password'] = $error;
                }

                if ($error = $this->validatePasswordConfirm(
                    $newPassword,
                    $confirmPassword,
                    true,
                    false
                )) {
                    $errors['confirm_password'] = $error;
                }

                if (empty($currentPassword)) {
                    $errors['current_password'] = "Aktuálne heslo je povinné pri zmene hesla";
                } elseif (!$user->checkPassword($currentPassword)) {
                    $errors['current_password'] = "Nesprávne aktuálne heslo";
                }
            }

            if (!empty($errors)) {
                return $this->html([
                    'user' => $user,
                    'errors' => $errors,
                    'message' => 'Neplatné údaje',
                ], 'edit');
            }

            if (!empty($formData['full_name'])) {
                $user->setFullname(trim($formData['full_name']));
            } else {
                $user->setFullname(null);
            }

            $user->setPhone(trim($formData['phone']));
            $user->setEmail(trim($formData['email']));

            if (!empty($newPassword)) {
                $user->setPassword($newPassword);
            }

            $user->save();

            $message = "Údaje boli úspešne upravené.";
            return $this->html(['user' => $user, 'message' => $message, 'success' => true], 'edit');
        }

        return $this->html(['user' => $user], 'edit');
    }
    public function confirmDelete(Request $request): Response
    {
        if (!($this->app->getAppUser()->isLoggedIn())) {
            return $this->redirect($this->url("auth.login"));
        }

        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();
        if (!$identity instanceof User) {
            return $this->redirect($this->url("auth.login"));
        }
        $user = $identity;
        return $this->html(compact('user'), 'delete');
    }

    public function delete(Request $request): Response
    {
        // Kontrola prihlásenia
        if (!($this->app->getAppUser()->isLoggedIn())) {
            return $this->redirect($this->url("auth.login"));
        }

        $id = (int)$request->value('id');
        $user = User::getOne($id);

        if (!$user) {
            return $this->redirect($this->url("auth.index"));
        }

        $identity = $this->app->getAuthenticator()->getUser()->getIdentity();
        if (!$identity instanceof \App\Models\User) {
            return $this->redirect($this->url("auth.login"));
        }
        $currentUser = $identity;
        if ($user->getId() !== $currentUser->getId()) {
            return $this->redirect($this->url("auth.index"));
        }

        if (!$request->hasValue('confirm') || $request->value('confirm') !== 'yes') {
            return $this->redirect($this->url("auth.confirmDelete", ['id' => $id]));
        }

        $user->delete();
        $this->app->getAuthenticator()->logout();

        return $this->redirect($this->url("auth.login"));
    }
}
