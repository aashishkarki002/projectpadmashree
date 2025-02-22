<?php
header('Content-Type: application/json');
require '../backend/connect.php'; // Ensure this file correctly connects to the database

$sql = "SELECT id, category_name FROM expense_categories";
$result = $conn->query($sql);

$categories = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    echo json_encode($categories);
} else {
    echo json_encode(["error" => "Failed to fetch categories"]);
}
?>
