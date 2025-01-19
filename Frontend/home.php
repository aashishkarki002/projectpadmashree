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
    // Replace your existing income form submission code with this:
$(document).ready(function() {
    $('form[action="../backend/income_insert.php"]').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const form = this;
        
        // First, validate the form
        const category = formData.get('category');
        const amount = formData.get('amount');
        const date = formData.get('date');
        
        if (!category || !amount || !date) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }

        $.ajax({
            url: '../backend/income_insert.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Income insert response:', response);
                
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        // Show success message first
                        Swal.fire({
                            title: 'Success!',
                            text: 'Income added successfully. Would you like to set a budget?',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, set budget',
                            cancelButtonText: 'No, skip'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show budget prompt
                                Swal.fire({
                                    title: 'Set Budget',
                                    html: `
                                        <div class="form-group">
                                            <label for="budget-category">Category</label>
                                            <input list="expense-categories" id="budget-category" class="swal2-input" placeholder="Select category" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="budget-amount">Budget Amount</label>
                                            <input type="number" id="budget-amount" class="swal2-input" placeholder="Enter your budget" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="budget-period">Period</label>
                                            <select id="budget-period" class="swal2-input" required>
                                                <option value="">Select period</option>
                                                <option value="monthly">Monthly</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="yearly">Yearly</option>
                                            </select>
                                        </div>
                                    `,
                                    showCancelButton: true,
                                    confirmButtonText: 'Save Budget',
                                    showLoaderOnConfirm: true,
                                    preConfirm: () => {
                                        const budgetCategory = document.getElementById('budget-category').value;
                                        const budgetAmount = document.getElementById('budget-amount').value;
                                        const budgetPeriod = document.getElementById('budget-period').value;
                                        
                                        if (!budgetCategory || !budgetAmount || !budgetPeriod) {
                                            Swal.showValidationMessage('Please fill in all fields');
                                            return false;
                                        }

                                        if (budgetAmount <= 0) {
                                            Swal.showValidationMessage('Please enter a valid amount');
                                            return false;
                                        }

                                        return $.ajax({
                                            url: '../backend/save_budget.php',
                                            method: 'POST',
                                            data: {
                                                category: budgetCategory,
                                                amount: budgetAmount,
                                                period: budgetPeriod
                                            },
                                            dataType: 'json'
                                        }).catch(error => {
                                            console.error('Budget save error:', error);
                                            throw new Error('Failed to save budget');
                                        });
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        Swal.fire('Success', 'Budget has been set successfully!', 'success')
                                        .then(() => {
                                            // Refresh the page to update all values
                                            location.reload();
                                        });
                                    }
                                });
                            } else {
                                // Just reload to show the new income
                                location.reload();
                            }
                        });

                        // Reset the form
                        form.reset();
                        
                        // Update the displayed amounts without page reload
                        $.ajax({
                            url: '../backend/fetch.php',
                            method: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (!response.error) {
                                    $('.income-amt').html("&#8360;" + response.total_income);
                                    $('.expense-amt').html("&#8360;" + response.total_expense);
                                    $('.balance-amt').html("&#8360;" + response.balance);
                                }
                            }
                        });
                    } else {
                        Swal.fire('Error', data.error || 'Failed to add income', 'error');
                    }
                } catch (e) {
                    console.error("Parse error:", e);
                    console.log('Raw response:', response);
                    Swal.fire('Error', 'Invalid server response', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Ajax error:", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                Swal.fire('Error', 'Failed to process request', 'error');
            }
        });
    });
});
</script>
<script>
    // Function to save budget
    function saveBudget(amount) {
        fetch('/backend/budget_operations.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                amount: amount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Budget saved successfully!');
                // Refresh the page or update the UI as needed
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the budget');
        });
    }

    // Function to get current budget
    function getCurrentBudget() {
        fetch('/backend/budget_operations.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update your budget display
                document.getElementById('currentBudget').textContent = data.budget;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>
</body>
</html>