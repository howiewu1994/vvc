<?php
namespace VVC\Controller;

use VVC\Model\Database\Reader;

/**
 * Processes user login
 */
class LoginController extends BaseController
{
    protected $template = 'login.twig';

    public function showLoginPage()
    {
        $this->render();
    }

    public function showLoginFailPage(string $username)
    {
        $this->addTwigVar('username', $username);

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
            $this->flash('fail', 'Username or password contain invalid characters');
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
            $this->flash('fail', 'Login failed, please try again');
            return $this->showLoginFailPage($username);
        }

        if (empty($user)) {
            $this->flash('fail', 'Username was not found');
            return $this->showLoginFailPage($username);
        }

        if (!password_verify($password, $user['password'])) {
            $this->flash('fail', 'Password is incorrect');
            return $this->showLoginFailPage($username);
        }

        global $session;

        $this->flash('success', "Welcome back, {$user['username']}");
        $authToken = Auth::encodeToken($user['id'], $user['role_id']);
        Router::redirect('/', $authToken);
    }
}
