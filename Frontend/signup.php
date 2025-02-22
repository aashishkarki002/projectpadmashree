<?php
include("../backend/connect.php");
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="css/signup.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiko:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="main">
        <div class="logo-container"><img src="img/img.png" alt="" class="logo">
        </div>

        <div class="second-main">
            <div class="title">
                <div class="titles">Create an account</div>
                <div class="sub-title ">Already have an account? <a href="login.php" id="signupForm">login</a></div>
            </div>
            <form action="/projectpadmashree/backend/insert.php" method="post">
                <div class="names">
                    <div><input type="text" placeholder="first name" class="firstname" name="f_name" required></div>
                    <div><input type="text" placeholder="last name" class="lastname" name="l_name" required></div>
                </div>
                <div><input type="email" placeholder="abc@example.com" class="email" name="email" id="email" required></div>
                <div id="email__msg"></div>
                <div id="password_holder">
                    <div><input type="password" placeholder="password" class="pword" id="password" name="password" required></div>
                    <div class="" id="msg"></div>
                    <div class="pwd_req" id="validate"></div>
                    <div><input type="password" placeholder="confirm password" class="cpword" id="c_password" name="c_password" required></div>
                </div>
                <div id="message" class="error_msg"></div>
                <div class="agree">
                    <div><input type="checkbox" name="checkbox" class="checkbox" required></div>
                    <div class="term">I agree to the <a href="">terms and condition</a></div>
                </div>
                <div class="create_account">
                    <div><button class="button " type="submit" name="submit" id="button">Create account</button></div>
                </div>
        </div>
        </form>
    </div>
    <script src="authentication.js">

    </script>
</body>

</html>