<?php
require_once __DIR__ . '/../models/RequestModel.php';

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
}
?>
