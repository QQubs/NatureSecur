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
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $clientId = ClientModel::authenticate($email, $password);
        if ($clientId) {
            session_start();
            $_SESSION['client_id'] = $clientId;
            header('Location: profile-client.php');
            exit;
        }

        $empId = EmployeeModel::authenticate($email, $password);
        if ($empId) {
            session_start();
            $_SESSION['emp_id'] = $empId;
            header('Location: profile-employee.php');
            exit;
        }

        header('Location: ../Program/auth.html?error=invalid');
        exit;
    }
}
?>
