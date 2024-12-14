<?php
session_start();
include("connect.php");
$user_id=$_SESSION['user_id'];
$expense_query = "SELECT SUM(amount) AS total_expense FROM expense WHERE u_id=$user_id";
$expense_result = mysqli_query($conn, $expense_query);

if ($expense_result) {
    // Fetch the result
    $expense_data = mysqli_fetch_assoc($expense_result);
    
    if ($expense_data) {
        // Check if the result is not null or 0
        $total_expense = $expense_data['total_expense'] ? $expense_data['total_expense'] : 0;
        echo $total_expense; // Return the total expense
    } else {
        echo "Error: No data returned from query.";
    }
} else {
    // Output the exact MySQL error
    echo "MySQL Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>