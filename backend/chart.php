<?php
session_start();
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

require_once("connect.php");

// Function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}

// Validate user session
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    sendJsonResponse([
        "status" => "error",
        "message" => "Unauthorized access"
    ], 401);
}

$user_id = (int)$_SESSION['user_id'];

try {
    // Check if database connection is established
    if (!isset($conn)) {
        throw new Exception("Database connection not established");
    }

    // Fetch monthly totals from income table
    $monthlyIncomeQuery = "
        SELECT 
            DATE_FORMAT(date, '%Y-%m') as month,
            SUM(amount) as total_income
        FROM income 
        WHERE u_id = ?
        AND date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(date, '%Y-%m')
    ";

    // Fetch monthly totals from expense table
    $monthlyExpenseQuery = "
        SELECT 
            DATE_FORMAT(date, '%Y-%m') as month,
            SUM(amount) as total_expense
        FROM expense
        WHERE u_id = ?
        AND date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(date, '%Y-%m')
    ";

    // Fetch category breakdown for income
    $incomeCategoryQuery = "
        SELECT 
            category,
            SUM(amount) as total_amount
        FROM income
        WHERE u_id = ?
        AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m')
        GROUP BY category
        ORDER BY total_amount DESC
    ";

    // Fetch category breakdown for expense
    $expenseCategoryQuery = "
        SELECT 
            category,
            SUM(amount) as total_amount
        FROM expense
        WHERE u_id = ?
        AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m')
        GROUP BY category
        ORDER BY total_amount DESC
    ";

    // Execute income monthly query
    $incomeStmt = $conn->prepare($monthlyIncomeQuery);
    if (!$incomeStmt) {
        throw new Exception("Income prepare failed: " . $conn->error);
    }
    $incomeStmt->bind_param("i", $user_id);
    if (!$incomeStmt->execute()) {
        throw new Exception("Income execute failed: " . $incomeStmt->error);
    }
    $incomeResult = $incomeStmt->get_result();

    // Execute expense monthly query
    $expenseStmt = $conn->prepare($monthlyExpenseQuery);
    if (!$expenseStmt) {
        throw new Exception("Expense prepare failed: " . $conn->error);
    }
    $expenseStmt->bind_param("i", $user_id);
    if (!$expenseStmt->execute()) {
        throw new Exception("Expense execute failed: " . $expenseStmt->error);
    }
    $expenseResult = $expenseStmt->get_result();

    // Execute income category query
    $incomeCatStmt = $conn->prepare($incomeCategoryQuery);
    if (!$incomeCatStmt) {
        throw new Exception("Income category prepare failed: " . $conn->error);
    }
    $incomeCatStmt->bind_param("i", $user_id);
    if (!$incomeCatStmt->execute()) {
        throw new Exception("Income category execute failed: " . $incomeCatStmt->error);
    }
    $incomeCatResult = $incomeCatStmt->get_result();

    // Execute expense category query
    $expenseCatStmt = $conn->prepare($expenseCategoryQuery);
    if (!$expenseCatStmt) {
        throw new Exception("Expense category prepare failed: " . $conn->error);
    }
    $expenseCatStmt->bind_param("i", $user_id);
    if (!$expenseCatStmt->execute()) {
        throw new Exception("Expense category execute failed: " . $expenseCatStmt->error);
    }
    $expenseCatResult = $expenseCatStmt->get_result();

    // Process monthly data
    $monthlyData = [];
    $monthlyIncomes = [];
    $monthlyExpenses = [];

    // Process income results
    while ($row = $incomeResult->fetch_assoc()) {
        $monthlyIncomes[$row['month']] = (float)$row['total_income'];
    }

    // Process expense results
    while ($row = $expenseResult->fetch_assoc()) {
        $monthlyExpenses[$row['month']] = (float)$row['total_expense'];
    }

    // Combine income and expense data
    $allMonths = array_unique(array_merge(array_keys($monthlyIncomes), array_keys($monthlyExpenses)));
    sort($allMonths);

    foreach ($allMonths as $month) {
        $income = $monthlyIncomes[$month] ?? 0;
        $expense = $monthlyExpenses[$month] ?? 0;
        $monthlyData[] = [
            'month' => $month,
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense
        ];
    }

    // Process category data
    $categoryData = [
        'income' => [],
        'expense' => []
    ];

    while ($row = $incomeCatResult->fetch_assoc()) {
        $categoryData['income'][] = [
            'category' => $row['category'],
            'amount' => (float)$row['total_amount']
        ];
    }

    while ($row = $expenseCatResult->fetch_assoc()) {
        $categoryData['expense'][] = [
            'category' => $row['category'],
            'amount' => (float)$row['total_amount']
        ];
    }

    // Calculate current month totals
    $currentMonthIncome = array_sum(array_column($categoryData['income'], 'amount'));
    $currentMonthExpense = array_sum(array_column($categoryData['expense'], 'amount'));

    // Prepare response
    $response = [
        'monthly' => $monthlyData,
        'categories' => $categoryData,
        'currentMonth' => [
            'income' => $currentMonthIncome,
            'expense' => $currentMonthExpense,
            'balance' => $currentMonthIncome - $currentMonthExpense
        ]
    ];

    // Send success response
    sendJsonResponse($response);

} catch (Exception $e) {
    error_log("Financial Data Error: " . $e->getMessage());
    sendJsonResponse([
        "status" => "error",
        "message" => "An error occurred while fetching financial data",
        "debug" => [
            "error_message" => $e->getMessage(),
            "user_id" => $user_id
        ]
    ], 500);

} finally {
    // Close all statements
    if (isset($incomeStmt)) $incomeStmt->close();
    if (isset($expenseStmt)) $expenseStmt->close();
    if (isset($incomeCatStmt)) $incomeCatStmt->close();
    if (isset($expenseCatStmt)) $expenseCatStmt->close();
    if (isset($conn)) $conn->close();
}
?>