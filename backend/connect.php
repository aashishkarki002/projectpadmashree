<?php
$db_server="localhost";
$db_user="root";
$db_password="";
$db_name="project";
$conn="";

try {
    $conn=mysqli_connect($db_server,$db_user,$db_password,$db_name);
} catch (mysqli_sql_exception) {
    echo"database couldnt be connected";
}
?>