<?php
// save_budget.php
session_start();

// Set header to return JSON response
header('Content-Type: application/json');

try {
    // Include database connection
    require_once 'connect.php';

    // Verify user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }

    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and sanitize inputs
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $period = mysqli_real_escape_string($conn, $_POST['period'] ?? '');
    $userId = $_SESSION['user_id'];

    // Validate required fields
    if (empty($category) || empty($amount) || empty($period)) {
        throw new Exception('Missing required fields');
    }

    // Validate amount
    if ($amount <= 0) {
        throw new Exception('Invalid amount');
    }

    // Validate period
    $validPeriods = ['weekly', 'monthly', 'yearly'];
    if (!in_array($period, $validPeriods)) {
        throw new Exception('Invalid period');
    }

    // Prepare SQL statement with correct column names
    $query = "INSERT INTO budgets (u_id, category, amount, period, created_at) 
             VALUES (?, ?, ?, ?, NOW()) 
             ON DUPLICATE KEY UPDATE 
             amount = ?, 
             period = ?, 
             updated_at = NOW()";

    // Prepare and bind parameters
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isdssd", 
        $userId, 
        $category, 
        $amount, 
        $period, 
        $amount,  // For the UPDATE part
        $period   // For the UPDATE part
    );

    // Execute statement
    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        throw new Exception('Failed to save budget: ' . mysqli_error($conn));
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Budget saved successfully',
        'data' => [
            'category' => $category,
            'amount' => $amount,
            'period' => $period
        ]
    ]);

} catch (Exception $e) {
    // Log error
    error_log("Budget save error: " . $e->getMessage());
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close connection
if (isset($conn)) {
    mysqli_close($conn);
}