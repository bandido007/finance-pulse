<?php
session_start();
require_once __DIR__ . '/../models/Database.php';

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Redirect logged-in users away from auth pages
if (isset($_SESSION['user_id']) && in_array($request, ['/login', '/register'])) {
    header("Location: /dashboard");
    exit();
}

// Route handling
switch ($request) {
    case '/':
        header("Location: /login");
        exit();
    case '/login':
        require __DIR__ . '/../views/auth/login.php';
        break;
    case '/register':
        require __DIR__ . '/../views/auth/register.php';
        break;
    case '/auth':
        require __DIR__ . '/../controllers/AuthController.php';
        break;
    case '/dashboard':
        require __DIR__ . '/../views/dashboard.php';
        break;
    
    case '/expenses/add':
        require_once __DIR__ . '/../controllers/ExpenseController.php';
        $controller = new ExpenseController(new Database());
        $controller->showAddForm();
        break;
    
    case '/expenses/view':
        require_once __DIR__ . '/../controllers/ExpenseController.php';
        $controller = new ExpenseController(new Database());
        $controller->viewExpenses();
        break;
    
    case '/expenses/edit':
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: /expenses/view?error=Invalid%20expense%20ID");
            exit();
        }
        require_once __DIR__ . '/../controllers/ExpenseController.php';
        $controller = new ExpenseController(new Database());
        $controller->showEditForm($_GET['id']);
        break;
    
    case '/expenses/update':
        require_once __DIR__ . '/../controllers/ExpenseController.php';
        $controller = new ExpenseController(new Database());
        $controller->handleRequest();
        break;

    case '/expenses/handle':
    require_once __DIR__ . '/../controllers/ExpenseController.php';
    $controller = new ExpenseController(new Database());
    $controller->handleRequest();
    break;

    
        
    case '/funds/add':
        require_once __DIR__ . '/../controllers/FundController.php';
        $controller = new FundController(new Database());
        $controller->addFund();
        break;
    
    case '/funds/view':
        require_once __DIR__ . '/../controllers/FundController.php';
        $controller = new FundController(new Database());
        $controller->viewFunds();
        break;
    
    case '/funds/details':
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../controllers/FundController.php';
            $controller = new FundController(new Database());
            $controller->viewFundDetails($_GET['id']);
        }
        break;

    case '/funds/edit':
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: /funds/view?error=Invalid%20fund%20ID");
            exit();
        }
        require_once __DIR__ . '/../controllers/FundController.php';
        $controller = new FundController(new Database());
        $controller->editFund($_GET['id']);
        break;

    case '/funds/update':
        require_once __DIR__ . '/../controllers/FundController.php';
        $controller = new FundController(new Database());
        $controller->updateFund();
        break;

        // Expenses Delete
    case '/expenses/delete':
        require_once __DIR__ . '/../controllers/ExpenseController.php';
        $controller = new ExpenseController(new Database());
        $controller->deleteExpense();
        break;
    
    // Funds Delete
    case '/funds/delete':
        require_once __DIR__ . '/../controllers/FundController.php';
        $controller = new FundController(new Database());
        $controller->deleteFund();
        break;
    
    case '/logout':
        session_unset();
        session_destroy();
        header("Location: /login");
        exit();
        break;
        
    default:
        header("Location: /login");
        exit();
}
?>
