<?php
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

class AdminController
{
    public static function addEmployee()
    {
        session_start();
        if (!self::checkRole()) return;

        $first = trim($_POST['first_name'] ?? '');
        $second = trim($_POST['second_name'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = 'сотрудник';
        $password = $_POST['password'] ?? '';
        if ($first === '' || $second === '' || $login === '' || $email === '' || $password === '') {
            header('Location: profile-admin.php?error=invalid');
            exit;
        }
        EmployeeModel::createEmployee($first, $second, $login, $email, $phone, $role, $password);
        header('Location: profile-admin.php?success=emp_added');
        exit;
    }

    public static function updateEmployee()
    {
        session_start();
        if (!self::checkRole()) return;

        $id = intval($_POST['emp_id'] ?? 0);
        $first = trim($_POST['first_name'] ?? '');
        $second = trim($_POST['second_name'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = trim($_POST['role'] ?? '');
        if ($id <= 0) {
            header('Location: profile-admin.php?error=invalid');
            exit;
        }
        if (isset($_POST['delete'])) {
            EmployeeModel::deleteEmployee($id);
            header('Location: profile-admin.php?success=emp_deleted');
            exit;
        }
        EmployeeModel::updateEmployee($id, $first, $second, $login, $email, $phone, $role);
        header('Location: profile-admin.php?success=emp_updated');
        exit;
    }

    public static function updateOrder()
    {
        session_start();
        if (!self::checkRole()) return;

        $id = intval($_POST['order_id'] ?? 0);
        $clientId = intval($_POST['client_id'] ?? 0);
        $empId = intval($_POST['emp_id'] ?? 0);
        $orderType = trim($_POST['order_type'] ?? '');
        $deadline = trim($_POST['deadline'] ?? '');
        $status = trim($_POST['status'] ?? '');
        if ($id <= 0) {
            header('Location: profile-admin.php?error=invalid');
            exit;
        }
        if (isset($_POST['delete'])) {
            OrderModel::deleteOrder($id);
            header('Location: profile-admin.php?success=order_deleted');
            exit;
        }
        OrderModel::updateOrder($id, $clientId, $empId, $orderType, $deadline, $status);
        header('Location: profile-admin.php?success=order_updated');
        exit;
    }

    public static function addClient()
    {
        session_start();
        if (!self::checkRole()) return;

        $name = trim($_POST['name'] ?? '');
        $company = trim($_POST['company_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email === '' || $password === '') {
            header('Location: profile-admin.php?error=invalid');
            exit;
        }
        require_once __DIR__ . '/../models/ClientModel.php';
        ClientModel::createClient($name, $company, $email, $phone, $password);
        header('Location: profile-admin.php?success=client_added');
        exit;
    }

    protected static function checkRole()
    {
        if (!isset($_SESSION['emp_id']) || ($_SESSION['role'] ?? '') !== 'администратор') {
            header('Location: ../Program/auth.html');
            exit;
        }
        return true;
    }
}
?>
