<?php
session_start();



// Retrieve stored form data
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// Set default values if not available
$formData['date'] = $formData['date'] ?? '';
$formData['category'] = $formData['category'] ?? '';
$formData['amount'] = $formData['amount'] ?? '';
$formData['description'] = $formData['description'] ?? '';
$formData['fund_id'] = $formData['fund_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        <?php include __DIR__ . '/../components/alert.php'; ?>
        
        <h2>Add New Expense</h2>
        
        <form action="/expenses/handle" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($formData['date']) ?>" required>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="" disabled selected>Select a category</option>
                    <optgroup label="Essentials">
                        <option value="Food" <?= $formData['category'] === 'Food' ? 'selected' : '' ?>>Food (e.g., ugali, rice)</option>
                        <option value="Transport" <?= $formData['category'] === 'Transport' ? 'selected' : '' ?>>Transport (e.g., bus fare)</option>
                        <option value="Utilities" <?= $formData['category'] === 'Utilities' ? 'selected' : '' ?>>Utilities (e.g., electricity)</option>
                        <option value="Medical & Health" <?= $formData['category'] === 'Medical & Health' ? 'selected' : '' ?>>Medical & Health (e.g., clinic)</option>
                    </optgroup>
                    <optgroup label="Lifestyle & Social">
                        <option value="Entertainment" <?= $formData['category'] === 'Entertainment' ? 'selected' : '' ?>>Entertainment (e.g., movies)</option>
                        <option value="Shopping" <?= $formData['category'] === 'Shopping' ? 'selected' : '' ?>>Shopping (e.g., clothes)</option>
                        <option value="Gifts & Donations" <?= $formData['category'] === 'Gifts & Donations' ? 'selected' : '' ?>>Gifts & Donations</option>
                        <option value="Dates & Relationship" <?= $formData['category'] === 'Dates & Relationship' ? 'selected' : '' ?>>Dates & Relationship</option>
                        <option value="Helping Friends & Family" <?= $formData['category'] === 'Helping Friends & Family' ? 'selected' : '' ?>>Helping Friends & Family</option>
                        <option value="Subscriptions" <?= $formData['category'] === 'Subscriptions' ? 'selected' : '' ?>>Subscriptions (e.g., streaming)</option>
                    </optgroup>
                    <optgroup label="Financial">
                        <option value="Savings" <?= $formData['category'] === 'Savings' ? 'selected' : '' ?>>Savings</option>
                        <option value="Investments" <?= $formData['category'] === 'Investments' ? 'selected' : '' ?>>Investments</option>
                        <option value="Debt Payments" <?= $formData['category'] === 'Debt Payments' ? 'selected' : '' ?>>Debt Payments</option>
                        <option value="Insurance" <?= $formData['category'] === 'Insurance' ? 'selected' : '' ?>>Insurance</option>
                    </optgroup>
                    <optgroup label="Education & Career">
                        <option value="Education" <?= $formData['category'] === 'Education' ? 'selected' : '' ?>>Education (e.g., books)</option>
                        <option value="Work Expenses" <?= $formData['category'] === 'Work Expenses' ? 'selected' : '' ?>>Work Expenses (e.g., uniforms)</option>
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label for="amount">Amount (TZS):</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0" 
                       value="<?= htmlspecialchars($formData['amount']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="3"><?= htmlspecialchars($formData['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="fund_id">Fund Source:</label>
                <select id="fund_id" name="fund_id">
                    <option value="">None</option>
                    <?php foreach ($availableFunds as $fund): ?>
                        <option value="<?= htmlspecialchars((string)$fund['id']) ?>" 
                            <?= $formData['fund_id'] == $fund['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($fund['source']) ?> (Remaining: <?= number_format($fund['remaining'], 2) ?> TZS)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">Add Expense</button>
        </form>

        <a href="/expenses/view" class="nav-link">View Expenses</a>
    </div>
</body>
</html>