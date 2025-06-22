<?php
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';

class AuthController {
    public static function register() {
        $name = trim($_POST['fio'] ?? '');
        $company = trim($_POST['companyName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            header('Location: ../Program/auth.html?error=invalid');
            exit;
        }
        try {
            ClientModel::createClient($name, $company, $email, $phone, $password);
            header('Location: ../Program/auth.html?registered=1');
        } catch (Exception $e) {
            header('Location: ../Program/auth.html?error=exists');
        }
        exit;
    }

    public static function login() {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';
        $clientId = ClientModel::authenticate($identifier, $password);
        if ($clientId) {
            session_start();
            $_SESSION['client_id'] = $clientId;
            header('Location: profile-client.php');
            exit;
        }

        $emp = EmployeeModel::authenticate($identifier, $password);
        if ($emp) {
            session_start();
            $_SESSION['emp_id'] = $emp['emp_id'];
            $_SESSION['role'] = $emp['role'];
            if ($emp['role'] === 'администратор') {
                header('Location: profile-admin.php');
            } else {
                header('Location: profile-employee.php');
            }
            exit;
        }

        header('Location: ../Program/auth.html?error=invalid');
        exit;
    }
}
?>
