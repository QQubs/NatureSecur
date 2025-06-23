<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ClientModel.php';

class OrderController
{
    public static function create()
    {
        session_start();
        if (!isset($_SESSION['emp_id'])) {
            header('Location: ../Program/auth.html');
            exit;
        }

        $clientId = intval($_POST['client_id'] ?? 0);
        $orderType = trim($_POST['order_type'] ?? '');
        $deadline = trim($_POST['deadline'] ?? '');
        if ($clientId <= 0 || $orderType === '') {
            header('Location: profile-employee.php?error=invalid');
            exit;
        }

        $dl = $deadline !== '' ? $deadline : null;
        $orderId = OrderModel::createOrder($clientId, $_SESSION['emp_id'], $orderType, $dl);

        $client = ClientModel::getClientById($clientId);
        if ($client && !empty($client['email'])) {
            $subject = 'Создан новый заказ';
            $message = 'Для вас создан заказ №' . $orderId . '.';
            @mail($client['email'], $subject, $message);
        }

        header('Location: profile-employee.php?success=created');
        exit;
    }
}
?>
