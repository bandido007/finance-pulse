<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="/css/style.css">
    <!-- Add Poppins font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
            <p>Manage your expenses and funds efficiently</p>
        </div>
        
        <div class="nav-grid">
            <!-- Expenses Card -->
            <div class="action-card expenses-card">
                <h3 class="card-header">Expenses</h3>
                <div class="button-group">
                    <a href="/expenses/add" class="btn-primary">Add Expense</a>
                    <a href="/expenses/view" class="btn-secondary">View Expenses</a>
                </div>
            </div>

            <!-- Funds Card -->
            <div class="action-card funds-card">
                <h3 class="card-header">Funds</h3>
                <div class="button-group">
                    <a href="/funds/add" class="btn-primary">Add New Fund</a>
                    <a href="/funds/view" class="btn-secondary">View Funds</a>
                </div>
            </div>

            <!-- Account Card -->
            <div class="action-card account-card">
                <h3 class="card-header">Account</h3>
                <div class="button-group">
                    <a href="/logout" class="btn-secondary btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>