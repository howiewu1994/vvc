<?php
namespace VVC\Controller;

use VVC\Model\Database\Reader;
use VVC\Model\Database\Updater;

class AccountController extends BaseController
{
    protected $template = 'my_account.twig';

    public function showChangePasswordPage()
    {
        $this->render();
    }

    public function showChangePasswordFailPage()
    {
        $this->render();
    }

    public function changePassword(array $post)
    {
        if (!$this->isClean($post)) {
            $this->flash('fail', 'Invalid password, try another');
            return $this->showChangePasswordFailPage();
        }

        $curPassword = $post['cur_password'];
        $newPassword = $post['new_password'];
        $confirmPassword = $post['confirm_password'];

        if ($newPassword != $confirmPassword) {
            $this->flash('fail', "Passwords do not match");
            return $this->showChangePasswordFailPage();
        }

        $userId = Auth::getUserId();

        if ($userId === false) {
            $this->flash(
                'fail', 'Something went wrong. Please re-login and try again'
            );
            return $this->showChangePasswordFailPage();
        }

        try {
            $dbReader = new Reader();
            $user = $dbReader->findUserById($userId);

            if (empty($user)) {
                $this->flash(
                    'fail', 'Something went wrong. Please re-login and try again'
                );
                return $this->showChangePasswordFailPage();
            }

            if (!password_verify($curPassword, $user->getPassword())) {
                $this->flash('fail', 'Incorrect current password');
                return $this->showChangePasswordFailPage();
            }

            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

            $dbUpdater = new Updater();
            $dbUpdater->changePassword($userId, $hashed);

            $this->flash('success', 'Password changed successfully');
            return Router::redirect('/account');   // auth token?

        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to change user password', $e);
            $this->flash('fail', 'Operation failed, please try again');
            return $this->showChangePasswordFailPage();
        }
    }
}
