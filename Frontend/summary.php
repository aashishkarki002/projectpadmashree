<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Summary Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/home.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: #f3f4f6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            color: #1f2937;
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .header p {
            color: #6b7280;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 0.875rem;
            color: #4b5563;
        }

        .stat-card .amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .stat-card .trend {
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .trend.up {
            color: #ef4444;
        }

        .trend.down {
            color: #10b981;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .chart-card h3 {
            color: #1f2937;
            margin-bottom: 15px;
        }

        .chart {
            width: 100%;
            height: 300px;
            background: #f9fafb;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
        }

        
        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            margin-top: 8px;
        }

        .progress-bar .fill {
            height: 100%;
            border-radius: 4px;
            background-color: #10b981;
            width: 85%;
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
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

                
</body>
</html>