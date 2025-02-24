<?php if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
} ?>
<nav class="main-nav">
    <div class="nav-content">
        <a href="/dashboard" class="nav-item">Dashboard</a>
        <a href="/expenses/add" class="nav-item">Add Expense</a>
        <a href="/expenses/view" class="nav-item">View Expenses</a>
        <a href="/logout" class="nav-item logout">Logout</a>
    </div>
</nav>