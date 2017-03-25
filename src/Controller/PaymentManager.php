<?php
namespace VVC\Controller;

use VVC\Model\Database\Creator;
use VVC\Model\Database\Deleter;
use VVC\Model\Database\Reader;
use VVC\Model\Database\Updater;

/**
 * Admin controller for managing payments
 */
class PaymentManager extends AdminController
{
    public function showPaymentListPage()
    {
        try {
            $dbReader = new Reader();
            $payments = $dbReader->getAllPayments();
        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to get all payments', $e);
            $this->flash('fail', 'Database operation failed');
            return $this->showDashboardPage();
        }

        $ymls = Uploader::getFiles(YML_DIRECTORY, ['yml']);
        $this->addTwigVar('files', $ymls);

        $this->setTemplate('admin_pays.twig');
        $this->addTwigVar('payments', $payments);
        $this->render();
    }

    public function showAddPaymentPage()
    {
        try {
            $dbReader = new Reader();
            $illnesses = $dbReader->getAllIllnesses();
        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to get all illnesses', $e);
            $this->flash('fail', 'Database operation failed');
            return $this->showPaymentListPage();
        }

        $this->addTwigVar('illnesses', $illnesses->getJustIllnesses());
        $this->setTemplate('add_payment.twig');
        $this->render();
    }

    public function addPayment(array $post)
    {
        if (!$this->isClean($post)) {
            $this->flash('fail', 'Input contains invalid characters');
            return $this->showAddPaymentPage();
        }

        $illnessName = $post['illness'];
        $name = $post['name'];
        $cost = $post['cost'];
        $number = $post['number'];

        try {
            $dbReader = new Reader();
            $illnessId = $dbReader->findIllnessIdByName($illnessName);

            if (!$illnessId) {
                $this->flash('fail', "Illness name $illnessName does not exist");
                return $this->showAddPaymentPage();
            }

            $dbCreator = new Creator();
            $payment = $dbCreator->createPayment(
                $illnessId, $name, $cost, $number
            );

            $this->flash('success', "$name added successfully");
            return Router::redirect('/admin/payments');

        } catch (\Exception $e) {
            Logger::log('db', 'error', "Failed to create payment $name (single)", $e);
            $this->flash('fail', 'Database operation failed');
            return $this->showAddPaymentPage();
        }
    }

    public function batchAddPayments(array $payments)
    {
        try {
            $dbReader = new Reader();
            $dbCreator = new Creator();
        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to open connection', $e);
            $this->flash('fail', 'Database connection failed');
            return Router::redirect('/admin/payments');
        }

        $good = [];
        $bad = [];

        foreach ($payments as $payment) {
            if (empty($payment['illnessName'])
                || empty($payment['name'])
            ) {
                $this->flash('fail', 'Some data is wrong or missing');
                return Router::redirect('/admin/payments');
            }

            if (!$this->isClean($payment)) {
                $bad['data'][] = $payment;
                continue;
            }

            $name = $payment['name'];
            $illnessName = $payment['illnessName'];
            $cost = $payment['cost'];
            $number = $payment['number'];

            try {
                $illnessId = $dbReader->findIllnessIdByName($illnessName);

                if (!$illnessId) {
                    $this->flash('fail', "Illness name $illnessName does not exist");
                    return Router::redirect('/admin/payments');
                }

                $newPayment = $dbCreator->createPayment(
                    $illnessId,
                    $name,
                    $cost,
                    $number
                );

                $good[] = $newPayment;

            } catch (\Exception $e) {
                Logger::log(
                    'db', 'error',
                    "Failed to create payment {$payment['name']} (batch)",
                    $e
                );
                $bad['db'][] = $payment;
                continue;
            }
        }

        $this->prepareGoodBatchResults($good, $payments, ['name', 'illnessName']);
        $this->prepareBadBatchResults($bad, $payments, ['name', 'illnessName']);

        return Router::redirect('/admin/payments');
    }

    public function showChangePaymentPage(string $paymentId)
    {
        try {
            $dbReader = new Reader();
            $payment = $dbReader->findPaymentById($paymentId);
            $illnesses = $dbReader->getAllIllnesses();
        } catch (\Exception $e) {
            Logger::log('db', 'error', "Failed to find payment by id $paymentId", $e);
            $this->flash('fail', 'Database operation failed');
            Router::redirect('/admin/payments');
        }

        if (empty($payment)) {
            $this->flash('fail', "Payment $paymentId not found");
            Router::redirect('/admin/payments');
        }

        $this->setTemplate('change_payment.twig');
        $this->addTwigVar('illnesses', $illnesses->getJustIllnesses());
        $this->addTwigVar('pay', $payment);
        $this->render();
    }

    public function changePayment(string $paymentId, array $post)
    {
        if (!$this->isClean($post)) {
            $this->flash('fail', 'Input contains invalid characters');
            return $this->showChangePaymentPage($paymentId);
        }

        $illnessName = $post['illness'];
        $name = $post['name'];
        $cost = $post['cost'];
        $number = $post['number'];

        try {
            $dbReader = new Reader();
            $illnessId = $dbReader->findIllnessIdByName($illnessName);

            if (!$illnessId) {
                $this->flash('fail', "Illness name $illnessName does not exist");
                return $this->showChangePaymentPage($paymentId);
            }

            $dbUpdater = new Updater();
            $dbUpdater->updatePayment(
                $paymentId, $illnessId, $name, $cost, $number
            );

            $this->flash('success', "Payment $name updated");
            return Router::redirect('/admin/payments');

        } catch (\Exception $e) {
            Logger::log('db', 'error', "Failed to change payment $paymentId", $e);
            $this->flash('fail', 'Database operation failed');
            return $this->showChangePaymentPage($paymentId);
        }
    }

    public function deletePayment(string $paymentId)
    {
        try {
            $dbDeleter = new Deleter();
            $deleted = $dbDeleter->deletePayment($paymentId);
            if (!$deleted) {
                $this->flash('fail',
                    "Could not delete payment $paymentId, try again"
                );
                return Router::redirect('/admin/payments');
            }

            $this->flash('success', "Payment $paymentId deleted");
            return Router::redirect('/admin/payments');

        } catch (\Exception $e) {
            Logger::log('db', 'error',
                "Failed to delete payment $paymentId (single)", $e
            );
            $this->flash('fail', 'Database operation failed');
            return Router::redirect('/admin/payments');
        }
    }

    public function deletePayments(array $payments)
    {
        $good = [];
        $bad = [];

        foreach ($payments as $paymentId) {
            try {
                $dbDeleter = new Deleter();
                $deleted = $dbDeleter->deletePayment($paymentId);

                if (!$deleted) {
                    $bad['db'][] = $paymentId;
                } else {
                    $good[] = $deleted;
                }
            } catch (\Exception $e) {
                Logger::log('db', 'error',
                    "Failed to delete payment $paymentId (batch)", $e
                );
                $bad['db'][] = $paymentId;
            }
        }

        $this->prepareGoodBatchResults($good, $payments, ['id', 'name']);
        $this->prepareBadBatchResults($bad, $payments, ['id']);

        return Router::redirect('/admin/payments');
    }
}
