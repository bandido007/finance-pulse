<?php
class Database {
    /**
    * TODO 1: Consider externalising  the database configuration to a separate file
    */
    private $host = "localhost";
    private $db_name = "finance";
    private $username = "tostao";
    private $password = "123";
    private $conn;

    public function __construct() {
        $this->connect();
    }

     /*
      TODO 2: Check more on how you can utilise connection pooling, not sure if PDO supports it.
      With pooling you can reuse connections instead of creating a new one each time and specify a number of standby connection.
      this will significantly improve the performance of your application.
      https://www.php.net/manual/en/mysqli.quickstart.connections.php
      https://learn.microsoft.com/en-us/sql/connect/php/connection-options?view=sql-server-ver16
      https://www.php.net/manual/en/features.persistent-connections.php
     */
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
