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

        $this->setTemplate('admin_payments.twig');
        $this->addTwigVar('payments', $payments);
        $this->render();
    }

    public function showPaymentPage(int $paymentId)
    {
        try {
            $dbReader = new Reader();
            $payment = $dbReader->getPaymentById($paymentId);
        } catch (\Exception $e) {
            Logger::log('db', 'error',
                "Failed to get payment by id $paymentId", $e
            );
            $this->flash('fail', 'Database operation failed');
            return $this->showPaymentListPage();
        }

        if (empty($payment)) {
            $this->flash('fail', "Could not find this payment - $paymentId");
            return $this->showPaymentListPage();
        }

        $this->setTemplate('payment.twig');
        $this->addTwigVar('payment', $payment);
        $this->render();
    }

    public function showAddPaymentPage()
    {
        $this->setTemplate('add_payment.twig');
        $this->render();
    }

    public function addPayment(array $post)
    {
        if (!$this->isClean($post)) {
            $this->flash('fail', 'Input contains invalid characters');
            return $this->showAddPaymentPage();
        }

        $illnessId = $post['illnessId'];
        $name = $post['name'];
        $cost = $post['cost'];
        $number = $post['number'];

        try {
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
            $dbCreator = new Creator();
        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to open connection', $e);
            $this->flash('fail', 'Database connection failed');
            return Router::redirect('/admin/payments');
        }

        $good = [];
        $bad = [];

        foreach ($payments as $payment) {
            if (empty($ill['illnessId'])
                || empty($ill['name'])
                || empty($ill['cost'])
                || empty($ill['number'])
            ) {
                $this->flash('fail', 'Some data is wrong or missing');
                return Router::redirect('/admin/payments');
            }

            if (!$this->isClean($payment)) {
                $bad['data'][] = $payment;
                continue;
            }

            try {
                $newPayment = $dbCreator->createPayment(
                    $payment['illnessId'],
                    $payment['name'],
                    $payment['cost'],
                    $payment['number']
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

        $this->prepareGoodBatchResults($good, $payments, ['illnessId', 'name']);
        $this->prepareBadBatchResults($bad, $payments, ['illnessId', 'name']);

        return Router::redirect('/admin/payments');
    }

    public function showChangePaymentPage(string $paymentId)
    {
        try {
            $dbReader = new Reader();
            $payment = $dbReader->findPaymentById($paymentId);
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
        $this->addTwigVar('payment', $payment);
        $this->render();
    }

    public function changePayment(string $paymentId, array $post)
    {
        if (!$this->isClean($post)) {
            $this->flash('fail', 'Input contains invalid characters');
            return $this->showChangePaymentPage($paymentId);
        }

        $illnessId = $post['illnessId'];
        $name = $post['name'];
        $cost = $post['cost'];
        $number = $post['number'];

        try {
            $oldPayment = $dbReader->findPaymentById($paymentId);
            if (empty($oldPayment)) {
                $this->flash('fail', 'Some problem occurred, please try again');
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
