<?php
require_once __DIR__ . '/../models/RequestModel.php';
require_once __DIR__ . '/../models/ChangeLogModel.php';

class RequestController
{
    public static function create()
    {
        session_start();

        if (!isset($_SESSION['client_id'])) {
            header('Location: profile-client.php?error=auth');
            exit;
        }

        $clientId = $_SESSION['client_id'];
        $orderType = trim($_POST['work_type'] ?? '');
        if ($orderType === '') {
            header('Location: profile-client.php?error=invalid');
            exit;
        }

        RequestModel::createRequest($clientId, $orderType);

        header('Location: profile-client.php?success=1');
        exit;
    }
     public static function decline()
    {
        session_start();
        if (!isset($_SESSION['emp_id'])) {
            header('Location: ../Program/auth.html');
            exit;
        }

        $requestId = intval($_POST['request_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        if ($requestId <= 0 || $reason === '') {
            header('Location: profile-employee.php?error=invalid');
            exit;
        }

        $req = RequestModel::getRequestById($requestId);
        if (!$req) {
            header('Location: profile-employee.php?error=notfound');
            exit;
        }

        require_once __DIR__ . '/../models/ClientModel.php';
        $client = ClientModel::getClientById($req['client_id']);
        if ($client && !empty($client['email'])) {
            $subject = 'Заявка отклонена';
            $message = 'Ваша заявка №' . $requestId . " отклонена.\nПричина: " . $reason;
            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $headers = [
                'From: no-reply@naturesecur.local',
                'Content-Type: text/plain; charset=UTF-8'
            ];
            @mail($client['email'], $encodedSubject, $message, implode("\r\n", $headers));
        }

        RequestModel::deleteRequest($requestId);
        header('Location: profile-employee.php?success=declined');
        exit;
    }

    public static function createOrder()
    {
        session_start();
        if (!isset($_SESSION['emp_id'])) {
            header('Location: ../Program/auth.html');
            exit;
        }

        $requestId = intval($_POST['request_id'] ?? 0);
        $deadline = trim($_POST['deadline'] ?? '');
        if ($requestId <= 0) {
            header('Location: profile-employee.php?error=invalid');
            exit;
        }

        $req = RequestModel::getRequestById($requestId);
        if (!$req) {
            header('Location: profile-employee.php?error=notfound');
            exit;
        }

        require_once __DIR__ . '/../models/OrderModel.php';
        $dl = $deadline !== '' ? $deadline : null;
        $orderId = OrderModel::createOrder($req['client_id'], $_SESSION['emp_id'], $req['order_type'], $dl);
        ChangeLogModel::addLog($orderId, $_SESSION['emp_id'], 'создание', 'Создан заказ из заявки ' . $requestId);

        require_once __DIR__ . '/../models/ClientModel.php';
        $client = ClientModel::getClientById($req['client_id']);
        if ($client && !empty($client['email'])) {
            $subject = 'Заявка принята';
            $message = 'Ваша заявка №' . $requestId . ' преобразована в заказ №' . $orderId . '.';
            @mail($client['email'], $subject, $message);
        }

        RequestModel::deleteRequest($requestId);
        header('Location: profile-employee.php?success=created');
        exit;
    }
}
?>
