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
    if (!isset($data['id'])) {
        throw new Exception('Missing required field: budget id');
    }
    
    // Validate and sanitize budget_id
    $budgetId = filter_var($data['id'], FILTER_VALIDATE_INT);
    if ($budgetId === false || $budgetId <= 0) {
        throw new Exception('Invalid budget ID');
    }
    
    // Prepare and execute delete query
    $query = "DELETE FROM budgets WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare delete statement');
    }
    
    $success = $stmt->execute([$budgetId]);
    
    if (!$success) {
        throw new Exception('Failed to execute delete statement');
    }
    
    // Check if any rows were affected
    $affectedRows = $stmt->rowCount();
    if ($affectedRows === 0) {
        throw new Exception('Budget not found or already deleted');
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Budget deleted successfully',
        'affected_rows' => $affectedRows
    ]);
    
} catch (Exception $e) {
    // Log the error
    error_log('Delete budget error: ' . $e->getMessage());
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}