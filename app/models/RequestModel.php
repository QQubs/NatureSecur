<?php
class RequestModel {
    protected static function getDB() {
        static $db = null;
        if ($db === null) {
            $dsn = getenv('DB_DSN') ?: 'mysql:host=localhost;dbname=naturesecur;charset=utf8';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            $db = new PDO($dsn, $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }

    public static function createRequest($clientId, $orderType) {
        $db = self::getDB();
        $stmt = $db->prepare("INSERT INTO requests (client_id, request_date, order_type) VALUES (:client_id, CURRENT_DATE, :order_type)");
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':order_type', $orderType);
        $stmt->execute();
        return $db->lastInsertId();
    }
}
?>
