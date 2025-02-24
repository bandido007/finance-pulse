<?php include __DIR__ . '/../components/header.php'; ?>

<div class="container fund-form-container">
    <h2>Add New Fund Source</h2>
    <form action="/funds/add" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div class="form-group">
            <label>Type:</label>
            <select name="type" required>
                <option value="aid">Financial Aid</option>
                <option value="saving">Personal Saving</option>
            </select>
        </div>

        <div class="form-group">
            <label>Source Name:</label>
            <input type="text" name="source" required 
                placeholder="e.g. Parent Support, Emergency Fund">
        </div>

        <div class="form-group">
            <label>Amount (TZS):</label>
            <input type="number" step="0.01" name="amount" required>
        </div>

        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" rows="3" placeholder="Brief description about the fund"></textarea>
        </div>

        <button type="submit" class="btn-submit">Add Fund</button>
    </form>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
