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
}
?>
