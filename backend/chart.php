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

// Get and validate time period
$period = $_GET['period'] ?? 'month';
if (!in_array($period, ['week', 'month', 'year'])) {
    $period = 'month';
}

try {
    // Check if database connection is established
    if (!isset($conn)) {
        throw new Exception("Database connection not established");
    }

    // Define date format and interval based on period
    switch ($period) {
        case 'week':
            $dateFormat = '%Y-%u'; // Year-Week
            $interval = '6 WEEK';
            $currentPeriodCondition = 'YEARWEEK(date, 1) = YEARWEEK(CURRENT_DATE, 1)';
            break;
        case 'year':
            $dateFormat = '%Y';
            $interval = '5 YEAR';
            $currentPeriodCondition = 'YEAR(date) = YEAR(CURRENT_DATE)';
            break;
        default: // month
            $dateFormat = '%Y-%m';
            $interval = '6 MONTH';
            $currentPeriodCondition = 'DATE_FORMAT(date, "%Y-%m") = DATE_FORMAT(CURRENT_DATE, "%Y-%m")';
    }

    // Fetch totals from income table
    $incomeQuery = "
        SELECT 
            DATE_FORMAT(date, ?) as period,
            SUM(amount) as total_income
        FROM income 
        WHERE u_id = ?
        AND date >= DATE_SUB(CURRENT_DATE, INTERVAL $interval)
        GROUP BY DATE_FORMAT(date, ?)
    ";

    // Fetch totals from expense table
    $expenseQuery = "
        SELECT 
            DATE_FORMAT(date, ?) as period,
            SUM(amount) as total_expense
        FROM expense
        WHERE u_id = ?
        AND date >= DATE_SUB(CURRENT_DATE, INTERVAL $interval)
        GROUP BY DATE_FORMAT(date, ?)
    ";

    // Fetch current period totals
    $currentPeriodQuery = "
        SELECT 
            (SELECT COALESCE(SUM(amount), 0) 
             FROM income 
             WHERE u_id = ? AND $currentPeriodCondition) as current_income,
            (SELECT COALESCE(SUM(amount), 0) 
             FROM expense 
             WHERE u_id = ? AND $currentPeriodCondition) as current_expense
    ";

    // Execute income query
    $incomeStmt = $conn->prepare($incomeQuery);
    if (!$incomeStmt) {
        throw new Exception("Income prepare failed: " . $conn->error);
    }
    $incomeStmt->bind_param("sis", $dateFormat, $user_id, $dateFormat);
    if (!$incomeStmt->execute()) {
        throw new Exception("Income execute failed: " . $incomeStmt->error);
    }
    $incomeResult = $incomeStmt->get_result();

    // Execute expense query
    $expenseStmt = $conn->prepare($expenseQuery);
    if (!$expenseStmt) {
        throw new Exception("Expense prepare failed: " . $conn->error);
    }
    $expenseStmt->bind_param("sis", $dateFormat, $user_id, $dateFormat);
    if (!$expenseStmt->execute()) {
        throw new Exception("Expense execute failed: " . $expenseStmt->error);
    }
    $expenseResult = $expenseStmt->get_result();

    // Execute current period query
    $currentStmt = $conn->prepare($currentPeriodQuery);
    if (!$currentStmt) {
        throw new Exception("Current period prepare failed: " . $conn->error);
    }
    $currentStmt->bind_param("ii", $user_id, $user_id);
    if (!$currentStmt->execute()) {
        throw new Exception("Current period execute failed: " . $currentStmt->error);
    }
    $currentResult = $currentStmt->get_result();

    // Process trend data
    $trendData = [];
    $periodIncomes = [];
    $periodExpenses = [];

    // Process income results
    while ($row = $incomeResult->fetch_assoc()) {
        $periodIncomes[$row['period']] = (float)$row['total_income'];
    }

    // Process expense results
    while ($row = $expenseResult->fetch_assoc()) {
        $periodExpenses[$row['period']] = (float)$row['total_expense'];
    }

    // Combine income and expense data
    $allPeriods = array_unique(array_merge(array_keys($periodIncomes), array_keys($periodExpenses)));
    sort($allPeriods);

    foreach ($allPeriods as $period_key) {
        $income = $periodIncomes[$period_key] ?? 0;
        $expense = $periodExpenses[$period_key] ?? 0;
        $trendData[] = [
            'month' => $period_key, // Keep 'month' key for frontend compatibility
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense
        ];
    }

    // Get current period totals
    $currentPeriodData = $currentResult->fetch_assoc();
    $currentIncome = (float)$currentPeriodData['current_income'];
    $currentExpense = (float)$currentPeriodData['current_expense'];

    // Prepare response
    $response = [
        'monthly' => $trendData, // Keep 'monthly' key for frontend compatibility
        'currentMonth' => [ // Keep 'currentMonth' key for frontend compatibility
            'income' => $currentIncome,
            'expense' => $currentExpense,
            'balance' => $currentIncome - $currentExpense
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
            "user_id" => $user_id,
            "period" => $period
        ]
    ], 500);

} finally {
    // Close all statements
    if (isset($incomeStmt)) $incomeStmt->close();
    if (isset($expenseStmt)) $expenseStmt->close();
    if (isset($currentStmt)) $currentStmt->close();
    if (isset($conn)) $conn->close();
}
?>