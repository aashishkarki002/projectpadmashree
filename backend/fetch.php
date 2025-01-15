<?php
session_start();
include("connect.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo 0;
    exit;
}

$user_id = $_SESSION['user_id'];


$income_query = "
    SELECT SUM(amount) AS total_income 
    FROM income 
    WHERE u_id = $user_id
    AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m');
";
$income_result = mysqli_query($conn, $income_query);
$income_data = mysqli_fetch_assoc($income_result);


$expense_query = "
    SELECT SUM(amount) AS total_expense 
    FROM expense 
    WHERE u_id = $user_id
    AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m');
";
$expense_result = mysqli_query($conn, $expense_query);
$expense_data = mysqli_fetch_assoc($expense_result);


$response = [
    'total_income' => $income_data['total_income'] ?? 0, 
    'total_expense' => $expense_data['total_expense'] ?? 0, 
    'balance' => ($income_data['total_income'] ?? 0) - ($expense_data['total_expense'] ?? 0)
];


header('Content-Type: application/json');
echo json_encode($response);


mysqli_close($conn);
?>