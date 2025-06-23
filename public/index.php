<?php
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'register':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        AuthController::register();
        break;
    case 'login':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        AuthController::login();
        break;
    case 'create_request':
        require_once __DIR__ . '/../app/controllers/RequestController.php';
        RequestController::create();
        break;
    case 'decline_request':
        require_once __DIR__ . '/../app/controllers/RequestController.php';
        RequestController::decline();
        break;
    case 'create_order':
        require_once __DIR__ . '/../app/controllers/RequestController.php';
        RequestController::createOrder();
        break;
    default:
        header('Location: ../Program/index.php');
        break;
}
