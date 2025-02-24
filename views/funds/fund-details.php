<?php include __DIR__ . '/../../components/header.php'; ?>

<link rel="stylesheet" href="/css/style.css">


<div class="container">
    <h2><?= htmlspecialchars($fund['source']) ?> Details</h2>
    
    <div class="fund-details">
        <div class="detail-item">
            <label>Type:</label>
            <span class="fund-type <?= $fund['type'] ?>">
                <?= ucfirst($fund['type']) ?>
            </span>
        </div>
        
        <div class="detail-item">
            <label>Initial Amount:</label>
            <div class="value">TZS <?= number_format($fund['amount'], 2) ?></div>
        </div>
        
        <div class="detail-item">
            <label>Remaining Balance:</label>
            <div class="value <?= ($fund['remaining'] < 0) ? 'negative' : '' ?>">
                TZS <?= number_format($fund['remaining'], 2) ?>
            </div>
        </div>
        
        <div class="detail-item">
            <label>Description:</label>
            <p><?= nl2br(htmlspecialchars($fund['description'])) ?></p>
        </div>
    </div>

    <!-- Expense Section -->
    <h3>Associated Expenses</h3>
    <?php if (!empty($fund['expenses'])): ?>
        <div class="expenses-list">
            <?php foreach ($fund['expenses'] as $expense): ?>
                <div class="expense-item">
                    <div class="expense-amount">
                        TZS <?= number_format($expense['amount'], 2) ?>
                    </div>
                    <div class="expense-description">
                        <?= htmlspecialchars($expense['description']) ?>
                    </div>
                    <small><?= date('M d, Y', strtotime($expense['date'])) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No expenses recorded for this fund.</p>
    <?php endif; ?>

    <a href="/funds/view" class="btn-back">Back to Funds</a>
</div>

<div class="detail-actions">
    <a href="/funds/edit?id=<?= $fund['id'] ?>" class="btn-edit">
        Edit Fund
    </a>
    <a href="/funds/view" class="btn-back">Back to List</a>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>