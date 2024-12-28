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
            <div class="dash">Statsitics</div>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
 // Load Google Charts
 google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        // Function to draw the chart
        function drawChart() {
            // Fetch data from PHP file
            fetch('data.php')
                .then(response => response.json())
                .then(jsonData => {
                    var data = google.visualization.arrayToDataTable(jsonData);

                    var options = {
                        title: 'Income and Expense Summary',
                        pieHole: 0.4, // For Donut Chart
                        colors: ['#4CAF50', '#F44336'] // Green for income, red for expense
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                    chart.draw(data, options);
                })
                .catch(error => console.error('Error loading chart data:', error));
        }       
    </script>
                <div id="chart_div" style="width: 800px; height: 500px;"></div>
            </body>
            <script src="navigation.js" type="text/javascript">
                
            </script>
            </html>