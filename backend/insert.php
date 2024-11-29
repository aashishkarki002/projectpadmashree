<?php 
if (isset($_POST['submit'])) {
    $f_name=htmlspecialchars(trim($_POST["f_name"]));
    $l_name=htmlspecialchars(trim($_POST["l_name"]));
    $email=htmlspecialchars(trim($_POST["email"]),FILTER_SANITIZE_EMAIL);
    $password=$_POST["password"];
 $hashed_password=password_hash($password,PASSWORD_BCRYPT);
include("connect.php");
$sql="INSERT INTO register(f_name,l_name,email,pword) VALUES('$f_name','$l_name','$email','$hashed_password')";

try {
    mysqli_query($conn,$sql);
    header("Location: ../frontend/login.php");
    exit();


} catch (mysqli_sql_exception) {
    echo"unable to register";
}

mysqli_close($conn);
}
?>
