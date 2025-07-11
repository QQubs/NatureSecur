<?php
class OrderModel {
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

    public static function getOrdersByClient($clientId) {
        $db = self::getDB();
        $sql = "SELECT o.order_id, o.order_type, o.order_date, o.deadline, o.status,
                       CONCAT(e.first_name, ' ', e.second_name) AS employee_name,
                       (SELECT file_path FROM reports r WHERE r.order_id = o.order_id ORDER BY version DESC LIMIT 1) AS file_path
                FROM orders o
                LEFT JOIN employees e ON o.emp_id = e.emp_id
                WHERE o.client_id = :client_id
                ORDER BY o.order_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createOrder($clientId, $empId, $orderType, $deadline = null) {
        $db = self::getDB();
        $stmt = $db->prepare("INSERT INTO orders (client_id, emp_id, order_type, order_date, deadline, status) VALUES (:client_id, :emp_id, :order_type, CURRENT_DATE, :deadline, 'принят')");
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':emp_id', $empId, PDO::PARAM_INT);
        $stmt->bindParam(':order_type', $orderType);
        $stmt->bindParam(':deadline', $deadline);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public static function getOrdersByEmployee($empId) {
        $db = self::getDB();
        $sql = "SELECT o.order_id, o.order_type, o.order_date, o.deadline, o.status,
                       COALESCE(NULLIF(c.company_name, ''), c.name) AS client_name
                FROM orders o
                JOIN clients c ON o.client_id = c.client_id
                WHERE o.emp_id = :emp_id
                ORDER BY o.order_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':emp_id', $empId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStatus($orderId) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT status FROM orders WHERE order_id = :order_id");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function updateStatus($orderId, $status) {
        $current = self::getStatus($orderId);
        if ($current === false) {
            return false;
        }
        $allowed = ['принят', 'в работе', 'на проверке', 'завершен'];
        $curIndex = array_search($current, $allowed, true);
        $newIndex = array_search($status, $allowed, true);
        if ($curIndex === false || $newIndex === false || $newIndex < $curIndex) {
            return false;
        }

        $db = self::getDB();
        $stmt = $db->prepare("UPDATE orders SET status = :status WHERE order_id = :order_id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    }

    public static function getAllOrders() {
        $db = self::getDB();
        $sql = "SELECT o.order_id, o.order_type, o.order_date, o.deadline, o.status, o.client_id, o.emp_id,
                       COALESCE(NULLIF(c.company_name, ''), c.name) AS client_name,
                       CONCAT(e.first_name, ' ', e.second_name) AS employee_name
                FROM orders o
                LEFT JOIN clients c ON o.client_id = c.client_id
                LEFT JOIN employees e ON o.emp_id = e.emp_id
                ORDER BY o.order_id";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateOrder($id, $clientId, $empId, $orderType, $deadline, $status) {
        $db = self::getDB();
        $stmt = $db->prepare("UPDATE orders SET client_id = :client_id, emp_id = :emp_id, order_type = :order_type, deadline = :deadline, status = :status WHERE order_id = :id");
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':emp_id', $empId, PDO::PARAM_INT);
        $stmt->bindParam(':order_type', $orderType);
        $stmt->bindParam(':deadline', $deadline);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function deleteOrder($id) {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM orders WHERE order_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>

