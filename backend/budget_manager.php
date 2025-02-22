<?php
require 'connect.php'; // Ensure you have a database connection file

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$category_id = $_POST['category'] ?? '';
$amount = $_POST['amount'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

if (empty($category_id) || empty($amount) || empty($start_date) || empty($end_date)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Debugging output
error_log("User ID: $user_id, Category ID: $category_id, Amount: $amount, Start Date: $start_date, End Date: $end_date");

// Prepare and insert the budget
$stmt = $conn->prepare("INSERT INTO budgets (u_id, category_id, amount, start_date, end_date, spent_amount) VALUES (?, ?, ?, ?, ?, 0)");
$stmt->bind_param("iidss", $user_id, $category_id, $amount, $start_date, $end_date);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Budget added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>
