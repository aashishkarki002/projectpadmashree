<?php
include("connect.php");
if ($_SERVER["REQUEST_METHOD"]=="POST") {
   $fname=filter_input(INPUT_POST,"f_name", FILTER_SANITIZE_SPECIAL_CHARS);
   $l_name=filter_input(INPUT_POST,"l_name", FILTER_SANITIZE_SPECIAL_CHARS);
      $email=filter_input(INPUT_POST,"email", FILTER_SANITIZE_EMAIL);
      $password=filter_input(INPUT_POST,"password", FILTER_SANITIZE_SPECIAL_CHARS);
      $c_password=filter_input(INPUT_POST,"c_password", FILTER_SANITIZE_SPECIAL_CHARS);
}
$error=[];
if (empty($fname)) {
    $errors[] = "First name is required.";
}

if (empty($l_name)) {
    $errors[] = "Last name is required.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email address is required.";
}

if (empty($password)) {
    $errors[] = "Password is required.";
}

if ($password !== $c_password) {
    $errors[] = "Passwords do not match.";
}
else{

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

}
try {

    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $sql = "INSERT INTO users (first_name, last_name, email, password) 
            VALUES (:fname, :l_name, :email, :password)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':l_name', $l_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

  
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Registration successful!</p>";
    } else {
        echo "<p style='color:red;'>An error occurred while registering your details. Please try again.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
}


?>