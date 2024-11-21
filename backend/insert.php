<?php 
if (isset($_POST['submit'])) {
    $f_name=$_POST["f_name"];
    $l_name=$_POST["l_name"];
    $email=$_POST["email"];
    $password=$_POST["password"];
 
} 


include("connect.php");
$sql="INSERT INTO register(f_name,l_name,email,pword) VALUES('$f_name','$l_name','$email','$password')";

try {
    mysqli_query($conn,$sql);
    echo"user registerd";

} catch (mysqli_sql_exception) {
    echo"unable to register";
}

mysqli_close($conn);
?>
