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
                <div class="individual" id="setting">
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
        // Monthly Trend Chart
        const monthlyCtx = document.createElement('canvas');
        document.querySelector('.chart-card:nth-child(1) .chart').innerHTML = '';
        document.querySelector('.chart-card:nth-child(1) .chart').appendChild(monthlyCtx);

        // Category Breakdown Chart
        const categoryCtx = document.createElement('canvas');
        document.querySelector('.chart-card:nth-child(2) .chart').innerHTML = '';
        document.querySelector('.chart-card:nth-child(2) .chart').appendChild(categoryCtx);

        return {
            monthlyCtx,
            categoryCtx
        };
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
                            callback: value => `$${value.toLocaleString()}`
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
                    backgroundColor: [
                        '#ef4444',
                        '#f97316',
                        '#f59e0b',
                        '#10b981',
                        '#06b6d4',
                        '#6366f1'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Expenses by Category'
                    },
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `$${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    function updateProgressBar(percentage) {
        const fillElement = document.querySelector('.progress-bar .fill');
        const widthPercentage = Math.min(100, Math.max(0, 100 - percentage));
        fillElement.style.width = `${widthPercentage}%`;
        
        // Update color based on budget status
        if (percentage > 0) {
            fillElement.style.backgroundColor = '#10b981'; // Green for under budget
        } else {
            fillElement.style.backgroundColor = '#ef4444'; // Red for over budget
        }
    }

    // Main data fetch and update function
    function fetchAndUpdateDashboard() {
        $.ajax({
            url: '../backend/sum_fetch.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Update summary cards with error checking
                    try {
                        // Total Expenses
                        $('.stat-card:nth-child(1) .amount').text(`$${(data.total_expenses || 0).toLocaleString()}`);
                        $('.stat-card:nth-child(1) .trend').html(
                            `<i class="fas fa-arrow-${data.expense_change >= 0 ? 'up' : 'down'}"></i> 
                            ${Math.abs(data.expense_change)}% from last month`
                        );
                        
                        // Monthly Savings
                        $('.stat-card:nth-child(2) .amount').text(`$${(data.monthly_savings || 0).toLocaleString()}`);
                        $('.stat-card:nth-child(2) .trend').text(`${data.savings_percentage || 0}% of income`);
                        
                        // Budget Status
                        const budgetStatus = data.budget_status || 0;
                        const statusText = budgetStatus > 0 ? 'On Track' : 'Over Budget';
                        const statusColor = budgetStatus > 0 ? '#10b981' : '#ef4444';
                        
                        $('.stat-card:nth-child(4) .amount').text(statusText).css('color', statusColor);
                        $('.stat-card:nth-child(4) div:nth-child(3)').text(
                            `${Math.abs(budgetStatus)}% ${budgetStatus > 0 ? 'under' : 'over'} budget`
                        );
                        
                        // Update progress bar
                        updateProgressBar(budgetStatus);

                        // Initialize and update charts
                        const { monthlyCtx, categoryCtx } = initializeCharts();
                        
                        if (monthlyTrendChart) {
                            monthlyTrendChart.destroy();
                        }
                        if (categoryChart) {
                            categoryChart.destroy();
                        }
                        
                        monthlyTrendChart = createMonthlyTrendChart(monthlyCtx, {
                            months: data.months || [],
                            expenses: data.monthly_expenses || [],
                            income: data.monthly_income || []
                        });
                        
                        categoryChart = createCategoryChart(categoryCtx, {
                            categories: data.expense_categories || [],
                            amounts: data.category_amounts || []
                        });
                        if (data.largest_expense) {
                            $('.stat-card:nth-child(3) .amount').html(
                                `$${data.largest_expense.amount.toLocaleString()}`
                            );
                            $('.stat-card:nth-child(3) .trend').html(
                                `${data.largest_expense.category}`
                            );
                        } else {
                            $('.stat-card:nth-child(3) .amount').text('No expenses');
                            $('.stat-card:nth-child(3) .trend').text('');
                        }
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

    // Initial fetch
    fetchAndUpdateDashboard();

    // Refresh data every 5 minutes
    setInterval(fetchAndUpdateDashboard, 5 * 60 * 1000);
});
</script>
                    
    </body>
    </html>