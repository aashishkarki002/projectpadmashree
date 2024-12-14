<?php
session_start();

include("connect.php");
if (!isset($_SESSION['user_id'])) {
    echo 0;
    exit;
}

$user_id=$_SESSION['user_id'];
$total_income = 0;
$total_expense = 0;

// Fetch total income for the user
$income_query = "SELECT SUM(amount) AS total_income FROM income WHERE u_id = ?";
$income_stmt = mysqli_prepare($conn, $income_query);
mysqli_stmt_bind_param($income_stmt, 'i', $user_id);
mysqli_stmt_execute($income_stmt);
$income_result = mysqli_stmt_get_result($income_stmt);
if ($income_result) {
    $income_data = mysqli_fetch_assoc($income_result);
    $total_income = $income_data['total_income'] ? $income_data['total_income'] : 0; 
}
// Fetch total expense for the user
$expense_query = "SELECT SUM(amount) AS total_expense FROM expense WHERE u_id = ?";
$expense_stmt = mysqli_prepare($conn, $expense_query);
mysqli_stmt_bind_param($expense_stmt, 'i', $user_id);
mysqli_stmt_execute($expense_stmt);
$expense_result = mysqli_stmt_get_result($expense_stmt);
if ($expense_result) {
    $expense_data = mysqli_fetch_assoc($expense_result);
    $total_expense = $expense_data['total_expense'] ? $expense_data['total_expense'] : 0;
}


$balance = $total_income - $total_expense;

// Return balance
echo $balance;

// Close database connection
mysqli_close($conn);
?>
