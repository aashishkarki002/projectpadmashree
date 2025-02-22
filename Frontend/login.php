<?php
session_start();
$error = ""; 
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error']; 
    unset($_SESSION['error']); 
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiko:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
<div><input type="text" placeholder="abc@gmail.com" class="email" name="email" id="email" required></div>
<div id="status"></div>
<div><input type="password" placeholder="Password" class="password" name="pword" required>
<span id="togglePassword" style="cursor: pointer;">👁️</span>
</div>

<?php if (!empty($error)): ?>
                    <div class="error-message" style="color: red; margin-top: 10px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
</div>


<div class="term">Dont have an account? <a href="signup.php">Signup</a> </div>
</span><div class="create_account">
<div class="button-holder" ><button class="button" name="login" type="submit" >LOGIN</button></div>
</div>

    </div>
    </form>
</div>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordField = document.getElementById('password');
    this.textContent = passwordField.type === 'password' ? '🙈' : '👁️';
    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
});

</script>
<script>

$(document).ready(function() {
        let timeoutId;
        
        $('#email').on('input', function() {
            clearTimeout(timeoutId);
            const email = $(this).val();
            const statusDiv = $('#status');
            
           
            if (!email) {
                statusDiv.html('');
                return;
            }
            
           
            timeoutId = setTimeout(function() {
                $.ajax({
                    url: '../backend/auth_log.php',
                    type: 'POST',
                    data: { email: email },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.exists) {
                            statusDiv.html('')
                                   .removeClass('error')
                                   .addClass('success');
                        } else {
                            statusDiv.html('Email is not registered!')
                                   .removeClass('sucess')
                                   .addClass('error');
                        }
                    },
                    error: function() {
                        statusDiv.html('Error checking email')
                               .removeClass('success')
                               .addClass('error');
                    }
                });
            }, 500);
        });
    });




    
</script>
</body>
</html>