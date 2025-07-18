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
    case 'new_order':
        require_once __DIR__ . '/../app/controllers/OrderController.php';
        OrderController::create();
        break;
    case 'update_status':
        require_once __DIR__ . '/../app/controllers/OrderController.php';
        OrderController::updateStatus();
        break;
    case 'add_report':
        require_once __DIR__ . '/../app/controllers/OrderController.php';
        OrderController::addReport();
        break;
    case 'add_client':
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        AdminController::addClient();
        break;
    case 'add_employee':
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        AdminController::addEmployee();
        break;
    case 'update_employee':
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        AdminController::updateEmployee();
        break;
    case 'update_order':
        require_once __DIR__ . '/../app/controllers/AdminController.php';
        AdminController::updateOrder();
        break;
    default:
        header('Location: ../Program/index.php');
        break;
}
