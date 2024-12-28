<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

// Include the database connection
include("connect.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

// Retrieve the user ID from the session
$user_id = $_SESSION['user_id'];

try {
    // Query for total income
    $income_query = "SELECT SUM(amount) AS total_income FROM income WHERE u_id = ?";
    $income_stmt = $conn->prepare($income_query);
    $income_stmt->bind_param('i', $user_id);
    $income_stmt->execute();
    $income_result = $income_stmt->get_result();
    $income_data = $income_result->fetch_assoc();
    $total_income = $income_data['total_income'] ?? 0; // Default to 0 if null

    // Query for total expenses
    $expense_query = "SELECT SUM(amount) AS total_expense FROM expense WHERE u_id = ?";
    $expense_stmt = $conn->prepare($expense_query);
    $expense_stmt->bind_param('i', $user_id);
    $expense_stmt->execute();
    $expense_result = $expense_stmt->get_result();
    $expense_data = $expense_result->fetch_assoc();
    $total_expense = $expense_data['total_expense'] ?? 0; // Default to 0 if null

    // Calculate balance
    $balance = $total_income - $total_expense;

    // Prepare the response
    $response = [
        'total_income' => $total_income,
        'total_expense' => $total_expense,
        'balance' => $balance
    ];

    // Encode the response as JSON and output it
    echo json_encode($response);

} catch (Exception $e) {
    // Handle any errors
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
} finally {
    // Close the database connection
    $conn->close();
    exit;
}
?>
