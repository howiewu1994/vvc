<?php
namespace VVC\Controller;

use VVC\Model\Database\Reader;

/**
 * Processes user login
 */
class LoginController extends BaseController
{

    public function showLoginPage()
    {
        $this->render();
    }

    public function showLoginFailPage(string $username)
    {
        $this->addVar('username', $username);

        $this->render();
    }

    /**
     * Verifies login info, logs in user and redirects to homepage
     * OR stays on login page and displays errors
     * @param  array  $post - [username, password]
     */
    public function login(array $post)
    {
        if (!$this->isClean($post)) {
            $this->flashBag->add('fail',
                'Username or password contain invalid characters'
            );
            return $this->showLoginFailPage($post['username']);
        }

        $username = $post['username'];
        $password = $post['password'];

        try {
            $dbReader = new Reader();
            $user = $dbReader->findUserByUsername($username);
        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flashBag->add('fail', 'Login failed, please try again');
            return $this->showLoginFailPage($username);
        }

        if (empty($user)) {
            $this->flashBag->add('fail', 'Username was not found');
            return $this->showLoginFailPage($username);
        }

        if (!password_verify($password, $user['password'])) {
            $this->flashBag->add('fail', 'Password is incorrect');
            return $this->showLoginFailPage($username);
        }

        $this->flashBag->add('success', "Welcome back, {$user['username']}");
        $authToken = Auth::encodeToken($user['id'], $user['role_id']);
        Router::redirect('/', $authToken);
    }

}
