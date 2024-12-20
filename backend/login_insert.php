<?php
session_start();
include("connect.php");
$error = "";

if(isset($_POST['login'])){
    $email=$_POST["email"];
    $password=$_POST["pword"];
 $sql="SELECT * FROM register WHERE email='$email'";
$result=mysqli_query($conn,$sql);
if(mysqli_num_rows($result)>0){
    $user =mysqli_fetch_assoc($result);
    if(password_verify($password,$user['pword'])){
$_SESSION['user_id']=$user['id'];
$_SESSION['email']=$user['email'];
$_SESSION['firstname']=$user['f_name'];
$_SESSION['logged_in']=true;
header("Location: ../frontend/home.php");
exit();
    }
    else {
        $_SESSION['error'] = "Incorrect password.";
        header("location: ../frontend/login.php");
        exit();
    }
}else{
    $_SESSION['error'] = "No user found with that email.";
    header("location:../frontend/login.php");
    exit();

}

}







?>
