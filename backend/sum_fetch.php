<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    // Database configuration
    $host = 'localhost';
    $dbname = 'hyaa';
    $username = 'root';
    $password = '';

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    $user_id = $_SESSION['user_id'];
    $currentMonth = date('m');
    $currentYear = date('Y');
    $currentDate = date('Y-m-d');
    $firstDayOfMonth = date('Y-m-01');
    $lastDayOfMonth = date('Y-m-t');

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Fetch total expenses for the current month
    $currentExpensesStmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0) AS total 
        FROM expense 
        WHERE u_id = ? AND MONTH(date) = ? AND YEAR(date) = ?
    ");
    $currentExpensesStmt->execute([$user_id, $currentMonth, $currentYear]);
    $currentExpenses = floatval($currentExpensesStmt->fetch()['total']);

    // Fetch total income for the current month
    $currentIncomeStmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0) AS total 
        FROM income 
        WHERE u_id = ? AND MONTH(date) = ? AND YEAR(date) = ?
    ");
    $currentIncomeStmt->execute([$user_id, $currentMonth, $currentYear]);
    $currentIncome = floatval($currentIncomeStmt->fetch()['total']);

    // Fetch last month's expenses
    $lastMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
    $lastYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

    $lastExpensesStmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0) AS total 
        FROM expense 
        WHERE u_id = ? AND MONTH(date) = ? AND YEAR(date) = ?
    ");
    $lastExpensesStmt->execute([$user_id, $lastMonth, $lastYear]);
    $lastExpenses = floatval($lastExpensesStmt->fetch()['total']);

    // Calculate expense change percentage
    $expenseChange = $lastExpenses > 0 
        ? round((($currentExpenses - $lastExpenses) / $lastExpenses) * 100, 1)
        : 0;

    // UPDATED: Calculate monthly savings as 30% of income
    $monthlySavings = $currentIncome * 0.3;
    $savingsPercentage = 30.0; // This is now fixed at 30%

    // Fetch category-wise expenses breakdown
    $categoryStmt = $pdo->prepare("
    SELECT 
        ec.category_name, 
        COALESCE(SUM(e.amount), 0) AS total 
    FROM expense e
    JOIN expense_categories ec ON e.category_id = ec.id
    WHERE e.u_id = ? AND MONTH(e.date) = ? AND YEAR(e.date) = ?
    GROUP BY ec.category_name 
    ORDER BY total DESC
    ");
    $categoryStmt->execute([$user_id, $currentMonth, $currentYear]);
    $categories = $categoryStmt->fetchAll();

    // Fetch last 6 months' expenses trend
    $monthlyTrendStmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(date, '%Y-%m') AS month, 
            COALESCE(SUM(amount), 0) AS total 
        FROM expense 
        WHERE u_id = ? AND date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH) 
        GROUP BY DATE_FORMAT(date, '%Y-%m') 
        ORDER BY month ASC
    ");
    $monthlyTrendStmt->execute([$user_id]);
    $monthlyTrend = $monthlyTrendStmt->fetchAll();

    // Prepare data for charts
    $months = [];
    $expenses = [];
    foreach ($monthlyTrend as $trend) {
        $months[] = date('M', strtotime($trend['month'] . '-01'));
        $expenses[] = floatval($trend['total']);
    }

    $expenseCategories = [];
    $categoryAmounts = [];
    foreach ($categories as $category) {
        $expenseCategories[] = $category['category_name'];
        $categoryAmounts[] = floatval($category['total']);
    }

    // UPDATED BUDGET CALCULATION
    // Fetch active budgets by category for the current month
    $budgetStmt = $pdo->prepare("
        SELECT 
            b.category_id,
            ec.category_name,
            SUM(b.amount) AS budget_amount,
            COALESCE((
                SELECT SUM(e.amount) 
                FROM expense e 
                WHERE e.u_id = b.u_id 
                AND e.category_id = b.category_id
                AND MONTH(e.date) = ?
                AND YEAR(e.date) = ?
            ), 0) AS spent_amount
        FROM budgets b
        JOIN expense_categories ec ON b.category_id = ec.id
        WHERE b.u_id = ? 
        AND (
            (b.period = 'monthly' AND MONTH(b.start_date) <= ? AND MONTH(b.end_date) >= ? AND YEAR(b.start_date) <= ? AND YEAR(b.end_date) >= ?)
            OR 
            (b.period != 'monthly' AND b.start_date <= ? AND b.end_date >= ?)
        )
        GROUP BY b.category_id, ec.category_name
    ");

    $budgetStmt->execute([
        $currentMonth, $currentYear,  // For expense filtering
        $user_id, 
        $currentMonth, $currentMonth, $currentYear, $currentYear,  // For monthly budgets
        $lastDayOfMonth, $firstDayOfMonth  // For other period types
    ]);

    $budgetsByCategory = $budgetStmt->fetchAll();

    // Calculate overall budget status
    $totalBudget = 0;
    $totalSpent = 0;
    $categoryBudgets = [];

    foreach ($budgetsByCategory as $budget) {
        $totalBudget += $budget['budget_amount'];
        $totalSpent += $budget['spent_amount'];
        
        // Calculate category-specific status
        $categoryStatus = $budget['budget_amount'] > 0
            ? round((($budget['budget_amount'] - $budget['spent_amount']) / $budget['budget_amount']) * 100, 1)
            : 0;
            
        $categoryBudgets[] = [
            'category' => $budget['category_name'],
            'budget' => floatval($budget['budget_amount']),
            'spent' => floatval($budget['spent_amount']),
            'status' => $categoryStatus
        ];
    }

    // Overall budget status
    $budgetStatus = $totalBudget > 0
        ? round((($totalBudget - $totalSpent) / $totalBudget) * 100, 1)
        : 0;

    // Prepare response
    $response = [
        'success' => true,
        'total_expenses' => $currentExpenses,
        'total_income' => $currentIncome,
        'expense_change' => $expenseChange,
        'monthly_savings' => $monthlySavings,
        'savings_percentage' => $savingsPercentage,
        'budget_status' => $budgetStatus,
        'category_budgets' => $categoryBudgets,
        'total_budget' => $totalBudget,
        'months' => $months,
        'monthly_expenses' => $expenses,
        'monthly_income' => array_fill(0, count($months), $currentIncome), // Ensure income is mapped to months
        'expense_categories' => $expenseCategories,
        'category_amounts' => $categoryAmounts,
        'largest_expense' => !empty($categories) ? [
            'category' => $categories[0]['category_name'],
            'amount' => floatval($categories[0]['total'])
        ] : null
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>