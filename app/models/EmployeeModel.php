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

    public static function authenticate($login, $password) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT emp_id, role, password FROM employees WHERE login = :login");
        $stmt->bindParam(':login', $login);
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

    public static function getAllEmployees() {
        $db = self::getDB();
        $stmt = $db->query("SELECT emp_id, first_name, second_name, role, email, phone, login FROM employees ORDER BY emp_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createEmployee($firstName, $secondName, $login, $email, $phone, $role, $password) {
        $db = self::getDB();
        $stmt = $db->prepare("INSERT INTO employees (first_name, second_name, role, email, phone, login, password) VALUES (:fn, :sn, :role, :email, :phone, :login, :password)");
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':fn', $firstName);
        $stmt->bindParam(':sn', $secondName);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $hashed);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public static function updateEmployee($id, $firstName, $secondName, $login, $email, $phone, $role) {
        $db = self::getDB();
        $stmt = $db->prepare("UPDATE employees SET first_name = :fn, second_name = :sn, role = :role, email = :email, phone = :phone, login = :login WHERE emp_id = :id");
        $stmt->bindParam(':fn', $firstName);
        $stmt->bindParam(':sn', $secondName);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function deleteEmployee($id) {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM employees WHERE emp_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
