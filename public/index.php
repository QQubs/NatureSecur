<?php
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'create_request':
        require_once __DIR__ . '/../app/controllers/RequestController.php';
        RequestController::create();
        break;
    default:
        header('Location: ../Program/index.html');
        break;
}
