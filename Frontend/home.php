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
                    <div class="balance-amt">&#8360;150000</div>
                </div>
                </div>
            </div>
            <div class="additions">
<div class="table" >
    <div class="table-title">
<div class="sub-title">Income</div>
<div id="expense-title">Expense</div>
</div>
<div class="tags">tags</div>
<div class="amount-holder">
    <div class="dropdown">
        <input list="types" placeholder="choose a existing tags or add new" />
        <datalist id="types">
          <option value="personal expense">
          <option value="rent">
          <option value="food">
          <option value="entertainment">
        </datalist>
        </div>
        <div class="amt">
</div>
<input type="number" placeholder="amount">
</div>
<div class="note">
    <div><input type="textarea" name="" id=""></div>
    <div><button class="add-btn" >add income</button></div>
</div>

</div>

</div>

<div>


</div>
  </div>
               
    </div>
    <script>
const individual = document.getElementsByClassName("individual");


    </script>
</body>
</html>