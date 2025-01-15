<?php
session_start();
include("connect.php");

if(isset($_POST['income'])) {
    try {
        // Validate inputs
        if(empty($_POST["category"]) || empty($_POST["amount"]) || empty($_POST["date"])) {
            throw new Exception("Please fill in all required fields");
        }

        // Sanitize inputs
        $category = mysqli_real_escape_string($conn, $_POST["category"]);
        $amount = mysqli_real_escape_string($conn, $_POST["amount"]);
        $date = mysqli_real_escape_string($conn, $_POST["date"]);
        $note = mysqli_real_escape_string($conn, $_POST["note"]);
        $u_id = $_SESSION["user_id"];

        // Prepare statement
        $sql = "INSERT INTO income(category, amount, date, notes, u_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if(!$stmt) {
            throw new Exception("Database error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "sdssi", $category, $amount, $date, $note, $u_id);
        
        if(mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Failed to add income: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        if(isset($stmt)) mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>