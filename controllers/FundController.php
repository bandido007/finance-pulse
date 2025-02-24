<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Fund.php';
require_once __DIR__ . '/../models/Database.php';

class FundController {
    private Fund $fundModel;
    private Database $database;

    public function __construct() {
        $this->database = new Database();
        $this->fundModel = new Fund($this->database);
    }

    public function addFund(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $data = [
                'type' => $_POST['type'] ?? '',
                'source' => $_POST['source'] ?? '',
                'amount' => (float)($_POST['amount'] ?? 0),
                'description' => $_POST['description'] ?? ''
            ];
    
            if ($this->fundModel->createFund($userId, $data)) {
                header("Location: /funds/view?success=Fund+added+successfully");
            } else {
                header("Location: /funds/add?error=Failed+to+add+fund");
            }
            exit();
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        require __DIR__ . '/../views/funds/add-fund.php';
    }

    public function getFunds(int $userId): array {
        return $this->fundModel->getFunds($userId);
    }
    

    public function viewFunds(): void {
        $userId = $_SESSION['user_id'];
        $funds = $this->fundModel->getFunds($userId);
        require __DIR__ . '/../views/funds/view-fund.php';
    }


    

    private function validateCsrfToken(): void {
    $token = $_POST['csrf_token'] ?? '';
    $storedToken = $_SESSION['csrf_token'] ?? '';
    if (!hash_equals($storedToken, $token)) {
        throw new InvalidArgumentException("Invalid CSRF token");
    }
}
    


    public function viewFundDetails(): void {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: /funds/view?error=invalid_id");
            exit();
        }
        $userId = $_SESSION['user_id'];
        $fundId = (int)$_GET['id'];
        $fund = $this->fundModel->getFundDetails($userId, $fundId);
        if (!$fund) {
            header("Location: /funds/view?error=fund_not_found");
            exit();
        }
        $expenses = $this->database->fetchAll(
            "SELECT * FROM expenses WHERE fund_id = ? AND user_id = ? ORDER BY date DESC",
            [$fundId, $userId]
        );
        $fund['expenses'] = $expenses;
        require __DIR__ . '/../views/funds/fund-details.php';
    }

    public function editFund(): void {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: /funds/view?error=invalid_id");
            exit();
        }
    
        $userId = $_SESSION['user_id']; // Get logged-in user ID
        $fundId = (int)$_GET['id'];
        
        // Pass both user ID and fund ID
        $fund = $this->fundModel->getFundDetails($userId, $fundId);
    
        if (!$fund) {
            header("Location: /funds/view?error=fund_not_found");
            exit();
        }
    
        require __DIR__ . '/../views/funds/edit-fund.php';
    }

    public function updateFund(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            header("Location: /funds/view?error=invalid_request");
            exit();
        }
    
        $userId = $_SESSION['user_id'];
        $fundId = (int)$_POST['id'];
        
        $data = [
            'type' => $_POST['type'] ?? '',
            'source' => $_POST['source'] ?? '',
            'amount' => (float)($_POST['amount'] ?? 0),
            'description' => $_POST['description'] ?? ''
        ];
    
        // Pass user ID, fund ID, and data
        if ($this->fundModel->updateFund($userId, $fundId, $data)) {
            header("Location: /funds/view?success=Fund+updated+successfully");
        } else {
            header("Location: /funds/edit?id=$fundId&error=Update+failed");
        }
        exit();
    }
}