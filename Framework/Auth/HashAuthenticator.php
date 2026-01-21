<?php

namespace Framework\Auth;

use App\Models\User;
use Framework\Core\App;
use Framework\Core\IIdentity;

/**
 * Class HashAuthenticator
 * A basic implementation of user authentication using hardcoded credentials.
 *
 * @package App\Auth
 */
class HashAuthenticator extends SessionAuthenticator
{
    // Hardcoded username for authentication
    public const LOGIN = "admin";
    // Hash of the password "admin"
    public const PASSWORD_HASH = '$2y$10$GRA8D27bvZZw8b85CAwRee9NH5nj4CQA6PDFMc90pN9Wi4VAWq3yq';
    // Display name for the logged-in user
    public const USERNAME = "Admin";
    // Application instance

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    //kazdemu bere z databazy
    protected function authenticate(string $email, string $password): ?IIdentity
    {
        $user = User::getAll('`email` = ?', [$email]);

        if(count($user) === 1) {
            $user = $user[0];

            if($user->checkPassword($password)) {
                return $user;
            }
            // na stare plaint text hesla
            $storedPassword = $user->getPassword();
            if ($password === $storedPassword) {
                $user->setPassword($password);
                $user->save();
                return $user;
            }

        }
        return null;
    }
}
