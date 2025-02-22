<?php
// Update the budget after an expense is added
$category_id = $_GET['category_id'];
$expense = $_GET['expense'];

// Fetch the current budget for the category
// Assuming you have a `budget` table with `category_id` and `amount`
$budgetQuery = "SELECT amount FROM budgets WHERE category_id = :category_id";
$statement = $pdo->prepare($budgetQuery);
$statement->execute(['category_id' => $category_id]);
$budget = $statement->fetch();

// Calculate the new budget after subtracting the expense
$newBudgetAmount = $budget['amount'] - $expense;

// Update the budget in the database
$updateQuery = "UPDATE budgets SET amount = :amount WHERE category_id = :category_id";
$statement = $pdo->prepare($updateQuery);
$statement->execute(['amount' => $newBudgetAmount, 'category_id' => $category_id]);

// Return a response
echo json_encode(["message" => "Budget updated successfully"]);
?>
