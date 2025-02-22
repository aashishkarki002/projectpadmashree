<?php

include("connect.php");

if(isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify the token
    $sql = "SELECT * FROM register WHERE verification_token = ? AND is_verified = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        // Update user as verified
        $update_sql = "UPDATE register SET is_verified = 1, verification_token = NULL WHERE verification_token = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("s", $token);
        
        if($update_stmt->execute()) {
            echo "
                <div style='text-align: center; font-family: Arial, sans-serif; margin-top: 50px;'>
                    <h2>Email Verified Successfully!</h2>
                    <p>Your email has been verified. You can now <a href='../frontend/login.php'>login</a> to your account.</p>
                </div>";
        } else {
            echo "Error verifying email.";
        }
        
        $update_stmt->close();
    } else {
        echo "Invalid or expired verification token.";
    }
    
    $stmt->close();
} else {
    echo "No verification token provided.";
}

$conn->close();
?>