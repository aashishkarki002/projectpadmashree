<?php
session_start();
include("connect.php");
if (!isset($_SESSION['user_id'])) {
    echo 0;
    exit;
}
$user_id=$_SESSION['user_id'];
$sql= "SELECT SUM(amount) AS total_income FROM income WHERE u_id=$user_id";
$result=mysqli_query($conn,$sql);
$row=mysqli_fetch_assoc($result);
echo $row['total_income']??0;
mysqli_close($conn);

?>