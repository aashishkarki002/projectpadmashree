<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiko:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Amiko', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .main {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 2rem;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .message {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .email-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 1.5rem;
        }

        .back-to-login {
            display: inline-block;
            text-decoration: none;
            color: #4A90E2;
            font-weight: 600;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-to-login:hover {
            background-color: #f0f7ff;
        }

        .note {
            font-size: 0.9rem;
            color: #888;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="logo-container">
            <img src="img/img.png" alt="Logo" class="logo">
        </div>
        <svg class="email-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" color="#4A90E2">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <h1 class="title">Verify Your Email Address</h1>
        <p class="message">
            We've sent a verification link to your email address.<br>
            Please check your inbox to complete the registration process.
        </p>
        <p class="note">
            Don't forget to check your spam folder if you don't see the email in your inbox.
        </p>
        <a href="login.php" class="back-to-login">Return to Login</a>
    </div>
</body>
</html>