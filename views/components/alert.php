<?php
// Handle success messages from GET parameters
if (isset($_GET['success'])): ?>
    <div class="alert success">
        <?= htmlspecialchars(urldecode($_GET['success'])) ?>
    </div>
<?php endif; ?>

<?php
// Handle error messages from GET parameters
if (isset($_GET['error'])): 
    $errorKey = urldecode($_GET['error']);
    $errorMessages = [
        'database' => 'Database operation failed. Please try again.',
        'invalid_resource' => 'Invalid fund source selected.',
        'not_found' => 'Expense record not found.',
        'validation' => 'Invalid input data.',
        'system' => 'System error occurred.',
        'form_error' => 'Invalid form submission.',
        'invalid_amount' => 'Amount exceeds fund balance.',
        'Selected fund does not exist or you don\'t have access' => 'Invalid fund selection.'
    ];
    $errorMessage = $errorMessages[$errorKey] ?? 'An error occurred: ' . htmlspecialchars($errorKey);
?>
    <div class="alert error">
        <?= $errorMessage ?>
    </div>
<?php endif; ?>

<?php
// Handle session-based error messages
if (isset($_SESSION['error'])): ?>
    <div class="alert error">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php
// Handle session-based success messages
if (isset($_SESSION['success'])): ?>
    <div class="alert success">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>
