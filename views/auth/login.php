<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Login</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <form action="/auth" method="POST">
    <input type="text" name="username" placeholder="Username" required> <!-- Add this -->
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="/register">Register here</a>.</p>
    </div>
</body>
</html>