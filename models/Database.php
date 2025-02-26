<?php
class Database {
    private $host = "localhost";
    private $db_name = "finance";
    private $username = "tostao";
    private $password = "123";
    private $conn;

    public function __construct() {
        $this->connect();
    }
    
    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "pgsql:host={$this->host};dbname={$this->db_name}", 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
        return $this->conn;
    }

    // Add this new method to get the connection
    public function getConnection() {
        return $this->conn;
    }

    // Existing methods remain unchanged
    public function fetchAll($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function fetchOne($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function execute($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
    
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    public function commit() {
        return $this->conn->commit();
    }
    
    public function rollBack() {
        return $this->conn->rollBack();
    }


}
?>