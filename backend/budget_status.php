<?php
// budget_status.php
header('Content-Type: application/json');

// Database connection (replace with actual connection details)
$servername = "localhost"; // Your server name
$username = "root"; // Your username
$password = ""; // Your password
$dbname = "hyaa"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch budget details and calculate spent amounts
$sql = "
    SELECT b.id, b.amount, b.spent_amount, b.u_id, b.category_id, 
           IFNULL(SUM(e.amount), 0) AS total_spent, 
           (b.amount - b.spent_amount) AS remaining
    FROM budgets b
    LEFT JOIN expense e ON b.u_id = e.u_id AND b.category_id = e.category_id
    WHERE b.deleted_at IS NULL
    GROUP BY b.id, b.amount, b.spent_amount, b.u_id, b.category_id
";

$result = $conn->query($sql);

$budgets = [];
$totalBudget = 0;
$totalSpent = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calculate the status based on remaining amount
        $status = $row['remaining'] >= 0 ? 'On Track' : 'Over Budget';

        // Add budget data to the array
        $budgets[] = [
            'id' => $row['id'],
            'amount' => $row['amount'],
            'spent_amount' => $row['spent_amount'],
            'remaining' => $row['remaining'],
            'status' => $status
        ];

        // Update total budget and total spent
        $totalBudget += $row['amount'];
        $totalSpent += $row['spent_amount'];
    }
}

// Close connection
$conn->close();

// Return the response in JSON format
echo json_encode([
    'success' => true,
    'budgets' => $budgets,
    'total_budget' => $totalBudget,
    'total_spent' => $totalSpent
]);
?>
