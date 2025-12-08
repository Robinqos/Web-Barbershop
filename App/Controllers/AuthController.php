<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\User;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

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
    /**
     * Redirects to the login page.
     *
     * This action serves as the default landing point for the authentication section of the application, directing
     * users to the login URL specified in the configuration.
     *
     * @return Response The response object for the redirection to the login page.
     */
    public function index(Request $request): Response
    {
        if (!($this->app->getAppUser()->isLoggedIn())) {  //lepsie takto ako $this->user->isLoggedIn()
            return $this->redirect($this->url("auth.login"));
        }
        //$this->app->getAppUser()->getIdentity() instanceof \App\Models\User)
        //tu uz je nacitany user z databazy
        return $this->html();
    }

    /**
     * Authenticates a user and processes the login request.
     *
     * This action handles user login attempts. If the login form is submitted, it attempts to authenticate the user
     * with the provided credentials. Upon successful login, the user is redirected to the admin dashboard.
     * If authentication fails, an error message is displayed on the login page.
     *
     * @return Response The response object which can either redirect on success or render the login view with
     *                  an error message on failure.
     * @throws Exception If the parameter for the URL generator is invalid throws an exception.
     */
    public function login(Request $request): Response
    {
        $logged = null;
        if ($request->hasValue('submit')) {
            $logged = $this->app->getAuthenticator()->login($request->value('email'), $request->value('password'));
            if ($logged) {
                $user = $this->app->getAuthenticator()->getUser();
                date_default_timezone_set('Europe/Bratislava');
                $user->setLastLogin(date('Y-m-d H:i:s'));
                $user->save();
                return $this->redirect($this->url("auth.index"));
            }
        }
        $message = $logged === false ? 'Bad email or password' : null;
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

    //NAUCNA METODA
    //MOJ SERVER JE CONTROLLER
    //najprv rob kontroly na servery
    public function register(Request $request): Response
    {
        $formData = $this->app->getRequest()->post();

        // Ak je formulár odoslaný
        if (isset($formData['submit'])) {
            $errors = [];

            if ($error = $this->validateFullName($formData['full_name'] ?? null, false)) {
                $errors['full_name'] = $error;
            }

            if ($error = $this->validatePhone($formData['phone'] ?? null, true)) {
                $errors['phone'] = $error;
            }

            if ($error = $this->validateEmail($formData['email'] ?? null, true, true)) {
                $errors['email'] = $error;
            }

            if ($error = $this->validatePassword($formData['password'] ?? null, true)) {
                $errors['password'] = $error;
            }

            if ($error = $this->validatePasswordConfirm($formData['password'] ?? null,
                $formData['password_confirm'] ?? null,
                true)) {
                $errors['password_confirm'] = $error;
            }

            if ($error = $this->validateTerms($formData['terms'] ?? null, true)) {
                $errors['terms'] = $error;
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
        $user = $this->app->getAuthenticator()->getUser(); //aktualne prihlaseny user
        return $this->html(compact('user'));
    }

    public function update(Request $request): Response
    {
        $user = $this->app->getAuthenticator()->getUser();
        $formData = $request->post();

        if (isset($formData['submit'])) {
            $errors = [];

            // check full name
            if ($error = $this->validateFullName($formData['full_name'] ?? null, false)) {
                $errors['full_name'] = $error;
            }

            // check phone
            if ($error = $this->validatePhone($formData['phone'] ?? null, true)) {
                $errors['phone'] = $error;
            }

            // check email
            if ($error = $this->validateEmail($formData['email'] ?? null, true, false)) {
                $errors['email'] = $error;
            } else {
                $existingUser = User::getOneByEmail($formData['email']);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $errors['email'] = "Tento e-mail je už registrovaný";
                }
            }

            $newPassword = $formData['new_password'] ?? null;
            $currentPassword = $formData['current_password'] ?? null;
            $confirmPassword = $formData['confirm_password'] ?? null;

            if (!empty($newPassword)) {
                if ($error = $this->validatePassword($newPassword, true)) {
                    $errors['new_password'] = $error;
                }

                if ($error = $this->validatePasswordConfirm($newPassword, $confirmPassword, true)) {
                    $errors['confirm_password'] = $error;
                }

                if (empty($currentPassword)) {
                    $errors['current_password'] = "Aktuálne heslo je povinné pri zmene hesla";
                } elseif ($currentPassword !== $user->getPassword()) {  // PRIAMEPOROVNANIE - BEZ HASHU
                    $errors['current_password'] = "Nesprávne aktuálne heslo";
                }
            }

            if (!empty($errors)) {
                return $this->html([
                    'user' => $user,
                    'errors' => $errors,
                    'message' => 'Opravte chyby vo formulári'
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

        $user = $this->app->getAuthenticator()->getUser();
        return $this->html(compact('user'), 'delete');
    }

    public function delete(Request $request): Response
    {
        if (!($this->app->getAppUser()->isLoggedIn())) {
            return $this->redirect($this->url("auth.login"));
        }

        $id = (int)$request->value('id');

        $user = User::getOne($id);

        if (!$user) {
            return $this->redirect($this->url("auth.index"));
        }

        $currentUser = $this->app->getAuthenticator()->getUser();
        if ($user->getId() !== $currentUser->getId()) {
            // Ak chce zmenit ineho
            return $this->redirect($this->url("auth.index"));
        }

        if (!$request->hasValue('confirm') || $request->value('confirm') !== 'yes') {
            return $this->html(compact('user'), 'delete');
        }

        $user->delete();
        $this->app->getAuthenticator()->logout();

        return $this->redirect($this->url("auth.login"));
    }

    ////////////////////////////////////////////////////////////Metody na validacie
    private function validateFullName(?string $full_name, bool $required = false): ?string
    {
        if ($required && (empty($full_name) || trim($full_name) === '')) {
            return "Meno je povinné";
        }

        if (!empty($full_name) && trim($full_name) !== '') {
            $trimmed = trim($full_name);
            $trimmed = preg_replace('/\s+/', ' ', $trimmed);

            if (strlen(str_replace(' ', '', $trimmed)) < 2) {
                return "Meno musí obsahovať aspoň 2 ne-medzerové znaky";
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
                return "Telefónne číslo musí obsahovať 9-15 číslic";
            }

            if (!preg_match('/^[\d\s\-+()]+$/', $phone)) {
                return "Telefón môže obsahovať iba čísla, medzery, +, - a ()";
            }
        }

        return null;
    }
    private function validateEmail(?string $email, bool $required = true, bool $checkUnique = true): ?string
    {
        if ($required && empty($email)) {
            return "Email je povinný";
        }

        if (!empty($email)) {
            $email = trim($email);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "Zadajte platný email (napr. priklad@email.sk)";
            } elseif ($checkUnique) {
                // Kontrola, či už existuje používateľ s týmto emailom
                $existing_user = User::getOneByEmail($email);
                if ($existing_user) {
                    return "Tento e-mail je už registrovaný";
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

    private function validatePasswordConfirm(?string $password, ?string $password_confirm,
                                             bool $required = true): ?string
    {
        if ($required && empty($password_confirm)) {
            return "Potvrdenie hesla je povinné";
        }

        if (!empty($password_confirm) && $password !== $password_confirm) {
            return "Heslá sa nezhodujú";
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
    /*public function confirmDelete(Request $request): Response
    {
        // Kontrola, či používateľ je prihlásený
        if (!($this->app->getAppUser()->isLoggedIn())) {
            return $this->redirect($this->url("auth.login"));
        }

        $user = $this->app->getAuthenticator()->getUser();
        return $this->html(compact('user'), 'delete');
    }

    public function delete(Request $request): Response
    {
        // Kontrola, či používateľ je prihlásený
        if (!($this->app->getAppUser()->isLoggedIn())) {
            return $this->redirect($this->url("auth.login"));
        }

        // Ak nebolo potvrdené, zobraz potvrdzovací formulár
        if (!$request->hasValue('confirm') || $request->value('confirm') !== 'yes') {
            $user = $this->app->getAuthenticator()->getUser();
            return $this->html(compact('user'), 'delete');
        }

        // Ak je potvrdené, vykonaj zmazanie
        $user = $this->app->getAuthenticator()->getUser();
        $user->delete();

        // Odhlásenie používateľa
        $this->app->getAuthenticator()->logout();

        // Presmerovanie na login
        return $this->redirect($this->url("auth.login"));
    }*/

    /*public function update(Request $request): Response
    {
        // Bezpečnostná verzia - iba svoj účet
        $user = $this->app->getAuthenticator()->getUser();

        // Získaj ID z formulára pre kontrolu
        $idFromForm = (int)$request->value('id');

        // Kontrola, či používateľ nesnaží upravovať niekoho iného účet
        if ($idFromForm !== $user->getId()) {
            $error = "Nemáte oprávnenie upravovať tento účet.";
            return $this->html(['user' => $user, 'error' => $error], 'edit');
        }

        $full_name = $request->value('full_name');

        // Validácia
        $errors = [];
        if (empty($full_name)) {
            $errors['full_name'] = "Meno a priezvisko je povinné.";
        }

        if (count($errors) > 0) {
            return $this->html(['user' => $user, 'errors' => $errors], 'edit');
        }

        // Aktualizácia
        $user->setFullname($full_name);
        $user->save();

        $message = "Účet bol úspešne upravený.";
        return $this->html(['user' => $user, 'message' => $message], 'edit');
    }*/
}
