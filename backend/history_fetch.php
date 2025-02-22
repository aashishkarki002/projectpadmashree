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

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    sendJsonResponse([
        "status" => "error",
        "message" => "Unauthorized access"
    ], 401);
}

$user_id = (int)$_SESSION['user_id'];

// Set fixed limit to 4 and get page number
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 4; // Fixed limit of 4 transactions
$offset = ($page - 1) * $limit;

// Optional filters
$startDate = isset($_GET['start_date']) ? filter_var($_GET['start_date'], FILTER_SANITIZE_STRING) : null;
$endDate = isset($_GET['end_date']) ? filter_var($_GET['end_date'], FILTER_SANITIZE_STRING) : null;
$type = isset($_GET['type']) ? filter_var($_GET['type'], FILTER_SANITIZE_STRING) : null;

try {
    // Combined query for both expenses and income using UNION
    $query = "SELECT 
                'expense' as type,
                e.id,
                e.u_id,
                e.amount,
                e.category,
                e.date,
                e.notes,
                COUNT(*) OVER() as total_count
            FROM expense e
            WHERE e.u_id = ?
            UNION ALL
            SELECT 
                'income' as type,
                i.id,
                i.u_id,
                i.amount,
                i.category,
                i.date,
                i.notes,
                COUNT(*) OVER() as total_count
            FROM income i
            WHERE i.u_id = ?";

    $params = [$user_id, $user_id];
    $types = "ii";

    // Add date range filters if provided
    if ($startDate && $endDate) {
        $query = "SELECT * FROM ($query) as combined 
                 WHERE date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= "ss";
    }

    // Add type filter if provided
    if ($type) {
        $query = "SELECT * FROM ($query) as combined 
                 WHERE type = ?";
        $params[] = $type;
        $types .= "s";
    }

    // Add sorting and pagination with fixed limit of 4
    $query = "SELECT *, COUNT(*) OVER() as final_count 
             FROM ($query) as filtered 
             ORDER BY date DESC 
             LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    // Bind parameters dynamically
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception("Failed to execute query: " . $stmt->error);
    }

    $result = $stmt->get_result();

    $transactions = [];
    $totalRecords = 0;

    while ($row = $result->fetch_assoc()) {
        $totalRecords = $row['final_count'];
        unset($row['final_count']); 
        unset($row['total_count']); 

        // Format date and amount
        $row['date'] = date('Y-m-d H:i:s', strtotime($row['date']));
        $row['amount'] = number_format((float)$row['amount'], 2, '.', '');

        // Add sign to amount based on transaction type
        if ($row['type'] === 'expense') {
            $row['amount'] = '-' . $row['amount'];
        }

        $transactions[] = $row;
    }

    // Calculate summary statistics
    $summary = [
        'total_income' => 0,
        'total_expenses' => 0,
        'net_amount' => 0
    ];

    foreach ($transactions as $transaction) {
        $amount = (float)$transaction['amount'];
        if ($transaction['type'] === 'income') {
            $summary['total_income'] += $amount;
        } else {
            $summary['total_expenses'] += abs($amount);
        }
    }
    $summary['net_amount'] = $summary['total_income'] - $summary['total_expenses'];

    // Prepare pagination metadata
    $totalPages = ceil($totalRecords / $limit);

    sendJsonResponse([
        "status" => "success",
        "data" => [
            "transactions" => $transactions,
            "summary" => $summary,
            "pagination" => [
                "current_page" => $page,
                "total_pages" => $totalPages,
                "total_records" => $totalRecords,
                "limit" => $limit
            ]
        ]
    ]);

} catch (Exception $e) {
    error_log("Transaction History Error: " . $e->getMessage());
    sendJsonResponse([
        "status" => "error",
        "message" => "An error occurred while fetching transactions"
    ], 500);

} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
    
}