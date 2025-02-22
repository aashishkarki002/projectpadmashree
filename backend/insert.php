<?php
// Adjust the path to point to your vendor directory
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {
    // Sanitize and validate input
    $f_name = htmlspecialchars(trim($_POST["f_name"]));
    $l_name = htmlspecialchars(trim($_POST["l_name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Generate verification token
    $verification_token = bin2hex(random_bytes(32));
    $is_verified = 0;

    include("connect.php");
    
  
    $sql = "INSERT INTO register (f_name, l_name, email, pword, verification_token, is_verified) 
            VALUES (?, ?, ?, ?, ?, ?)";
            
    try {
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $f_name, $l_name, $email, $hashed_password, $verification_token, $is_verified);
        
        if (mysqli_stmt_execute($stmt)) {
            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);
            
            try {
                // Server settings for Gmail SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'aashishkarki002@gmail.com'; // Your Gmail address
                $mail->Password = 'bcms wybq mfbd ryky';    // Your Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Recipients
                $mail->setFrom('your-email@gmail.com', 'Your Name');
                $mail->addAddress($email, $f_name . ' ' . $l_name);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Email Address';
                
                // Local verification link
                $verification_link = "http://localhost/projectpadmashree/backend/verify.php?token=" . $verification_token;
                
                $mail->Body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                            <h2>Welcome, {$f_name}!</h2>
                            <p>Thank you for registering. Please verify your email address to continue:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='{$verification_link}' 
                                   style='background-color: #4CAF50;
                                          color: white;
                                          padding: 12px 25px;
                                          text-decoration: none;
                                          border-radius: 4px;
                                          display: inline-block;'>
                                    Verify Email Address
                                </a>
                            </div>
                            <p>Or copy and paste this link in your browser:</p>
                            <p>{$verification_link}</p>
                        </div>
                    </body>
                    </html>";
                
                $mail->AltBody = "Welcome! Please verify your email by clicking this link: {$verification_link}";
                
                $mail->send();
                
                // Redirect to verification pending page
                header("Location: ../frontend/verify-email.php");
                exit();
                
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
        
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "This email is already registered.";
        } else {
            echo "Registration error: " . $e->getMessage();
        }
    } finally {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>