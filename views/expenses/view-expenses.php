<?php
error_log("View received processedData: " . print_r($processedData ?? [], true));
error_log("View received grandTotal: " . ($grandTotal ?? 'not set'));
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Expenses</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        /* ... (keep your existing styles) ... */
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>
    <div class="container">
        <?php include __DIR__ . '/../components/alert.php'; ?>
        <h1>Expense Overview</h1>
        
        <?php 
        // Initialize variables with defaults
        $processedData = $processedData ?? [];
        $grandTotal = $grandTotal ?? 0;
        ?>
        
        <?php if (!empty($processedData)): ?>
        <table class="expense-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Amount (TZS)</th>
                    <th>Description</th>
                    <th>Fund Source</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($processedData as $group): 
                    $groupDate = $group['date'] ?? '';
                    $dailyTotal = $group['daily_total'] ?? 0;
                    $expenses = $group['expenses'] ?? [];
                ?>
                <tr class="daily-header">
                    <td colspan="6">
                        <strong><?= date('F j, Y', strtotime($groupDate)) ?></strong>
                        <span class="daily-total">Daily Total: TZS <?= number_format($dailyTotal, 2) ?></span>
                    </td>
                </tr>
                <?php if (!empty($expenses)): ?>
                    <?php foreach ($expenses as $expense): 
                        $expenseId = $expense['id'] ?? 0;
                    ?>
                    <tr>
                        <td><?= date('H:i', strtotime($expense['date'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($expense['category'] ?? '') ?></td>
                        <td>TZS <?= number_format($expense['amount'] ?? 0, 2) ?></td>
                        <td><?= htmlspecialchars($expense['description'] ?? '') ?></td>
                        <td><?= !empty($expense['fund_source']) ? htmlspecialchars($expense['fund_source']) : 'None' ?></td>
                        <td>
                            <a href="/expenses/edit?id=<?= $expenseId ?>" class="btn-edit">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php endforeach; ?>
                <tr class="total-row grand-total">
                    <td colspan="2">Grand Total</td>
                    <td>TZS <?= number_format($grandTotal, 2) ?></td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-expenses">
            <p>No expenses found. <a href="/expenses/add">Add your first expense</a></p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>


<?php if (isset($_SESSION['success'])): ?>
    <div class="success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>