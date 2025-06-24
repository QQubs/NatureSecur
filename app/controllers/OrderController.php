<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/ChangeLogModel.php';

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
        ChangeLogModel::addLog($orderId, $_SESSION['emp_id'], 'создание', 'Создан заказ');

        $client = ClientModel::getClientById($clientId);
        if ($client && !empty($client['email'])) {
            $subject = 'Создан новый заказ';
            $message = 'Для вас создан заказ №' . $orderId . '.';
            @mail($client['email'], $subject, $message);
        }

        header('Location: profile-employee.php?success=created');
        exit;
    }

    public static function updateStatus()
    {
        session_start();
        if (!isset($_SESSION['emp_id'])) {
            header('Location: ../Program/auth.html');
            exit;
        }

        $orderId = intval($_POST['order_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $allowed = ['принят', 'в работе', 'на проверке', 'завершен'];
        if ($orderId <= 0 || !in_array($status, $allowed, true)) {
            http_response_code(400);
            echo 'invalid';
            return;
        }

        $oldStatus = OrderModel::getStatus($orderId);
        if (!OrderModel::updateStatus($orderId, $status)) {
            http_response_code(400);
            echo 'invalid';
            return;
        }
        ChangeLogModel::addLog($orderId, $_SESSION['emp_id'], 'изменение статуса', 'Статус изменен', $oldStatus, $status);

        echo 'ok';
    }

    public static function addReport()
    {
        session_start();
        if (!isset($_SESSION['emp_id'])) {
            header('Location: ../Program/auth.html');
            exit;
        }

        $orderId = intval($_POST['order_id'] ?? 0);
        if ($orderId <= 0 || !isset($_FILES['report_file'])) {
            header('Location: profile-employee.php?error=invalid');
            exit;
        }

        $file = $_FILES['report_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            header('Location: profile-employee.php?error=upload');
            exit;
        }
        if ($file['size'] > 20 * 1024 * 1024) {
            header('Location: profile-employee.php?error=size');
            exit;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if ($mime !== 'application/pdf') {
            header('Location: profile-employee.php?error=type');
            exit;
        }

        $dir = __DIR__ . '/../../uploads';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = uniqid('report_', true) . '.pdf';
        $dest = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            header('Location: profile-employee.php?error=save');
            exit;
        }

        require_once __DIR__ . '/../models/ReportModel.php';
        $path = '../uploads/' . $filename;
        ReportModel::addReport($orderId, $_SESSION['emp_id'], $path);
        ChangeLogModel::addLog($orderId, $_SESSION['emp_id'], 'отчет', 'Загружен отчет', null, $path);

        OrderModel::updateStatus($orderId, 'на проверке');

        header('Location: profile-employee.php?success=uploaded');
        exit;
    }
}
?>
