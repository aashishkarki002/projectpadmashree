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
</head>
<body>
    <div class="main">
         <div class="top-bar">
            <div class="logo">
                <img src="img/img.png" alt="" class="img"> 
            </div>
            <div class="profile">
            <button class="profile-trigger" onclick="toggleDropdown()">
                <div class="profile-logo">
                    <img src="./img/profile.jpg" alt="profile">
                </div>
                <h1>Profile</h1>
            </button>
            <div class="dropdown-content" id="myDropdown">
                <div class="profile-info">
                    <div class="profile-info-header">
                        <img src="./img/profile.jpg" alt="profile">
                        <span>  <?php echo strtoupper( htmlspecialchars($_SESSION['firstname'])); ?></span>
                    </div>
                </div>
                <a href="setting.php" class="dropdown-item">Settings & privacy</a>
                <a href="logout.php" class="dropdown-item">Log Out</a>
            </div>
        </div>
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
                    <div class="income-amt">&#8360;0</div>
                </div>
                </div>
                <div class="income" >
                    <div class="title">Total Expense</div>
                    <div class="holder">
                    <div><img src="icons/icons8-expense-50 1.png" alt="" class="transaction-icons"></div>
                    <div class="expense-amt">&#8360;0</div>
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
<script>
$(document).ready(function () {
    $.ajax({
        url: '../backend/fetch.php',
        method: 'GET',
        dataType: 'json',  
        success: function (response) {
            if (response.error) {
                console.error("Error:", response.error);
                return;
            }
            
            
            $('.income-amt').html("&#8360;" + response.total_income);
            $('.expense-amt').html("&#8360;" + response.total_expense);
            $('.balance-amt').html("&#8360;" + response.balance);
        },
        error: function (xhr, status, error) {
            console.error("Error fetching data:", error);
        }
    });
});
function toggleDropdown() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.profile-trigger') && 
                !event.target.matches('.profile-trigger *')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    
</script>
<script>
    // Add this to your existing JavaScript code
$(document).ready(function() {
    // Intercept the income form submission
    $('form[action="../backend/income_insert.php"]').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '../backend/income_insert.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Show budget prompt modal after successful income addition
                Swal.fire({
                    title: 'Set Budget',
                    html: `
                        <input type="number" id="budget-amount" class="swal2-input" placeholder="Enter your budget">
                        <select id="budget-period" class="swal2-input">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Save Budget',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const budgetAmount = document.getElementById('budget-amount').value;
                        const budgetPeriod = document.getElementById('budget-period').value;
                        
                        return $.ajax({
                            url: '../backend/save_budget.php',
                            method: 'POST',
                            data: {
                                amount: budgetAmount,
                                period: budgetPeriod
                            }
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Success', 'Budget has been set successfully!', 'success');
                        // Refresh the page or update necessary elements
                        location.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                Swal.fire('Error', 'Failed to add income', 'error');
            }
        });
    });
});
</script>
</body>
</html>