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
        $sql = "SELECT o.order_id, o.order_type, o.status,
                       (SELECT file_path FROM reports r WHERE r.order_id = o.order_id ORDER BY version DESC LIMIT 1) AS file_path
                FROM orders o
                WHERE o.client_id = :client_id
                ORDER BY o.order_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOrdersByEmployee($empId) {
        $db = self::getDB();
        $sql = "SELECT o.order_id, o.order_type, o.status,
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
}
?>
