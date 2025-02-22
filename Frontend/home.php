<?php

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
                    <div class="balance-amt">&#8360;0</div>
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
$(document).ready(function() {
    // Initial load of account summary
    updateAccountSummary();

    // Income form submission
    $('form[action="../backend/income_insert.php"]').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const form = this;
        
        // Validate the form
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
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        // Show success message without budget prompt
                        Swal.fire({
                            title: 'Success!',
                            text: 'Income added successfully',
                            icon: 'success'
                        }).then(() => {
                            // Reset the form
                            form.reset();
                            
                            // Update the displayed amounts without page reload
                            updateAccountSummary();
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

    // Expense form submission
    $('form[action="../backend/expense_insert.php"]').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const form = this;
        
        // Validate the form
        const category = formData.get('category');
        const amount = formData.get('amount');
        const date = formData.get('date');
        
        if (!category || !amount || !date) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }

        $.ajax({
            url: '../backend/expense_insert.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Expense added successfully',
                            icon: 'success'
                        }).then(() => {
                            // Reset the form
                            form.reset();
                            
                            // Update the displayed amounts without page reload
                            updateAccountSummary();
                        });
                    } else {
                        Swal.fire('Error', data.error || 'Failed to add expense', 'error');
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
    
    // Helper function to update account summary
    function updateAccountSummary() {
        $.ajax({
            url: '../backend/fetch.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (!response.error) {
                    $('.income-amt').html("&#8360;" + response.total_income);
                    $('.expense-amt').html("&#8360;" + response.total_expense);
                    $('.balance-amt').html("&#8360;" + response.balance);
                } else {
                    console.error("Error in response:", response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }
});
</script>
</body>
</html>