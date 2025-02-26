<?php include __DIR__ . '/../components/header.php'; ?>
<div class="container">
    <h2>Your Fund Sources</h2>
    
    <?php if (empty($funds)): ?>
        <div class="no-funds">
            <p>No funds found. <a href="/funds/add">Add your first fund</a></p>
        </div>
    <?php else: ?>
        <div class="funds-overview">
            <?php foreach ($funds as $fund): ?>
                <div class="fund-card">
                    <div class="fund-header">
                        <h3><?= htmlspecialchars($fund['source']) ?></h3>
                        <span class="fund-type <?= htmlspecialchars($fund['type']) ?>">
                            <?= ucfirst(htmlspecialchars($fund['type'])) ?>
                        </span>
                    </div>
                    
                    <div class="fund-stats">
                        <div class="stat">
                            <label>Initial Amount</label>
                            <div class="value">TZS <?= number_format($fund['amount'], 2) ?></div>
                        </div>
                        
                        <div class="stat">
                            <label>Remaining</label>
                            <?php 
                            $remaining = $fund['remaining'] ?? ($fund['amount'] - ($fund['spent'] ?? 0));
                            $remainingClass = $remaining < 0 ? 'negative' : ($remaining < ($fund['amount'] * 0.2) ? 'warning' : 'positive');
                            ?>
                            <div class="value <?= $remainingClass ?>">
                                TZS <?= number_format($remaining, 2) ?>
                            </div>
                        </div>
                        
                        <div class="stat">
                            <label>Used</label>
                            <div class="value">
                                <?= number_format(($fund['amount'] - $remaining) / $fund['amount'] * 100, 1) ?>%
                            </div>
                        </div>
                    </div>
                    
                    <div class="fund-actions">
                        <a href="/funds/details?id=<?= $fund['id'] ?>" class="btn-details">View Details</a>
                        <a href="/funds/edit?id=<?= $fund['id'] ?>" class="btn-edit">Edit</a>
                    </div>
                    
                    <div class="fund-actions">
                        <form action="/funds/delete" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="fund_id" value="<?= $fund['id'] ?>">
                            <button type="submit" class="btn-delete" 
                                    onclick="return confirm('Delete this fund and ALL associated expenses?')">
                                Delete Fund
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="add-fund-button">
            <a href="/funds/add" class="btn-primary">Add New Fund</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>