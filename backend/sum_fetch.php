<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("connect.php");

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    sendJsonResponse(["status" => "error", "message" => "Unauthorized access"], 401);
}

$user_id = (int)$_SESSION['user_id'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Fetch transactions
        $query = "SELECT * FROM transactions WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();

        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }

        sendJsonResponse(["status" => "success", "data" => $transactions]);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Add a new transaction
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            sendJsonResponse(["status" => "error", "message" => "Invalid input"], 400);
        }

        $type = $data['type'] ?? null;
        $amount = $data['amount'] ?? null;
        $category = $data['category'] ?? null;
        $date = $data['date'] ?? null;
        $notes = $data['notes'] ?? null;

        if (!$type || !$amount || !$category || !$date) {
            sendJsonResponse(["status" => "error", "message" => "Missing required fields"], 400);
        }

        $query = "INSERT INTO transactions (user_id, type, amount, category, date, notes) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("isdsss", $user_id, $type, $amount, $category, $date, $notes);

        if ($stmt->execute()) {
            sendJsonResponse(["status" => "success", "message" => "Transaction added successfully"]);
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }

    } else {
        sendJsonResponse(["status" => "error", "message" => "Invalid request method"], 405);
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    sendJsonResponse(["status" => "error", "message" => "An error occurred"], 500);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>