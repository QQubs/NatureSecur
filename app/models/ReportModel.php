<?php
class ReportModel {
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

    public static function addReport($orderId, $empId, $filePath) {
        $db = self::getDB();
        $stmt = $db->prepare('SELECT COALESCE(MAX(version),0) + 1 FROM reports WHERE order_id = :order_id');
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $version = $stmt->fetchColumn();

        $stmt = $db->prepare('INSERT INTO reports (order_id, emp_id, file_path, version) VALUES (:order_id, :emp_id, :file_path, :version)');
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindParam(':emp_id', $empId, PDO::PARAM_INT);
        $stmt->bindParam(':file_path', $filePath);
        $stmt->bindParam(':version', $version, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
