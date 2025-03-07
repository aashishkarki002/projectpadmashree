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
                    <div class="amount">0</div>
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
                    <div class="amount">0</div>
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
                    <div class="amount" style="color: #10b981;"></div>
                    <div></div>
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

    function calculatePercentageChange(currentValue, previousValue) {
        if (!previousValue || previousValue === 0) return { value: 0, isIncrease: false };
        
        const change = ((currentValue - previousValue) / previousValue) * 100;
        return {
            value: Math.abs(change.toFixed(1)),
            isIncrease: change > 0
        };
    }

    function createMonthlyTrendChart(ctx, data) {
        // Create datasets array with expenses and income
        const datasets = [
            {
                label: 'Expenses',
                data: data.expenses,
                borderColor: '#ef4444',
                tension: 0.4,
                fill: false
            }, 
            {
                label: 'Income',
                data: data.income,
                borderColor: '#10b981',
                tension: 0.4,
                fill: false
            }
        ];
        
        // Add budget dataset if available
        if (data.budget && data.budget.length > 0) {
            datasets.push({
                label: 'Budget',
                data: data.budget,
                borderColor: '#6366f1',
                borderDash: [5, 5],
                tension: 0.1,
                fill: false
            });
        }
        
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.months,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: data.budget && data.budget.length > 0 ? 
                              'Monthly Income vs Expenses vs Budget' : 
                              'Monthly Income vs Expenses'
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

    function updateBudgetStatus(totalSpent, totalBudget) {
        // Check if the budget card exists
        if ($('.stat-card:nth-child(4)').length === 0) {
            console.log("Budget card not found in the DOM");
            return;
        }
        
        // Default values if no budget is set
        let statusText = "No Budget Set";
        let statusColor = "#6366f1"; // Purple if no budget
        let budgetPercentage = 0;
        let statusDescription = "Set a budget to track progress";
        
        if (totalBudget && totalBudget > 0) {
            const usedPercentage = (totalSpent / totalBudget) * 100;
            budgetPercentage = Math.min(usedPercentage, 100); // Cap at 100% for the progress bar
            
            if (usedPercentage <= 85) {
                statusText = "On Track";
                statusColor = "#10b981"; // Green
                const underBudget = Math.round((1 - (totalSpent / totalBudget)) * 100);
                statusDescription = `${underBudget}% under budget`;
            } else if (usedPercentage <= 100) {
                statusText = "Approaching Limit";
                statusColor = "#f59e0b"; // Amber
                statusDescription = `${Math.round(usedPercentage)}% of budget used`;
            } else {
                statusText = "Over Budget";
                statusColor = "#ef4444"; // Red
                const overBudget = Math.round(((totalSpent / totalBudget) - 1) * 100);
                statusDescription = `${overBudget}% over budget`;
            }
        }
        
        // Update the budget status card
        $('.stat-card:nth-child(4) .amount').text(statusText).css('color', statusColor);
        $('.stat-card:nth-child(4) div:nth-child(3)').text(statusDescription);
        
        // Check if progress bar exists
        if ($('.stat-card:nth-child(4) .progress-bar .fill').length > 0) {
            // Update progress bar
            $('.stat-card:nth-child(4) .progress-bar .fill').css({
                'width': `${budgetPercentage}%`,
                'background-color': statusColor
            });
        }
    }

    function fetchAndUpdateDashboard() {
        // First fetch the regular summary data
        $.ajax({
            url: '../backend/sum_fetch.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    try {
                        // Update expense statistics
                        $('.stat-card:nth-child(1) .amount').text(formatToNPR(data.total_expenses || 0));
                        
                        // Update monthly change percentage dynamically
                        if (data.monthly_expenses && data.monthly_expenses.length >= 2) {
                            const currentMonth = data.monthly_expenses[data.monthly_expenses.length - 1];
                            const previousMonth = data.monthly_expenses[data.monthly_expenses.length - 2];
                            
                            const change = calculatePercentageChange(currentMonth, previousMonth);
                            
                            const trendElement = $('.stat-card:nth-child(1) .trend');
                            trendElement.removeClass('up down').addClass(change.isIncrease ? 'up' : 'down');
                            
                            const arrowIcon = change.isIncrease ? 
                                '<i class="fas fa-arrow-up"></i>' : 
                                '<i class="fas fa-arrow-down"></i>';
                                
                            trendElement.html(`${arrowIcon} ${change.value}% from last month`);
                        } else {
                            $('.stat-card:nth-child(1) .trend').html('<i class="fas fa-minus"></i> No previous data');
                        }
                        
                        // Update monthly savings
                        $('.stat-card:nth-child(2) .amount').text(formatToNPR(data.monthly_savings || 0));
                        
                        // Calculate and update savings percentage of income
                        if (data.monthly_income && data.monthly_income.length > 0 && data.monthly_savings) {
                            const currentMonthIncome = data.monthly_income[data.monthly_income.length - 1];
                            
                            if (currentMonthIncome && currentMonthIncome > 0) {
                                const savingsPercentage = ((data.monthly_savings / currentMonthIncome) * 100).toFixed(1);
                                
                                const savingsTrendElement = $('.stat-card:nth-child(2) .trend');
                                
                                // Determine if savings percentage is above target (30%)
                                const isAboveTarget = parseFloat(savingsPercentage) >= 30;
                                
                                savingsTrendElement.removeClass('up down').addClass(isAboveTarget ? 'up' : 'down');
                                
                                const arrowIcon = isAboveTarget ? 
                                    '<i class="fas fa-arrow-up"></i>' : 
                                    '<i class="fas fa-arrow-down"></i>';
                                    
                                savingsTrendElement.html(`${arrowIcon} ${savingsPercentage}% of income`);
                            } else {
                                $('.stat-card:nth-child(2) .trend').html('<i class="fas fa-minus"></i> No income data');
                            }
                        } else {
                            $('.stat-card:nth-child(2) .trend').html('<i class="fas fa-minus"></i> No income data');
                        }

                        if (data.largest_expense) {
                            $('.stat-card:nth-child(3) .amount').html(formatToNPR(data.largest_expense.amount));
                            $('.stat-card:nth-child(3) .trend').html(data.largest_expense.category);
                        } else {
                            $('.stat-card:nth-child(3) .amount').text('No expenses');
                            $('.stat-card:nth-child(3) .trend').text('');
                        }

                        // Now fetch the budget data
                        $.ajax({
                            url: '../backend/budget_status.php',
                            method: 'GET',
                            dataType: 'json',
                            success: function(budgetData) {
                                // Initialize the charts with combined data
                                const { monthlyCtx, categoryCtx } = initializeCharts();

                                if (monthlyTrendChart) monthlyTrendChart.destroy();
                                if (categoryChart) categoryChart.destroy();

                                // Create monthly budget array if budget data is available
                                let monthlyBudgetData = [];
                                
                                if (budgetData && budgetData.success && budgetData.total_budget > 0) {
                                    // Update budget card if it exists
                                    updateBudgetStatus(budgetData.total_spent || 0, budgetData.total_budget || 0);
                                    
                                    // If we have detailed monthly budget data, use it
                                    if (budgetData.monthly_budget && budgetData.monthly_budget.length > 0) {
                                        monthlyBudgetData = budgetData.monthly_budget;
                                    } else {
                                        // Otherwise, distribute total budget evenly across months
                                        const monthlyBudgetAmount = budgetData.total_budget / data.months.length;
                                        monthlyBudgetData = Array(data.months.length).fill(monthlyBudgetAmount);
                                    }
                                }

                                monthlyTrendChart = createMonthlyTrendChart(monthlyCtx, {
                                    months: data.months || [],
                                    expenses: data.monthly_expenses || [],
                                    income: data.monthly_income || [],
                                    budget: monthlyBudgetData
                                });

                                categoryChart = createCategoryChart(categoryCtx, {
                                    categories: data.expense_categories || [],
                                    amounts: data.category_amounts || []
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error("Error fetching budget data:", error);
                                console.log("Server response:", xhr.responseText);
                                
                                // If budget data fetch fails, still update charts with available data
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
                            }
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
    
    // Set interval to refresh dashboard
    const refreshInterval = setInterval(fetchAndUpdateDashboard, 5 * 60 * 1000);
    
    // Listen for budget updates from other pages/tabs
    window.addEventListener('storage', function(e) {
        if (e.key === 'budgetUpdated') {
            console.log("Budget updated in another tab, refreshing dashboard...");
            fetchAndUpdateDashboard();
        }
    });
});

</script>
                    
    </body>
    </html>