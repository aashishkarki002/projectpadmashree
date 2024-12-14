<?php
session_start();


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
</head>
<body>
    <div class="main">
         <div class="top-bar">
            <div class="logo">
                <img src="img/img.png" alt="" class="img"> 
            </div>
        </div>
     
        <div class="side-bar">
            <div class="individual" id="home">
            <div><img src="icons/home.png" alt="" class="icons"></div>
            <div>Home</div>
        </div>
            <div class="individual" id="stats">
            <div><img src="icons/bar-chart-square-01.png" alt="" class="icons"></div>
            <div>Statistics</div>
        </div>
            <div class="individual" id="summary">
            <div><img src="icons/coins-rotate.png" alt="" class="icons"></div>
            <div>Summary</div>
        </div>
            <div class="individual" id="history">
            <div><img src="icons/history.png" alt="" class="icons"></div>
            <div>History</div>
        </div>
            <div class="individual" id="setting" id="setting">
            <div><img src="icons/Vector.png" alt="" class="icons"></div>
            <div>Settings</div>
        </div>
        </div>  
        <div class="mid-bar">
            <div class="dash">DASHBOARD</div>
            <div class="welcome">WELCOME 
            <?php echo strtoupper( htmlspecialchars($_SESSION['firstname'])); ?>
            </div>
            <div class="transaction">
                <div class="income" >
                    <div class="title">Total income</div>
                    <div class="holder">
                    <div><img src="icons/icons8-income-50 1.png" alt="" class="transaction-icons"></div>
                    <div class="income-amt">&#8360;150000</div>
                </div>
                </div>
                <div class="income" >
                    <div class="title">Total Expense</div>
                    <div class="holder">
                    <div><img src="icons/icons8-expense-50 1.png" alt="" class="transaction-icons"></div>
                    <div class="expense-amt">&#8360;150000</div>
                </div>
                </div>
                <div class="income" >
                    <div class="title"> Balance</div>
                    <div class="holder">
                    <div><img src="icons/icons8-balance-48 1.png" alt="" class="transaction-icons"></div>
                    <div class="balance-amt">&#8360;</div>
                </div>
                </div>
            </div>
            <div class="table">
                <div class="addincome">
                    <p class="addin">Add Income</p>
                    <form action="../backend/income_insert.php" method="post">
                    <div class="box">
                       
                        <label for="category">Category</label>
                        <input list="income" placeholder="select category" name="category">
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
                    <label for="categoty">Category</label>
                    <input list="income" placeholder="select category" name="category">
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
<script>
$(document).ready(function () {
    // Fetch total income
    $.ajax({
        url: '../backend/fetch_income.php', 
        method: 'GET',
        success: function (response) {
            $('.income-amt').html("&#8360;" + response.trim());
        },
        error: function (xhr, status, error) {
            console.error("Error fetching total income:", error);
        }
    });

    // Fetch total expense
    $.ajax({
        url: '../backend/fetch_expense.php', 
        method: 'GET',
        success: function (response) {
            $('.expense-amt').html("&#8360;" + response.trim());
        },
        error: function (xhr, status, error) {
            console.error("Error fetching total expense:", error);
        }
    });
});

</script>

</body>
</html>