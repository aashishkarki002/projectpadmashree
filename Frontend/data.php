<?php
header('Content-Type: application/json');

// Example hardcoded data
$data = [
    ["Type", "Total"],  // Google Charts header
    ["Income", 8000],
    ["Expense", 3000]
];

// Convert the data to JSON format and output it
echo json_encode($data);
?>
