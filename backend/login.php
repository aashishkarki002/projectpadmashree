<?php
include("connect.php");

if(isset($_POST['login'])){
    $email=$_POST["email"];
    $password=$_POST["password"];
 $sql="SELECT * FROM register WHERE email='$email'";
$result=mysqli_query($conn,$sql);


}







?>
