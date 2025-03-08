<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['firstname'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit(); // Ensure no further code is executed after the redirect
}
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Expense Summary Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/summary.css">
        <style>
            .time-period-selector {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                margin-bottom: 15px;
            }
            
            .time-period-selector label {
                margin-right: 10px;
                font-weight: 500;
            }
            
            .time-period-selector select {
                padding: 8px 12px;
                border-radius: 5px;
                border: 1px solid #ddd;
                background-color: white;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s ease;
                min-width: 120px;
            }
            
            .time-period-selector select:hover {
                border-color: #6366f1;
            }
            
            .time-period-selector select:focus {
                outline: none;
                border-color: #6366f1;
                box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
            }
        </style>
    </head>
    <body>
    <div class="main">
        
    <?php
     include"header.php"
     ?>
        
            <?php include 'sidebar.php'; ?> 
            <div class="mid-bar">
               
                    
        <div class="container">
            <div class="header">
                <h1>Financial Summary</h1>
                <p>Your personal expense overview</p>
                
                <div class="time-period-selector">
                    <label for="timePeriod">View by:</label>
                    <select id="timePeriod" name="timePeriod">
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
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
    let currentTimePeriod = 'monthly'; // Default time period

    // Event listener for time period dropdown
    $('#timePeriod').change(function() {
        currentTimePeriod = $(this).val();
        fetchAndUpdateDashboard();
        
        // Update chart title based on selected time period
        const trendChartTitle = $('.chart-card:nth-child(1) h3');
        if (currentTimePeriod === 'weekly') {
            trendChartTitle.text('Weekly Trend');
        } else if (currentTimePeriod === 'monthly') {
            trendChartTitle.text('Monthly Trend');
        } else if (currentTimePeriod === 'yearly') {
            trendChartTitle.text('Yearly Trend');
        }
    });

    function initializeCharts() {
        const monthlyCtx = document.createElement('canvas');
        document.querySelector('.chart-card:nth-child(1) .chart').innerHTML = '';
        document.querySelector('.chart-card:nth-child(1) .chart').appendChild(monthlyCtx);

        const categoryCtx = document.createElement('canvas');
        document.querySelector('.chart-card:nth-child(2) .chart').innerHTML = '';
        document.querySelector('.chart-card:nth-child(2) .chart').appendChild(categoryCtx);

        return { monthlyCtx, categoryCtx };
    }

    // Function to format values in Nepali Rupees (NPR)
    function formatToNPR(value) {
        return `NPR ${value.toLocaleString('ne-NP')}`;
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
        // Get the appropriate title based on time period
        let chartTitle = 'Monthly Income vs Expenses';
        if (currentTimePeriod === 'weekly') {
            chartTitle = 'Weekly Income vs Expenses';
        } else if (currentTimePeriod === 'yearly') {
            chartTitle = 'Yearly Income vs Expenses';
        }
        
        if (data.budget && data.budget.length > 0) {
            chartTitle += ' vs Budget';
        }
        
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
                        text: chartTitle
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatToNPR(value);
                            }
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
                    title: { 
                        display: true, 
                        text: `Expenses by Category (${currentTimePeriod.charAt(0).toUpperCase() + currentTimePeriod.slice(1)})` 
                    },
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

    function updateBudgetStatus(budgetData) {
        // Check if the budget card exists
        if ($('.stat-card:nth-child(4)').length === 0) {
            console.log("Budget card not found in the DOM");
            return;
        }
        console.log("Budget data:", budgetData);
        // Get budget values
        const totalSpent = budgetData.total_spent || 0;
        const totalBudget = budgetData.total_budget || 0;
        console.log("Total spent:", totalSpent);
        console.log("Total budget:", totalBudget);  
        // Default values if no budget is set
        let statusText = "No Budget Set";
        let statusColor = "#6366f1"; // Purple if no budget
        let budgetPercentage = 0;
        let statusDescription = "Set a budget to track progress";
        
        if (totalBudget && totalBudget > 0) {
            const usedPercentage = (totalSpent / totalBudget) * 100;
            budgetPercentage = Math.min(usedPercentage, 100); // Cap at 100% for the progress bar
            console.log("Used percentage:", usedPercentage);
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
        
        // Update progress bar
        if ($('.stat-card:nth-child(4) .progress-bar .fill').length > 0) {
            $('.stat-card:nth-child(4) .progress-bar .fill').css({
                'width': `${budgetPercentage}%`,
                'background-color': statusColor
            });
        }
    }

    // New function to fetch budget data separately
    function fetchBudgetData() {
        $.ajax({
            url: '../backend/budget_status.php',
            method: 'GET',
            data: { time_period: currentTimePeriod },
            dataType: 'json',
            success: function(budgetData) {
                if (budgetData.success) {
                    console.log("Budget data received:", budgetData);
                    
                    // Update budget status with the received data
                    updateBudgetStatus(budgetData);
                    
                    // If you want to add budget data to the monthly chart
                    if (monthlyTrendChart && budgetData.total_budget > 0) {
                        // Calculate monthly budget (total budget divided by number of months)
                        const monthCount = monthlyTrendChart.data.labels.length;
                        const monthlyBudget = budgetData.total_budget / monthCount;
                        
                        // Check if budget dataset already exists
                        let budgetDatasetIndex = -1;
                        monthlyTrendChart.data.datasets.forEach((dataset, index) => {
                            if (dataset.label === 'Budget') {
                                budgetDatasetIndex = index;
                            }
                        });
                        
                        // Create or update budget dataset
                        if (budgetDatasetIndex === -1) {
                            // Add new dataset
                            monthlyTrendChart.data.datasets.push({
                                label: 'Budget',
                                data: Array(monthCount).fill(monthlyBudget),
                                borderColor: '#6366f1',
                                borderDash: [5, 5],
                                tension: 0.1,
                                fill: false
                            });
                        } else {
                            // Update existing dataset
                            monthlyTrendChart.data.datasets[budgetDatasetIndex].data = 
                                Array(monthCount).fill(monthlyBudget);
                        }
                        
                        monthlyTrendChart.update();
                    }
                } else {
                    console.error("Budget data fetch was not successful:", budgetData);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching budget data:", error);
                console.log("Server response:", xhr.responseText);
            }
        });
    }

    function fetchAndUpdateDashboard() {
        // Update card titles based on time period
        updateCardTitles();
        
        // Fetch data from sum_fetch.php with time period parameter
        $.ajax({
            url: '../backend/sum_fetch.php',
            method: 'GET',
            data: { time_period: currentTimePeriod },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    try {
                        console.log("Dashboard data received:", data);
                        
                        // Update expense statistics with NPR formatting
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
                                
                            // Change trend text based on time period
                            let periodText = "month";
                            if (currentTimePeriod === 'weekly') {
                                periodText = "week";
                            } else if (currentTimePeriod === 'yearly') {
                                periodText = "year";
                            }
                            
                            trendElement.html(`${arrowIcon} ${change.value}% from last ${periodText}`);
                        } else {
                            $('.stat-card:nth-child(1) .trend').html('<i class="fas fa-minus"></i> No previous data');
                        }
                        
                        // Update monthly savings with NPR formatting
                        $('.stat-card:nth-child(2) .amount').text(formatToNPR(data.monthly_savings || 0));
                        
                        // Calculate and update savings percentage of income
                        if (data.total_income && data.total_income > 0 && data.monthly_savings) {
                            const savingsPercentage = ((data.monthly_savings / data.total_income) * 100).toFixed(1);
                            
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

                        // Update largest expense with NPR formatting
                        if (data.largest_expense) {
                            $('.stat-card:nth-child(3) .amount').html(formatToNPR(data.largest_expense.amount));
                            $('.stat-card:nth-child(3) .trend').html(data.largest_expense.category);
                        } else {
                            $('.stat-card:nth-child(3) .amount').text('No expenses');
                            $('.stat-card:nth-child(3) .trend').text('');
                        }

                        // Initialize the charts
                        const { monthlyCtx, categoryCtx } = initializeCharts();

                        if (monthlyTrendChart) monthlyTrendChart.destroy();
                        if (categoryChart) categoryChart.destroy();

                        monthlyTrendChart = createMonthlyTrendChart(monthlyCtx, {
                            months: data.months || [],
                            expenses: data.monthly_expenses || [],
                            income: data.monthly_income || [],
                            budget: [] // This will be updated by fetchBudgetData if needed
                        });

                        categoryChart = createCategoryChart(categoryCtx, {
                            categories: data.expense_categories || [],
                            amounts: data.category_amounts || []
                        });
                        
                        // Fetch budget data after initializing charts
                        fetchBudgetData();
                        
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

    function updateCardTitles() {
        // Update card titles based on the selected time period
        let expenseTitle = "Total Expenses";
        let savingsTitle = "Monthly Savings";
        let periodText = "";
        
        if (currentTimePeriod === 'weekly') {
            expenseTitle = "Weekly Expenses";
            savingsTitle = "Weekly Savings";
            periodText = "Week";
        } else if (currentTimePeriod === 'monthly') {
            expenseTitle = "Monthly Expenses";
            savingsTitle = "Monthly Savings";
            periodText = "Month";
        } else if (currentTimePeriod === 'yearly') {
            expenseTitle = "Yearly Expenses";
            savingsTitle = "Yearly Savings";
            periodText = "Year";
        }
        
        $('.stat-card:nth-child(1) .card-header h3').text(expenseTitle);
        $('.stat-card:nth-child(2) .card-header h3').text(savingsTitle);
        $('.stat-card:nth-child(3) .card-header h3').text(`Largest Expense (${periodText})`);
        $('.stat-card:nth-child(4) .card-header h3').text(`Budget Status (${periodText})`);
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