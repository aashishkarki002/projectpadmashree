<?php
session_start();
error_log('Session status: ' . session_status());
error_log('Session ID: ' . session_id());
error_log('User ID in session: ' . ($_SESSION['user_id'] ?? 'not set'));

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("connect.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check database connection
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $amount = floatval($_POST['amount']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $note = mysqli_real_escape_string($conn, $_POST['note'] ?? '');

    // Validate input
    if (empty($category) || $amount <= 0 || empty($date)) {
        echo json_encode(['success' => false, 'error' => 'Invalid input data']);
        exit;
    }

    // Check if user exists
    $check_user = "SELECT id FROM register WHERE id = ?";
    $stmt = mysqli_prepare($conn, $check_user);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }

    // Check if category exists in expense_categories
    $query = "SELECT id FROM expense_categories WHERE category_name = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $category_id = $row['id'];
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid category']);
        exit;
    }

    // Insert into expense table
    try {
        $query = "INSERT INTO expense (u_id, category, amount, date, notes) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isdss", $user_id, $category, $amount, $date, $note);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn)); // Capture MySQL error
        }

        // Update budget spent_amount
        $update_budget = "UPDATE budgets SET spent_amount = spent_amount + ? WHERE u_id = ? AND category_id = ?";
        $stmt = mysqli_prepare($conn, $update_budget);
        mysqli_stmt_bind_param($stmt, "dii", $amount, $user_id, $category_id);
        mysqli_stmt_execute($stmt);

        // Check if any row was affected (budget exists)
        if (mysqli_stmt_affected_rows($stmt) === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'No budget found for this category',
                'category_id' => $category_id
            ]);
        } else {
            echo json_encode(['success' => true]);
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'user_id' => $user_id
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

mysqli_close($conn);
?>
