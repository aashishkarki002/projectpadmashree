<?php
session_start();
include("connect.php");
if (!isset($_SESSION['user_id'])) {
    echo 0;
    exit;
}
$user_id=$_SESSION['user_id'];
$income_query= "SELECT SUM(amount) AS total_income FROM income WHERE u_id=$user_id";
$income_result=mysqli_query($conn,$income_query);
$income_data=mysqli_fetch_assoc($income_result);


$expense_query = "SELECT SUM(amount) AS total_expense FROM expense WHERE u_id=$user_id";
$expense_result = mysqli_query($conn, $expense_query);
$expense_data = mysqli_fetch_assoc($expense_result);
$response = [
    'total_income' => $income_data['total_income'],
    'total_expense' => $expense_data['total_expense'],
    'balance' => $income_data['total_income'] - $expense_data['total_expense']
];
header('Content-Type: application/json');
echo json_encode($response);

mysqli_close($conn);

?>