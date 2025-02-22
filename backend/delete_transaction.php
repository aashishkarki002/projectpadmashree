<?php
header('Content-Type: application/json');
require_once 'connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Get the raw POST data
    $rawData = file_get_contents('php://input');
    
    // Log the raw input for debugging
    error_log('Raw input: ' . $rawData);
    
    // Decode JSON data
    $data = json_decode($rawData, true);
    
    // Check for JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }
    
    // Validate required fields
    if (!isset($data['transaction_id']) || !isset($data['transaction_type'])) {
        throw new Exception('Missing required fields: transaction_id or transaction_type');
    }
    
    // Validate and sanitize transaction_id
    $transactionId = filter_var($data['transaction_id'], FILTER_VALIDATE_INT);
    if ($transactionId === false || $transactionId <= 0) {
        throw new Exception('Invalid transaction ID');
    }
    
    // Validate transaction_type
    $transactionType = strtolower(trim($data['transaction_type']));
    if (!in_array($transactionType, ['income', 'expense'])) {
        throw new Exception('Invalid transaction type. Must be "income" or "expense"');
    }
    
    // Determine table name
    $table = ($transactionType === 'income') ? 'income' : 'expense';
    
    // Prepare and execute delete query
    $query = "DELETE FROM {$table} WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare delete statement');
    }
    
    $success = $stmt->execute([$transactionId]);
    
    if (!$success) {
        throw new Exception('Failed to execute delete statement');
    }
    
    // Check if any rows were affected
    $affectedRows = $stmt->rowCount();
    if ($affectedRows === 0) {
        throw new Exception('Transaction not found or already deleted');
    }
    
    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Transaction deleted successfully',
        'affected_rows' => $affectedRows
    ]);
    
} catch (Exception $e) {
    // Log the error
    error_log('Delete transaction error: ' . $e->getMessage());
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}