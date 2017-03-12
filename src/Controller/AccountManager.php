<?php
namespace VVC\Controller;

use VVC\Model\Database\Creator;
use VVC\Model\Database\Deleter;
use VVC\Model\Database\Reader;
use VVC\Model\Database\Updater;

/**
 * Admin controller for managing user accounts
 */
class AccountManager extends AdminController
{
    public function showAccountListPage()
    {
        try {
            $dbReader = new Reader();
            $users = $dbReader->getAllUsers();
        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            $this->showDashboardPage();
        }

        $this->setTemplate('admin_accs.twig');
        $this->addTwigVar('users', $users);
        $this->render();
    }

    public function showAddAccountPage(
        string $username = null, int $roleId = null
    ) {
        $this->setTemplate('add_account.twig');
        $this->addTwigVar('username', $username);
        $this->addTwigVar('role', $roleId);
        $this->render();
    }

    public function addAccount(array $post)
    {
        if (!$this->isClean($post)) {
            $this->flash('fail', 'Username or password contain invalid characters');
            return $this->showAddAccountPage($post['username'], $post['role_id']);
        }

        $username = $post['username'];
        $password = $post['password'];
        $roleId = $post['role_id'] ?? 2;

        try {
            $dbReader = new Reader();
            $user = $dbReader->findUserByUsername($username);

            if (!empty($user)) {
                $this->flash('fail', 'This username is already registered');
                return $this->showAddAccountPage($username, $roleId);
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $dbCreator = new Creator();
            $user = $dbCreator->createUser($username, $hashed, $roleId);

            $this->flash('success', 'Added successfully');
            return Router::redirect('/admin/accounts');

        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            return $this->showAddAccountPage($username, $roleId);
        }
    }

    public function showChangeAccountPage(int $userId)
    {
        try {
            $dbReader = new Reader();
            $user = $dbReader->findUserById($userId);
        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            Router::redirect('/admin/accounts');
        }

        if (empty($user)) {
            $this->flash('fail', 'User not found');
            Router::redirect('/admin/accounts');
        }

        $this->setTemplate('change_account.twig');
        $this->addTwigVar('user', $user);
        $this->render();
    }

    public function changeAccount(int $userId, array $post)
    {
        if (!$this->isClean($post)) {
            $this->flash('fail', 'Username or password contain invalid characters');
            return $this->showChangeAccountPage($userId);
        }

        $username = $post['username'];
        $roleId = $post['role_id'];
        $password = $post['password'];

        try {
            $dbReader = new Reader();

            $duplicateUser = $dbReader->findUserByUsername($username);
            if (!empty($duplicateUser)
                && $duplicateUser->getId() != $userId) {
                $this->flash('fail', 'This username is already registered');
                return $this->showChangeAccountPage($userId);
            }

            $oldUser = $dbReader->findUserById($userId);
            if (empty($oldUser)) {
                $this->flash('fail', 'Some problem occurred, please try again');
                return $this->showChangeAccountPage($userId);
            }

            $password = empty($password)
                ? $oldUser->getPassword()
                : password_hash($password, PASSWORD_DEFAULT);

            $dbUpdater = new Updater();
            $dbUpdater->updateUser(
                $userId,
                $username,
                $password,
                $roleId,
                $oldUser->getCreatedAt()
            );

            $this->flash('success', 'Account Updated');
            return Router::redirect('/admin/accounts');

        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            return $this->showChangeAccountPage($userId);
        }
    }

    public function deleteAccount($userId)
    {
        try {
            $dbDeleter = new Deleter();
            $result = $dbDeleter->deleteUser($userId);
            if (!$result) {
                $this->flash('fail', 'Could not delete this user, try again');
                return Router::redirect('/admin/accounts');
            }

            $this->flash('success', 'Account Deleted');
            return Router::redirect('/admin/accounts');

        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            return Router::redirect('/admin/accounts');
        }
    }
}
