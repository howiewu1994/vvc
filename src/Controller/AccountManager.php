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

    public function batchAddAccounts(array $users)
    {
        try {
            $dbReader = new Reader();
            $dbCreator = new Creator();
        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database connection failed');
            return Router::redirect('/admin/accounts');
        }

        $good = [];
        $bad = [];

        foreach ($users as $user) {
            if (!$this->isClean($user)) {
                $bad['data'][] = $user;
                continue;
            }

            try {
                $duplicate = $dbReader->findUserByUsername($user['username']);

                if ($duplicate) {
                    $bad['duplicate'][] = $user;
                    continue;
                }

                $password = password_hash($user['password'], PASSWORD_DEFAULT);
                $newUser = $dbCreator->createUser(
                    $user['username'], $password, $user['roleId']
                );

                $good[] = $newUser;

            } catch (\Exception $e) {
                // TODO logError($e);
                // throw $e;
                $bad['db'][] = $user;
                continue;
            }
        }
        // print_r($good);
        // print_r($bad);
        // exit;

        $total = count($users);

        if (!empty($good)) {
            $goodOut = "Completed additions: " . count($good) . "/$total\n\n";
            $goodOut .= "[id] - [username]\n";

            foreach ($good as $user) {
                $goodOut .=
                    $user->getId() . " - " . $user->getUsername() . "\n";
            }

            $this->flash('success', $goodOut);
        }


        if (!empty($bad)) {
            $badCount = 0;
            foreach ($bad as $reason) {
                foreach ($reason as $users) {
                    $badCount++;
                }
            }

            $badOut = "Not added: " . $badCount . "/$total\n";

            foreach ($bad as $reason => $users) {
                switch ($reason) {

                    case 'data' :
                        $badOut .= "\nBad input data:\n";
                        $badOut .= "[username]\n";
                        foreach ($users as $user) {
                            $badOut .= $user['username'] . "\n";
                        }
                        break;

                    case 'duplicate' :
                        $badOut .= "\nDuplicates:\n";
                        $badOut .= "[username]\n";
                        foreach ($users as $user) {
                            $badOut .= $user['username'] . "\n";
                        }
                        break;

                    case 'db' :
                        $badOut .= "\nDatabase failure:\n";
                        $badOut .= "[username]\n";
                        foreach ($users as $user) {
                            $badOut .= $user['username'] . "\n";
                        }
                        break;
                }
            }

            $this->flash('warning', $badOut);
        }

        return Router::redirect('/admin/accounts');
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

    public function deleteAccount(int $userId)
    {
        try {
            $dbDeleter = new Deleter();
            $deletedUser = $dbDeleter->deleteUser($userId);
            if (!$deletedUser) {
                $this->flash('fail',
                    "Could not delete user <b>$userId</b>, try again"
                );
                return Router::redirect('/admin/accounts');
            }

            $name = $deletedUser->getUsername();

            $this->flash('success', "User <b>$name</b> deleted");
            return Router::redirect('/admin/accounts');

        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            return Router::redirect('/admin/accounts');
        }
    }

    public function deleteAccounts(array $users)
    {
        foreach ($users as $userId) {
            try {
                $dbDeleter = new Deleter();
                $deletedUser = $dbDeleter->deleteUser($userId);

                if (!$deletedUser) {
                    $this->flash('fail',
                        "Could not delete user <b>$userId</b>, try again"
                    );
                }

                $name = $deletedUser->getUsername();
                $this->flash('success', "User <b>$name</b> deleted");
            } catch (\Exception $e) {
                // TODO logError($e);
                // throw $e;
                $this->flash('fail', 'Database operation failed');
            }
        }
        return Router::redirect('/admin/accounts');
    }
}
