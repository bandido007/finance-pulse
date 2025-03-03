<?php
class User {
    /*
    TODO 3: User is a model/ entity in the application, it should not be responsible for handling the connection.
    It should only be responsible for handling user data.
    */
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register a new user
// models/User.php
    public function register($username, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Return the new user's ID
        }
        return false;
    }

    // Login user
    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
        // TODO 4: I suspect  this is a dead code  as it won't be executed after function has returned.
        if (session_status() == PHP_SESSION_NONE) {
          session_start();
        }

    }
}
?>
