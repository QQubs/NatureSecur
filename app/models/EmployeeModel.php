<?php
class EmployeeModel {
    protected static function getDB() {
        static $db = null;
        if ($db === null) {
            $dsn = getenv('DB_DSN') ?: 'pgsql:host=localhost;port=5432;dbname=NatureSecur';
            $user = getenv('DB_USER') ?: 'postgres';
            $pass = getenv('DB_PASS') ?: 'ristal2222';
            $db = new PDO($dsn, $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }

    public static function authenticate($email, $password) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT emp_id, role, password FROM employees WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($emp && password_verify($password, $emp['password'])) {
            return ['emp_id' => $emp['emp_id'], 'role' => $emp['role']];
        }
        return false;
    }

    public static function getEmployeeById($id) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT first_name, second_name, role, email, phone FROM employees WHERE emp_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
