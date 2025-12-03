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
        return $this->redirect(Configuration::LOGIN_URL);
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
                return $this->redirect($this->url("admin.index"));
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
        return $this->html();
    }

    //NAUCNA METODA S PETKOM
    //MOJ SERVER JE CONTROLLER
    //najprv rob kontroly na servery
    public function register(Request $request): Response
    {
        //krokuj si to

        //vedelo by vytvorit prazdneho usera, lebo vrati najprv prazdny register
        //najprv idu kontroly az potom ci sa ma vytvarat user

        $formData = $this->app->getRequest()->post();
        if(isset($formData['submit'])) {
            //todo: validacie
            // VALIDÁCIA HESLA
            if(empty($formData['password'])) {
                $errors['password'] = "Heslo je povinné.";
            } elseif(strlen($formData['password']) < 8) {
                $errors['password'] = "Heslo musí mať aspoň 8 znakov.";
            } elseif(!preg_match('/[A-Z]/', $formData['password'])) {
                $errors['password'] = "Heslo musí obsahovať aspoň jedno veľké písmeno.";
            } elseif(!preg_match('/[0-9]/', $formData['password'])) {
                $errors['password'] = "Heslo musí obsahovať aspoň jednu číslicu.";
            }

            if (count($errors) > 0) {
                return $this->html(['errors' => $errors]);
            }
            $user = new User();
            $user->setFullname($formData['full_name']);     //berie to nazvy z formdata z toho ako su vo view(html)
            $user->setEmail($formData['email']);
            $user->setPassword($formData['password']);
            $user->setPhone($formData['phone']);
            $user->setPermissions(0); //defaultne nastavenie pre noveho usera
            //todo:nastavenie datumov!
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
        // Bezpečnostná verzia - iba svoj účet
        $user = $this->app->getAuthenticator()->getUser();

        $full_name = $request->value('full_name');

        // Aktualizácia
        $user->setFullname($full_name);
        $user->save();

        // NAČITAJ ČERSTVÉ DÁTA Z DATABÁZY!
        $freshUser = User::getOne($user->getId());

        $message = "Účet bol úspešne upravený.";
        return $this->html(['user' => $freshUser, 'message' => $message], 'edit');


    }
}
