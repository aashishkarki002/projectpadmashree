<?php
session_start();
include("connect.php");
if(isset($_POST['income'])){
    $category=$_POST["category"];
    $amount=$_POST["amount"];
    $date=$_POST["date"];
    $note=$_POST["note"];
    $u_id=$_SESSION["user_id"];


    $sql="INSERT INTO income(category,amount,date,notes,u_id) VALUES('$category','$amount','$date','$note','$u_id')";
}
try {
    if (mysqli_query($conn, $sql)) {
        header("Location: ../frontend/home.php");
        exit();
    } else {
        throw new Exception("Registration failed: " . mysqli_error($conn));
    }
} catch (Exception $e) {
   
    echo "Sorry, an error occurred during registration. Please try again later.";
}

mysqli_close($conn);

?>