<?php
namespace VVC\Controller;

use VVC\Controller\Router;
use VVC\Controller\BaseController;
use VVC\Model\Database\Reader;

class LoginController extends BaseController
{
    public function __construct(Router $router, $template)
    {
        parent::__construct($router, $template);
    }

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
            $user = Reader::findUserByUsername($username);

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
            $this->router->redirect('/');

        } catch (\Exception $e) {
            // TODO logError($e);
            $this->flashes->add('fail', 'Login failed, please try again');
            return $this->showLoginFailPage($username);
        }

        //redirect('/', makeCookies($user['id'], $user['role_id']));
    }

}
