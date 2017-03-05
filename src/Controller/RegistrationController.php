<?php
namespace VVC\Controller;

use VVC\Model\Database\Reader;
use VVC\Model\Database\Creator;

/**
 * Processes user registration
 */
class RegistrationController extends BaseController
{

    public function showRegistrationPage()
    {
        $this->render();
    }

    public function showRegistrationFailPage(string $username)
    {
        $this->addVar('username', $username);

        $this->render();
    }

    public function register(array $postData)
    {
        if (!$this->isClean($postData)) {
            $this->flashes->add('fail',
                'Username or password contain invalid characters'
            );
            return $this->showRegistrationFailPage($postData['username']);
        }

        $username = $postData['username'];
        $password = $postData['password'];
        $confirmPassword = $postData['confirm_password'];

        if ($password != $confirmPassword) {
            $this->flashes->add('fail', 'Passwords do not match');
            return $this->showRegistrationFailPage($postData['username']);
        }

        try {
            $dbReader = new Reader();
            $user = $dbReader->findUserByUsername($username);

            if (!empty($user)) {
                $this->flashes->add('fail', 'This username is already registered');
                return $this->showRegistrationFailPage($postData['username']);
            }

            //$hashed = password_hash($password, PASSWORD_DEFAULT);
            $hashed = $password;

            $dbCreator = new Creator();
            $user = $dbCreator->createUser($username, $hashed);

            $this->flashes->add('success', 'Registration complete');
            $this->router->makeCookies($user['id'], $user['role_id']);
            $this->router->redirect('/');

        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flashes->add('fail', 'Registration failed, please try again');
            return $this->showRegistrationFailPage($postData['username']);
        }
    }
}
