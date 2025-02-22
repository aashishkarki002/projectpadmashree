    <?php
 // Debugging Output
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "connect.php"; // Ensure correct DB connection

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            b.id, 
            c.category_name, 
            b.amount, 
            b.start_date, 
            b.end_date, 
            b.spent_amount AS spent  -- Ensure we get the correct spent value
        FROM budgets b
        JOIN expense_categories c ON b.category_id = c.id
        WHERE b.u_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$budgets = [];
while ($row = $result->fetch_assoc()) {
    $budgets[] = $row;
}

// Debugging Output to Console
error_log(json_encode($budgets));
echo json_encode($budgets);

    ?>