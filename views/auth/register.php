<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Register</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="/auth" method="POST">
            <input type="text" name="username" placeholder="Username" required> <!-- Add this -->
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="/login.php">Login here</a>.</p>
    </div>
</body>
</html>