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

// Optional parameters for pagination and filtering
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
$offset = ($page - 1) * $limit;

// Optional date range filter
$startDate = isset($_GET['start_date']) ? filter_var($_GET['start_date'], FILTER_SANITIZE_STRING) : null;
$endDate = isset($_GET['end_date']) ? filter_var($_GET['end_date'], FILTER_SANITIZE_STRING) : null;

try {
    // Base query
    $query = "SELECT 
                e.u_id,
                e.amount,
                e.category,
                e.date,
                e.notes,
                COUNT(*) OVER() as total_count
            FROM expense e
            WHERE e.u_id = ?";
    
    $params = [$user_id];
    $types = "i";

    // Add date range filters if provided
    if ($startDate && $endDate) {
        $query .= " AND e.date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= "ss";
    }

    // Add sorting and pagination
    $query .= " ORDER BY e.date DESC LIMIT ? OFFSET ?";
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
        $totalRecords = $row['total_count'];
        unset($row['total_count']); // Remove count from individual records
        
        // Format date and amount
        $row['date'] = date('Y-m-d H:i:s', strtotime($row['date']));
        $row['amount'] = number_format((float)$row['amount'], 2, '.', '');
        
        $transactions[] = $row;
    }

    // Prepare pagination metadata
    $totalPages = ceil($totalRecords / $limit);
    
    sendJsonResponse([
        "status" => "success",
        "data" => [
            "transactions" => $transactions,
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
