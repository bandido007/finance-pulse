<?php
// First, let's include the necessary files with absolute paths
require_once __DIR__ . '/../models/Database.php';  // Note the lowercase 'database.php'
require_once __DIR__ . '/../models/User.php';

// Start the session if it hasn't been started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection and user model
$database = new Database();
$user = new User($database->connect());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $username = ($_POST['username']);
        $password = ($_POST['password']);
        
        $user_id = $user->register($username, $password);
        if ($user_id) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            header("Location: /dashboard");
            exit();
        } else {
            header("Location: /register?error=Registration failed");
            exit();
        }
    } elseif (isset($_POST['login'])) {
        $username = ($_POST['username']);
        $password = ($_POST['password']);
        
        $loggedInUser = $user->login($username, $password);
        if ($loggedInUser) {
            $_SESSION['user_id'] = $loggedInUser['id'];
            $_SESSION['username'] = $loggedInUser['username'];
            header("Location: /dashboard");
            exit();
        } else {
            header("Location: /login?error=Invalid credentials");
            exit();
        }
    }
}