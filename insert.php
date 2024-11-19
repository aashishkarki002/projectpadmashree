<?php 
// if (isset($_POST['submit'])) {
//     $f_name="aashish";
//     $l_name="karki";
//     $email="aashishkarki002@gmail.com";
//     $password="123456";
//     $c_password="123456";
// } authentication required


include("connect.php");
$sql="INSERT INTO registration(firstname,lastname,email,password,c_password) VALUES('$f_name','$l_name','$email','$password','$c_password')";

try {
    mysqli_query($conn,$sql);
    echo"user registerd";

} catch (mysqli_sql_exception) {
    echo"unable to register";
}

mysqli_close($conn);
?>
