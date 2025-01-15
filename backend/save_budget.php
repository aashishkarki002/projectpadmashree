<?php
session_start();
include("connect.php");

header('Content-Type: application/json');

if(isset($_POST['budget'])) {
    try {
        // Validate inputs
        if(empty($_POST['category']) || empty($_POST['amount']) || empty($_POST['period'])) {
            throw new Exception("All fields are required");
        }

        // Sanitize inputs
        $user_id = $_SESSION['user_id'];
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $amount = mysqli_real_escape_string($conn, $_POST['amount']);
        $period = mysqli_real_escape_string($conn, $_POST['period']);
        $created_at = date('Y-m-d H:i:s');

        // Check if budget exists
        $check_query = "SELECT id FROM budgets WHERE u_id = ? AND category = ? AND period = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        
        if(!$check_stmt) {
            throw new Exception("Database error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($check_stmt, "iss", $user_id, $category, $period);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if(mysqli_stmt_num_rows($check_stmt) > 0) {
            // Update existing budget
            $update_query = "UPDATE budgets SET amount = ?, updated_at = ? WHERE u_id = ? AND category = ? AND period = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "dsiss", $amount, $created_at, $user_id, $category, $period);
        } else {
            // Insert new budget
            $insert_query = "INSERT INTO budgets (u_id, category, amount, period, created_at) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "isdss", $user_id, $category, $amount, $period, $created_at);
        }

        if(!$stmt) {
            throw new Exception("Database error: " . mysqli_error($conn));
        }

        if(mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Failed to save budget: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        if(isset($check_stmt)) mysqli_stmt_close($check_stmt);
        if(isset($stmt)) mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>