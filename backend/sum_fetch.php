<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    // Database configuration
    $host = 'localhost';
    $dbname = 'project1';
    $username = 'root';
    $password = '';

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }
    
    $user_id = $_SESSION['user_id'];
    $currentMonth = date('m');
    $currentYear = date('Y');

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Get current month's total expenses
    $currentExpensesStmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0) as total
        FROM expense
        WHERE u_id = ? AND MONTH(date) = ? AND YEAR(date) = ?
    ");
    $currentExpensesStmt->execute([$user_id, $currentMonth, $currentYear]);
    $currentExpenses = $currentExpensesStmt->fetch()['total'];

    // Get last month's expenses
    $lastMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
    $lastYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
    
    $lastExpensesStmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0) as total
        FROM expense
        WHERE u_id = ? AND MONTH(date) = ? AND YEAR(date) = ?
    ");
    $lastExpensesStmt->execute([$user_id, $lastMonth, $lastYear]);
    $lastExpenses = $lastExpensesStmt->fetch()['total'];

    // Calculate expense change percentage
    $expenseChange = $lastExpenses > 0 
        ? round((($currentExpenses - $lastExpenses) / $lastExpenses) * 100, 1)
        : 0;

    // Get category breakdown
    $categoryStmt = $pdo->prepare("
        SELECT 
            category,
            COALESCE(SUM(amount), 0) as total
        FROM expense
        WHERE u_id = ? 
        AND MONTH(date) = ? 
        AND YEAR(date) = ?
        GROUP BY category
        ORDER BY total DESC
    ");
    $categoryStmt->execute([$user_id, $currentMonth, $currentYear]);
    $categories = $categoryStmt->fetchAll();

    // Get monthly trend data (last 6 months)
    $monthlyTrendStmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(date, '%Y-%m') as month,
            COALESCE(SUM(amount), 0) as total
        FROM expense
        WHERE u_id = ?
        AND date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(date, '%Y-%m')
        ORDER BY month ASC
    ");
    $monthlyTrendStmt->execute([$user_id]);
    $monthlyTrend = $monthlyTrendStmt->fetchAll();

    // Mock income data (replace with actual income table if available)
    $monthlyIncome = 3000; // Example fixed monthly income
    $monthlySavings = $monthlyIncome - $currentExpenses;
    $savingsPercentage = round(($monthlySavings / $monthlyIncome) * 100, 1);

    // Prepare data for charts
    $months = [];
    $expenses = [];
    $income = [];
    foreach ($monthlyTrend as $trend) {
        $months[] = date('M', strtotime($trend['month'] . '-01'));
        $expenses[] = floatval($trend['total']);
        $income[] = $monthlyIncome; // Using mock fixed income
    }

    $expenseCategories = [];
    $categoryAmounts = [];
    foreach ($categories as $category) {
        $expenseCategories[] = $category['category'];
        $categoryAmounts[] = floatval($category['total']);
    }

    // Calculate budget status (assuming budget is 80% of income)
    $monthlyBudget = $monthlyIncome * 0.8;
    $budgetStatus = round((($monthlyBudget - $currentExpenses) / $monthlyBudget) * 100, 1);

    // Prepare response
    $response = [
        'success' => true,
        'total_expenses' => $currentExpenses,
        'expense_change' => $expenseChange,
        'monthly_savings' => $monthlySavings,
        'savings_percentage' => $savingsPercentage,
        'budget_status' => $budgetStatus,
        'months' => $months,
        'monthly_expenses' => $expenses,
        'monthly_income' => $income,
        'expense_categories' => $expenseCategories,
        'category_amounts' => $categoryAmounts,
        'largest_expense' => !empty($categories) ? [
            'category' => $categories[0]['category'],
            'amount' => $categories[0]['total']
        ] : null
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}