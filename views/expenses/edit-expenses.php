<!DOCTYPE html>
<html>
<head>
    <title>Edit Expense</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container">
        <?php include '../components/alert.php'; ?>
        <h1>Edit Expense</h1>
        <?php 
        $validCategories = [
            'Food', 'Transport', 'Utilities', 'Medical & Health',
            'Entertainment', 'Shopping', 'Gifts & Donations', 
            'Dates & Relationship', 'Helping Friends & Family', 'Subscriptions',
            'Savings', 'Investments', 'Debt Payments', 'Insurance',
            'Education', 'Work Expenses'
        ];
        ?>
        <form method="POST" action="/expenses/update">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="expense_id" value="<?= htmlspecialchars($expense['id']) ?>">
            <input type="hidden" name="original_fund_id" value="<?= $expense['fund_id'] ?>">
            <input type="hidden" name="original_amount" value="<?= $expense['amount'] ?>">
            
            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="date" value="<?= htmlspecialchars($expense['date']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Category:</label>
                <select name="category" required>
                    <?php foreach ($validCategories as $category): ?>
                        <option value="<?= $category ?>" 
                            <?= $category === $expense['category'] ? 'selected' : '' ?>>
                            <?= $category ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Amount (TZS):</label>
                <input type="number" name="amount" step="0.01" value="<?= htmlspecialchars($expense['amount']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($expense['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Fund Source:</label>
                <select name="fund_id">
                    <option value="">-- Select Fund Source --</option>
                    <?php foreach ($availableFunds as $fund): ?>
                    <option value="<?= $fund['id'] ?>"
                        <?= $fund['id'] == ($expense['fund_id'] ?? '') ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fund['source']) ?>
                        (TZS <?= number_format($fund['remaining'], 2) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn-primary">Update Expense</button>
            <a href="/expenses/view" class="btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>