<?php include __DIR__ . '/../../components/header.php'; ?>

<link rel="stylesheet" href="/css/style.css">


<div class="container">
    <h2>Edit Fund Source</h2>
    <form action="/funds/update" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <input type="hidden" name="id" value="<?= $fund['id'] ?>">

        <div class="form-group">
            <label>Type:</label>
            <select name="type" required>
                <option value="aid" <?= $fund['type'] === 'aid' ? 'selected' : '' ?>>Financial Aid</option>
                <option value="saving" <?= $fund['type'] === 'saving' ? 'selected' : '' ?>>Personal Saving</option>
            </select>
        </div>

        <div class="form-group">
            <label>Source Name:</label>
            <input type="text" name="source" required 
                value="<?= htmlspecialchars($fund['source']) ?>"
                placeholder="e.g. Parent Support, Emergency Fund">
        </div>

        <div class="form-group">
            <label>Amount (TZS):</label>
            <input type="number" step="0.01" name="amount" required
                value="<?= htmlspecialchars($fund['amount']) ?>">
        </div>

        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" rows="3"><?= 
                htmlspecialchars($fund['description']) 
            ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-update">Update Fund</button>
            <a href="/funds/view" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>