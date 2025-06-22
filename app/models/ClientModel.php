<?php
class ClientModel {
    protected static function getDB() {
        static $db = null;
        if ($db === null) {
            $dsn = getenv('DB_DSN') ?: 'pgsql:host=localhost;port=5432;dbname=NatureSecur;charset=utf8';
            $user = getenv('DB_USER') ?: 'postgres';
            $pass = getenv('DB_PASS') ?: 'ristal2222';
            $db = new PDO($dsn, $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }

    public static function createClient($name, $companyName, $email, $phone, $password) {
        $db = self::getDB();
        $stmt = $db->prepare("INSERT INTO clients (name, company_name, email, phone, password) VALUES (:name, :company, :email, :phone, :password)");
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':company', $companyName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $hashed);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public static function authenticate($email, $password) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT client_id, password FROM clients WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($client && password_verify($password, $client['password'])) {
            return $client['client_id'];
        }
        return false;
    }
}
?>
