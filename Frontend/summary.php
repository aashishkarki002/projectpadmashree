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
        
    <?php
     include"header.php"
     ?>
        
            <?php include 'sidebar.php'; ?> 
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
                    <div class="amount"></div>
                    <div class="trend"></div>
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
   
    </script>
<script>
$(document).ready(function() {
    // Initialize charts
    let monthlyTrendChart;
    let categoryChart;

    function initializeCharts() {
        const monthlyCtx = document.createElement('canvas');
        document.querySelector('.chart-card:nth-child(1) .chart').innerHTML = '';
        document.querySelector('.chart-card:nth-child(1) .chart').appendChild(monthlyCtx);

        const categoryCtx = document.createElement('canvas');
        document.querySelector('.chart-card:nth-child(2) .chart').innerHTML = '';
        document.querySelector('.chart-card:nth-child(2) .chart').appendChild(categoryCtx);

        return { monthlyCtx, categoryCtx };
    }

    function formatToNPR(value) {
        return `रू${value.toLocaleString()}`;
    }

    function createMonthlyTrendChart(ctx, data) {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.months,
                datasets: [{
                    label: 'Expenses',
                    data: data.expenses,
                    borderColor: '#ef4444',
                    tension: 0.4,
                    fill: false
                }, {
                    label: 'Income',
                    data: data.income,
                    borderColor: '#10b981',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Income vs Expenses'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => formatToNPR(value)
                        }
                    }
                }
            }
        });
    }

    function createCategoryChart(ctx, data) {
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.categories,
                datasets: [{
                    data: data.amounts,
                    backgroundColor: ['#ef4444', '#f97316', '#f59e0b', '#10b981', '#06b6d4', '#6366f1']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Expenses by Category' },
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${formatToNPR(value)} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    function fetchAndUpdateDashboard() {
        $.ajax({
            url: '../backend/sum_fetch.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    try {
                        $('.stat-card:nth-child(1) .amount').text(formatToNPR(data.total_expenses || 0));
                        $('.stat-card:nth-child(2) .amount').text(formatToNPR(data.monthly_savings || 0));

                        if (data.largest_expense) {
                            $('.stat-card:nth-child(3) .amount').html(formatToNPR(data.largest_expense.amount));
                            $('.stat-card:nth-child(3) .trend').html(data.largest_expense.category);
                        } else {
                            $('.stat-card:nth-child(3) .amount').text('No expenses');
                            $('.stat-card:nth-child(3) .trend').text('');
                        }

                        const { monthlyCtx, categoryCtx } = initializeCharts();

                        if (monthlyTrendChart) monthlyTrendChart.destroy();
                        if (categoryChart) categoryChart.destroy();

                        monthlyTrendChart = createMonthlyTrendChart(monthlyCtx, {
                            months: data.months || [],
                            expenses: data.monthly_expenses || [],
                            income: data.monthly_income || []
                        });

                        categoryChart = createCategoryChart(categoryCtx, {
                            categories: data.expense_categories || [],
                            amounts: data.category_amounts || []
                        });
                    } catch (err) {
                        console.error("Error updating dashboard:", err);
                    }
                } else {
                    console.error("Data fetch was not successful:", data);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
                console.log("Server response:", xhr.responseText);
            }
        });
    }

    fetchAndUpdateDashboard();
    setInterval(fetchAndUpdateDashboard, 5 * 60 * 1000);
});

</script>
                    
    </body>
    </html>