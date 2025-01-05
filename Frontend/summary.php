    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Expense Summary Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/summary.css">
        
        
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
                <div class="dash">Settings</div>
        
                    
        <div class="container">
            <div class="header">
                <h1>Financial Summary</h1>
                <p>Your personal expense overview</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="card-header">
                        <h3>Total Expenses</h3>
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="amount">$2,300</div>
                    <div class="trend up">
                        <i class="fas fa-arrow-up"></i>
                        +12% from last month
                    </div>
                </div>

                <div class="stat-card">
                    <div class="card-header">
                        <h3>Monthly Savings</h3>
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <div class="amount">$900</div>
                    <div class="trend down">
                        <i class="fas fa-arrow-down"></i>
                        30% of income
                    </div>
                </div>

                <div class="stat-card">
                    <div class="card-header">
                        <h3>Largest Expense</h3>
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="amount">Housing</div>
                    <div class="trend">$1,200 this month</div>
                </div>

                <div class="stat-card">
                    <div class="card-header">
                        <h3>Budget Status</h3>
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="amount" style="color: #10b981;">On Track</div>
                    <div>15% under budget</div>
                    <div class="progress-bar">
                        <div class="fill"></div>
                    </div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-card">
                    <h3>Monthly Trend</h3>
                    <div class="chart">
                        <p>Monthly expenses and income chart</p>
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Expenses by Category</h3>
                    <div class="chart">
                        <p>Category breakdown chart</p>
                    </div>
                </div>
            </div>

        
                </div>
            </div>
        </div>
        <script src="navigation.js" type="text/javascript"></script>
    

    <canvas id="categoryChart" width="400" height="400"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function() {
        $.ajax({
            url: '../backend/sum_fetch.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // Bar Chart for Income vs Expenses
                const ctxTrend = document.getElementById('trendChart').getContext('2d');
                new Chart(ctxTrend, {
                    type: 'bar',
                    data: {
                        labels: Array.from({length: data.incomeData.length}, (_, i) => `Month ${i + 1}`),
                        datasets: [
                            {
                                label: 'Income',
                                data: data.incomeData,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Expenses',
                                data: data.expenseData,
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Pie Chart for Expense Categories
                const ctxCategory = document.getElementById('categoryChart').getContext('2d');
                new Chart(ctxCategory, {
                    type: 'pie',
                    data: {
                        labels: data.categoryLabels,
                        datasets: [{
                            data: data.categoryData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching summary data:", error);
            }
        });
    });
    </script>
<script>
    $(document).ready(function() {
    $.ajax({
        url: '../backend/sum_fetch.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                // Update the "Largest Expense" card
                $('.stat-card:nth-child(3) .amount').text(`$${data.largest_amount}`);
            } else {
                console.error("Error fetching largest expense:", data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX error:", error);
        }
    });
});
</script>
                    
    </body>
    </html>