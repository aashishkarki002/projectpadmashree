
<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("connect.php");

// Check connection
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $amount = floatval($_POST['amount']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $note = mysqli_real_escape_string($conn, $_POST['note'] ?? '');
    $user_id = $_SESSION['user_id'] ?? 0; // Make sure you have user_id in session

    if (empty($category) || $amount <= 0 || empty($date)) {
        echo json_encode(['success' => false, 'error' => 'Invalid input data']);
        exit;
    }

    try {
        $query = "INSERT INTO income (u_id, category, amount, date, notes) 
                 VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isdss", $user_id, $category, $amount, $date, $note);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Failed to insert income");
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}