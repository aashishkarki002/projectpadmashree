<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="css/login.css">
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
            <div class="titles">Welcome</div>
<div class="sub-title ">Enter your Email to continue </div>
        </div>
<form action="/projectpadmashree/backend/login_insert.php" method="post">
<div class="details">
<div><input type="text" placeholder="abc@gmail.com" class="email" name="email" required></div>
<div><input type="password" placeholder="Password" class="password" name="pword" required></div>
</div>
<div class="forgot-pass">
    Forgot password?
</div>

<div class="term">Dont have an account? <a href="signup.php">Signup</a> </div>
<div class="create_account">
<div class="button-holder" ><button class="button" name="login" type="submit" >LOGIN</button></div>
</div>

    </div>
    </form>
</div>
<script>





    
</script>
</body>
</html>