<?php
class ChangeLogModel {
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

    public static function getAllLogs() {
        $db = self::getDB();
        $sql = "SELECT l.log_id, l.order_id, l.emp_id, l.change_date, l.action_type, l.description, l.old_value, l.new_value,
                       CONCAT(e.first_name, ' ', e.second_name) AS employee_name
                FROM changelog l
                LEFT JOIN employees e ON l.emp_id = e.emp_id
                ORDER BY l.log_id DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addLog($orderId, $empId, $actionType, $description, $oldValue = null, $newValue = null) {
        $db = self::getDB();
        $stmt = $db->prepare("INSERT INTO changelog (order_id, emp_id, action_type, description, old_value, new_value)
                              VALUES (:order_id, :emp_id, :action_type, :description, :old_value, :new_value)");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindParam(':emp_id', $empId, PDO::PARAM_INT);
        $stmt->bindParam(':action_type', $actionType);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':old_value', $oldValue);
        $stmt->bindParam(':new_value', $newValue);
        $stmt->execute();
    }
}
?>
