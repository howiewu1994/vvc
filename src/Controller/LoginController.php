<?php
namespace VVC\Controller;

use VVC\Model\Database\Reader;

/**
 * Processes user authentication
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

    public function login(array $postData)
    {
        if (!$this->isClean($postData)) {
            $this->flashes->add('fail',
                'Username or password contain invalid characters'
            );
            return $this->showLoginFailPage($postData['username']);
        }

        $username = $postData['username'];
        $password = $postData['password'];

        try {
            $dbReader = new Reader();
            $user = $dbReader->findUserByUsername($username);
        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flashes->add('fail', 'Login failed, please try again');
            return $this->showLoginFailPage($username);
        }

        if (empty($user)) {
            $this->flashes->add('fail', 'Username was not found');
            return $this->showLoginFailPage($username);
        }
        //(!password_verify($password, $user['password']
        if ($password != $user['password']) {
            $this->flashes->add('fail', 'Password is incorrect');
            return $this->showLoginFailPage($username);
        }

        $this->flashes->add('success', "Welcome back, {$user['username']}");
        $this->router->makeCookies($user['id'], $user['role_id']);
        $this->router->redirect('/');

        //redirect('/', makeCookies($user['id'], $user['role_id']));
    }

}
