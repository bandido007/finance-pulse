<?php
declare(strict_types=1);


require_once __DIR__ . '/../models/Database.php';

class ExpenseController {
    private $db;
    private $maxAmount = 1000000000; // 1 billion TZS
    
    public $validCategories = [
        'Food', 'Transport', 'Utilities', 'Medical & Health',
        'Entertainment', 'Shopping', 'Gifts & Donations',
        'Dates & Relationship', 'Helping Friends & Family', 'Subscriptions',
        'Savings', 'Investments', 'Debt Payments', 'Insurance',
        'Education', 'Work Expenses'
    ];
    
    public function __construct(Database $db) {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function viewExpenses(): void {
        try {
            $userId = $this->getAuthenticatedUserId();

                // Add debug logging
            error_log("Viewing expenses for user: " . $userId);
        
            
            $expenses = $this->db->fetchAll(
                "SELECT e.*, f.source AS fund_source 
                FROM expenses e
                LEFT JOIN funds f ON e.fund_id = f.id
                WHERE e.user_id = ?
                ORDER BY e.date DESC, e.id DESC",
                [$userId]
            );

                // Add debug logging
            error_log("Retrieved expenses: " . print_r($expenses, true));

    
            $availableFunds = $this->getAvailableFunds($userId);
            $processed = $this->processExpenseData($expenses);
            $processedData = $processed['data'];
            $grandTotal = $processed['total'];

                // Add debug logging
            error_log("Processed data: " . print_r($processedData, true));
        
            
            require __DIR__ . '/../views/expenses/view-expenses.php';
    
        } catch (PDOException $e) {
            error_log("Database error in viewExpenses: " . $e->getMessage());

            $this->logSystemError($e);
            $this->redirectWithError('/expenses/view', 'database_error');
        }
    }

    public function showAddForm(): void {
        try {
            $userId = $this->getAuthenticatedUserId();
            $availableFunds = $this->getAvailableFunds($userId);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            require __DIR__ . '/../views/expenses/add-expenses.php';
        } catch (Exception $e) {
            $this->logSystemError($e);
            $this->redirectWithError('/expenses/add', 'form_error');
        }
    }

    public function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCsrfToken();
                $postData = $this->sanitizeAndValidatePostData($_POST);

                if (isset($postData['expense_id'])) {
                    $this->updateExpense($postData);
                } else {
                    $this->addExpense($postData);
                }
                
            } catch (InvalidArgumentException $e) {
                $this->handleUserError($e->getMessage(), $_POST);
            } catch (PDOException $e) {
                $this->logSystemError($e);
                $this->handleUserError("Database error occurred", $_POST);
            } catch (Exception $e) {
                $this->logSystemError($e);
                $this->handleUserError("System error occurred", $_POST);
            }
        }
    }

    private function addExpense(array $data): void {
        $this->db->beginTransaction();
        
        try {
            $userId = $this->getAuthenticatedUserId();
            $validatedData = $this->validateExpenseData($data);
                // Add debug logging
            error_log("Adding expense: " . print_r($validatedData, true));
        
            
            if ($validatedData['fund_id'] !== null) {
                $this->validateFundBalance($validatedData['fund_id'], $validatedData['amount'], $userId);
            }
    
            $this->db->execute(
                "INSERT INTO expenses 
                (user_id, date, category, amount, description, fund_id) 
                VALUES (:user_id, :date, :category, :amount, :description, :fund_id)",
                [
                    'user_id' => $userId,
                    'date' => $validatedData['date'],
                    'category' => $validatedData['category'],
                    'amount' => $validatedData['amount'],
                    'description' => $validatedData['description'],
                    'fund_id' => $validatedData['fund_id']
                ]
            );

            // Add debug logging
            error_log("Insert result: " . print_r($result, true));
    
            $this->db->commit();
            $this->redirectWithSuccess('/expenses/view', 'Expense added successfully!');
    
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error adding expense: " . $e->getMessage());

            $this->handleUserError($e->getMessage(), $data);
        }
    }

    private function validateExpenseData(array $data): array {
        return [
            'date' => $this->validateDate($data['date'] ?? ''),
            'category' => $this->validateCategory($data['category'] ?? ''),
            'amount' => $this->validateAmount((float)($data['amount'] ?? 0)),
            'description' => $this->sanitizeDescription($data['description'] ?? ''),
            'fund_id' => $this->validateFundId($data['fund_id'] ?? '')
        ];
    }

    private function validateDate(string $date): string {
        if (!DateTime::createFromFormat('Y-m-d', $date)) {
            throw new InvalidArgumentException("Invalid date format (YYYY-MM-DD required)");
        }
        $maxDate = new DateTime('+1 day');
        $inputDate = new DateTime($date);
        if ($inputDate > $maxDate) {
            throw new InvalidArgumentException("Date cannot be in the future");
        }
        return $date;
    }

    private function validateAmount(float $amount): float {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Amount must be greater than zero");
        }
        if ($amount > $this->maxAmount) {
            throw new InvalidArgumentException("Amount exceeds maximum allowed value of TZS " . number_format($this->maxAmount));
        }
        return round($amount, 2);
    }

    private function validateCategory(string $category): string {
        if (!in_array($category, $this->validCategories)) {
            throw new InvalidArgumentException("Invalid expense category selected");
        }
        return $category;
    }

    private function sanitizeDescription(string $description): string {
        $cleaned = strip_tags($description);
        return substr($cleaned, 0, 500);
    }

    private function validateFundId(string $fundId): ?int {
        if (empty($fundId)) return null;
        $fund = $this->db->fetchOne(
            "SELECT id FROM funds WHERE id = ? AND user_id = ?",
            [(int)$fundId, $this->getAuthenticatedUserId()]
        );
        if (!$fund) {
            throw new InvalidArgumentException("Invalid fund source selected");
        }
        return (int)$fundId;
    }

    private function validateFundBalance(int $fundId, float $amount, int $userId): void {
        $fund = $this->db->fetchOne(
            "SELECT 
                f.amount - COALESCE(SUM(e.amount), 0) AS remaining
            FROM funds f
            LEFT JOIN expenses e ON e.fund_id = f.id
            WHERE f.id = ? AND f.user_id = ?
            GROUP BY f.id",
            [$fundId, $userId]
        );
    
        if (!$fund || (float)$fund['remaining'] < $amount) {
            throw new InvalidArgumentException(
                "Insufficient funds. Available: TZS " . 
                ($fund ? number_format($fund['remaining'], 2) : '0.00')
            );
        }
    }

    private function processExpenseData(array $expenses): array {
        $processed = [];
        $grandTotal = 0;
        foreach ($expenses as $expense) {
            $date = date('Y-m-d', strtotime($expense['date']));
            if (!isset($processed[$date])) {
                $processed[$date] = [
                    'date' => $date,
                    'daily_total' => 0,
                    'expenses' => []
                ];
            }
            $amount = (float)$expense['amount'];
            $processed[$date]['daily_total'] += $amount;
            $grandTotal += $amount;
            $processed[$date]['expenses'][] = $expense;
        }
        return [
            'data' => array_values($processed),
            'total' => $grandTotal
        ];
    }

    private function getAvailableFunds(int $userId): array {
        return $this->db->fetchAll(
            "SELECT f.id, f.source, 
            (f.amount - COALESCE(SUM(e.amount), 0)) AS remaining
            FROM funds f
            LEFT JOIN expenses e ON e.fund_id = f.id
            WHERE f.user_id = ?
            GROUP BY f.id, f.source",
            [$userId]
        );
    }

    private function getAuthenticatedUserId(): int {
        if (empty($_SESSION['user_id'])) {
            throw new InvalidArgumentException("Authentication required");
        }
        return (int)$_SESSION['user_id'];
    }

    private function validateCsrfToken(): void {
        $token = $_POST['csrf_token'] ?? '';
        $storedToken = $_SESSION['csrf_token'] ?? '';
        if (!hash_equals($storedToken, $token)) {
            throw new InvalidArgumentException("Invalid CSRF token");
        }
    }

    private function handleUserError(string $message, array $data = []): void {
        $_SESSION['form_data'] = $data;
        $_SESSION['error'] = $message;
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/expenses/add'));
        exit();
    }

    private function redirectWithSuccess(string $url, string $message): void {
        unset($_SESSION['form_data']);
        $_SESSION['success'] = $message;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header("Location: $url");
        exit();
    }

    private function logSystemError(Throwable $e): void {
        error_log(sprintf(
            "[%s] ERROR %s: %s\n%s",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getTraceAsString()
        ));
    }

    private function redirectWithError(string $url, string $errorType): void {
        header("Location: $url?error=$errorType");
        exit();
    }

    private function sanitizeAndValidatePostData(array $post): array {
        // Basic sanitization - adjust as per your needs
        return array_map('trim', $post);
    }

    private function updateExpense(array $data): void {
        $this->db->beginTransaction();
        try {
            $userId = $this->getAuthenticatedUserId();
            $validatedData = $this->validateExpenseData($data);
            $originalAmount = (float)$data['original_amount'];
            $newAmount = $validatedData['amount'];
            $amountDifference = $newAmount - $originalAmount;
    
            // 1. Handle original fund balance restoration
            if (!empty($data['original_fund_id'])) {
                $this->db->execute(
                    "UPDATE funds 
                    SET amount = amount + ? 
                    WHERE id = ? AND user_id = ?",
                    [$originalAmount, $data['original_fund_id'], $userId]
                );
            }
    
            // 2. Handle new fund deduction/update
            if ($validatedData['fund_id'] !== null) {
                // Check if changing funds
                if ($data['original_fund_id'] != $validatedData['fund_id']) {
                    $this->validateFundBalance($validatedData['fund_id'], $newAmount, $userId);
                }
    
                // Update new fund balance
                $this->db->execute(
                    "UPDATE funds 
                    SET amount = amount - ? 
                    WHERE id = ? AND user_id = ?",
                    [$newAmount, $validatedData['fund_id'], $userId]
                );
            }
    
            // 3. Update the expense record
            $this->db->execute(
                "UPDATE expenses SET
                    date = :date,
                    category = :category,
                    amount = :amount,
                    description = :description,
                    fund_id = :fund_id
                WHERE id = :id AND user_id = :user_id",
                [
                    'date' => $validatedData['date'],
                    'category' => $validatedData['category'],
                    'amount' => $newAmount,
                    'description' => $validatedData['description'],
                    'fund_id' => $validatedData['fund_id'],
                    'id' => $data['expense_id'],
                    'user_id' => $userId
                ]
            );
    
            $this->db->commit();
            $this->redirectWithSuccess('/expenses/view', 'Expense updated successfully!');
    
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->handleUserError($e->getMessage(), $data);
        }
    }
    
    public function fetchAll(string $query, array $params = []): array {
    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function showEditForm(int $expenseId): void {
    try {
        $userId = $this->getAuthenticatedUserId();
        $expense = $this->db->fetchOne(
            "SELECT * FROM expenses WHERE id = ? AND user_id = ?",
            [$expenseId, $userId]
        );
        
        if (!$expense) {
            throw new Exception("Expense not found");
        }

        $availableFunds = $this->getAvailableFunds($userId);
        
        require __DIR__ . '/../views/expenses/edit-expenses.php';
        
    } catch (Exception $e) {
        error_log($e->getMessage());
        header("Location: /expenses/view?error=not_found");
        exit();
    }
}

    public function deleteExpense(): void {
    try {
        $this->validateCsrfToken();
        $userId = $this->getAuthenticatedUserId();
        $expenseId = (int)($_POST['expense_id'] ?? 0);

        // Get expense details first
        $expense = $this->db->fetchOne(
            "SELECT * FROM expenses WHERE id = ? AND user_id = ?",
            [$expenseId, $userId]
        );

        if (!$expense) {
            throw new InvalidArgumentException("Expense not found");
        }

        $this->db->beginTransaction();

        // Update fund balance if expense had a fund
        if ($expense['fund_id']) {
            $this->db->execute(
                "UPDATE funds SET amount = amount + ? 
                WHERE id = ? AND user_id = ?",
                [
                    $expense['amount'],
                    $expense['fund_id'],
                    $userId
                ]
            );
        }

        // Delete the expense
        $this->db->execute(
            "DELETE FROM expenses WHERE id = ? AND user_id = ?",
            [$expenseId, $userId]
        );

        $this->db->commit();
        $this->redirectWithSuccess('/expenses/view', 'Expense deleted successfully!');

    } catch (Exception $e) {
        $this->db->rollBack();
        $this->handleUserError($e->getMessage());
    }
}
}