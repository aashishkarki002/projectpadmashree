<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['firstname'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit(); // Ensure no further code is executed after the redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/home.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiko:wght@400;600;700&display=swap" rel="stylesheet">
    <datalist id="income-categories">
    <?php
    include("../backend/connect.php");
    $query = "SELECT category_name FROM income_categories";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . htmlspecialchars($row['category_name']) . "'>";
    }
    ?>
    </datalist>
    <datalist id="expense-categories">
    <?php
    $query = "SELECT category_name FROM expense_categories";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . htmlspecialchars($row['category_name']) . "'>";
    }
    ?>
    </datalist>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        
    </style>
</head>
<body>
    <div class="main">
    <?php
     include "header.php";
     ?>
     <?php
     include "Sidebar.php";
     ?>
   
        <div class="mid-bar">
            <div class="dash">DASHBOARD</div>
            <div class="welcome">WELCOME 
            <?php echo strtoupper(htmlspecialchars($_SESSION['firstname'])); ?>
            </div>
            <div class="transaction">
                <div class="income">
                    <div class="title">Total income</div>
                    <div class="holder">
                    <div><img src="icons/icons8-income-50 1.png" alt="" class="transaction-icons"></div>
                    <div class="income-amt">NPR0</div>
                </div>
                </div>
                <div class="income">
                    <div class="title">Total Expense</div>
                    <div class="holder">
                    <div><img src="icons/icons8-expense-50 1.png" alt="" class="transaction-icons"></div>
                    <div class="expense-amt">NPR0</div>
                </div>
                </div>
                <div class="income">
                    <div class="title"> Balance</div>
                    <div class="holder">
                    <div><img src="icons/icons8-balance-48 1.png" alt="" class="transaction-icons"></div>
                    <div class="balance-amt">NPR0</div>
                </div>
                </div>
            </div>
            <div class="table">
                <div class="addincome">
                    <p class="addin">Add Income</p>
                    <form action="../backend/income_insert.php" method="post">
                    <div class="box">
                        <label for="category">Category</label>
                        <input list="income-categories" placeholder="select category" name="category">
                        <label for="amount">Amount</label>
                        <input type="number" placeholder="Enter Amount" name="amount">
                        <label for="date">Date</label>
                        <input type="date" name="date">
                        <label for="note">Note</label>
                        <input type="text" placeholder="Optional note" name="note">
                        <div class="b">
                        <button type="submit" class="btn" name="income">Add Income</button>
                    </div>
                </div>
                </form>
            </div>
            <div class="addincome">
                <p class="expense">Add expense</p>
                <form action="../backend/expense_insert.php" method="post">
                <div class="box">
                    <label for="category">Category</label>
                    <input list="expense-categories" placeholder="select category" name="category">
                    <label for="amount" >Amount</label>
                    <input type="number" placeholder="Enter Amount" name="amount">
                    <label for="date">Date</label>
                    <input type="date" name="date">
                    <label for="note">Note</label>
                    <input type="text" placeholder="Optional note" name="note">
                    <div class="b">
                    <button type="submit" class="expense-btn" name="expense">Add Expense</button>
                </div>
            </div>
            </form>
        </div>

    </div>
</div>
</div>
<div>
</div>
  </div>
                </div>
    <script src="navigation.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="home.js"></script>
</body>
</html>
