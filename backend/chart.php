<?php
header('Content-Type: application/json');

include("connect.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total income
$incomeQuery = "SELECT SUM(amount) AS total_income FROM income";
$incomeResult = $conn->query($incomeQuery);
$incomeRow = $incomeResult->fetch_assoc();
$totalIncome = $incomeRow['total_income'] ?? 0;

// Get total expense
$expenseQuery = "SELECT SUM(amount) AS total_expense FROM expense";
$expenseResult = $conn->query($expenseQuery);
$expenseRow = $expenseResult->fetch_assoc();
$totalExpense = $expenseRow['total_expense'] ?? 0;

// Format data for Google Charts
$data = [
    ['Type', 'Amount'], // Column labels
    ['Income', (float)$totalIncome],
    ['Expense', (float)$totalExpense]
];

$conn->close();

echo json_encode($data);
?>
