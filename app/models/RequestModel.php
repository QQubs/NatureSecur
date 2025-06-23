<?php
class RequestModel {
    protected static function getDB() {
        static $db = null;
        if ($db === null) {
            // Use PostgreSQL connection; charset is inherited from the database
            $dsn = getenv('DB_DSN') ?: 'pgsql:host=localhost;port=5432;dbname=NatureSecur';
            $user = getenv('DB_USER') ?: 'postgres';
            $pass = getenv('DB_PASS') ?: 'ristal2222';
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

    public static function getAllRequests() {
        $db = self::getDB();
        $sql = "SELECT r.request_id, r.request_date, r.order_type,
                       COALESCE(NULLIF(c.company_name, ''), c.name) AS client_name
                FROM requests r
                JOIN clients c ON r.client_id = c.client_id
                ORDER BY r.request_id";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
