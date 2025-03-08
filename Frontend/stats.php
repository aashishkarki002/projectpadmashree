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
    <title>Financial Statistics</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiko:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<style>
    /* Variables for consistent theming */
:root {
  --primary-color: #3a7bd5;
  --primary-light: #6faae1;
  --primary-dark: #2c5ea1;
  --accent-color: #00d09c;
  --text-dark: #333333;
  --text-light: #f8f9fa;
  --background-light: #ffffff;
  --background-gray: #f5f7fa;
  --border-color: #e0e0e0;
  --income-color: #4CAF50;
  --expense-color: #F44336;
  --balance-color: #2196F3;
  --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  --transition: all 0.3s ease;
  --border-radius: 8px;
}

.individual {
  padding: 10px 20px;
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: var(--transition);
  color: var(--text-dark);
  border-left: 4px solid transparent;
}

.individual:hover, .individual.active {
  background-color: rgba(58, 123, 213, 0.08);
  border-left: 4px solid var(--primary-color);
}

.individual img {
  width: 20px;
  height: 20px;
  margin-right: 12px;
}

/* Main content area */
.mid-bar {
  flex: 1;
  margin-left: 250px;
  padding: 0px 30px 30px;
  min-height: 100vh;
}

.dash {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 25px;
  color: var(--primary-dark);
  position: relative;
}

.dash::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -8px;
  width: 40px;
  height: 4px;
  background-color: var(--accent-color);
  border-radius: 10px;
}

/* Filter controls */
.filter-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 15px;
}

select#timePeriod {
  padding: 10px 15px;
  border-radius: var(--border-radius);
  border: 1px solid var(--border-color);
  background-color: var(--background-light);
  font-size: 15px;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: var(--shadow);
  outline: none;
  min-width: 150px;
}

select#timePeriod:hover, select#timePeriod:focus {
  border-color: var(--primary-light);
}

/* Chart containers */
#chart_div, #monthly_chart_div {
  background-color: var(--background-light);
  border-radius: var(--border-radius);
  padding: 20px;
  box-shadow: var(--shadow);
  margin-bottom: 30px;
  overflow: hidden;
  transition: var(--transition);
}

#chart_div:hover, #monthly_chart_div:hover {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

/* Loading and error indicators */
#loading_indicator {
  text-align: center;
  padding: 20px;
  font-weight: 600;
  color: var(--primary-color);
}

#error_display {
  background-color: rgba(244, 67, 54, 0.1);
  color: var(--expense-color);
  padding: 15px;
  border-radius: var(--border-radius);
  margin-bottom: 20px;
  font-weight: 600;
}

/* Responsive design */
@media screen and (max-width: 992px) {
  .sidebar {
    width: 200px;
  }
  
  .mid-bar {
    margin-left: 200px;
    padding: 90px 20px 20px;
  }
}

@media screen and (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
    width: 250px;
  }
  
  .sidebar.active {
    transform: translateX(0);
  }
  
  .mid-bar {
    margin-left: 0;
    padding: 90px 15px 15px;
  }
  
  /* Add mobile menu toggle */
  .menu-toggle {
    display: block;
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: var(--primary-color);
    color: var(--text-light);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    z-index: 101;
    box-shadow: var(--shadow);
  }
  
  .dash {
    font-size: 24px;
  }
}

/* Custom styles for Google Charts */
.google-visualization-tooltip {
  border-radius: var(--border-radius) !important;
  box-shadow: var(--shadow) !important;
  padding: 10px !important;
  border: none !important;
}

/* Some nice animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

#chart_div, #monthly_chart_div {
  animation: fadeIn 0.5s ease-out;
}

/* Color indicators for financial data */
.stat-card {
  display: flex;
  justify-content: space-between;
  background-color: var(--background-light);
  padding: 15px;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  margin-bottom: 20px;
}

.income-indicator {
  color: var(--income-color);
}

.expense-indicator {
  color: var(--expense-color);
}

.balance-indicator {
  color: var(--balance-color);
}
</style>
<body>
    <div class="main">
    <?php include"header.php"
     ?>

        <?php include 'sidebar.php'; ?> 

        <div class="mid-bar">
            <div class="dash">Statistics</div>
            
          
            <div style="margin-bottom: 20px;">
                <select id="timePeriod" style="padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="week">Weekly</option>
                    <option value="month" selected>Monthly</option>
                    <option value="year">Yearly</option>
                </select>
            </div>
            
            <!-- Chart containers -->
            <div id="chart_div" style="width: 100%; height: 400px;"></div>
            <div id="monthly_chart_div" style="width: 100%; height: 400px; margin-top: 20px;"></div>
            
            <!-- Loading indicator -->
            <div id="loading_indicator" style="text-align: center; display: none;">
                Loading charts...
            </div>
            
            <!-- Error display -->
            <div id="error_display" style="color: red; text-align: center; display: none;"></div>
        </div>
    </div>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Load Google Charts
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(initializeCharts);

        // Add time period variable
        let currentPeriod = 'month';

        // Add event listener for time period changes
        document.getElementById('timePeriod').addEventListener('change', function(e) {
            currentPeriod = e.target.value;
            initializeCharts();
        });

        function showError(message) {
            const errorDisplay = document.getElementById('error_display');
            errorDisplay.textContent = message;
            errorDisplay.style.display = 'block';
        }

        function showLoading() {
            document.getElementById('loading_indicator').style.display = 'block';
            document.getElementById('error_display').style.display = 'none';
        }

        function hideLoading() {
            document.getElementById('loading_indicator').style.display = 'none';
        }

        function initializeCharts() {
            showLoading();
            // Updated fetch URL to include period parameter
            fetch(`../backend/chart.php?period=${currentPeriod}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoading();
                    if (!data || typeof data !== 'object') {
                        throw new Error('Invalid data format received');
                    }

                    // Draw current period pie chart
                    drawPieChart(data.currentMonth, currentPeriod);
                    
                    // Draw trend chart
                    drawComboChart(data.monthly, currentPeriod);
                })
                .catch(error => {
                    hideLoading();
                    showError(`Failed to load chart data: ${error.message}`);
                    console.error('Chart error:', error);
                });
        }

        function drawPieChart(currentData, period) {
    if (!currentData || (currentData.income === 0 && currentData.expense === 0)) {
        document.getElementById('chart_div').innerHTML = 'No data available for income or expense for this period.';
        return; // Return early if there is no data
    }

    const chartData = [
        ['Category', 'Amount'],
        ['Income', currentData.income || 0],
        ['Expense', currentData.expense || 0]
    ];

    const data = google.visualization.arrayToDataTable(chartData);
    const options = {
        title: `Current ${period.charAt(0).toUpperCase() + period.slice(1)} Income vs Expense`,
        pieHole: 0.4,
        colors: ['#4CAF50', '#F44336'],
        legend: { position: 'bottom' },
        chartArea: { width: '90%', height: '80%' },
        tooltip: { format: 'currency', prefix: '₹' } // Added currency prefix
    };

    const chart = new google.visualization.PieChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}


function drawComboChart(trendData, period) {
    if (!trendData || trendData.length === 0) {
        document.getElementById('monthly_chart_div').innerHTML = 'No financial data available for this period.';
        return; // Return early if there is no data
    }

    const chartData = [[period.charAt(0).toUpperCase() + period.slice(1), 'Income', 'Expense', 'Net Balance']];
    
    trendData.forEach(data => {
        let label;
        const dateObj = new Date(data.month + '-01');
        
        switch(period) {
            case 'week':
                const weekNum = getWeekNumber(dateObj);
                label = `Week ${weekNum}`;
                break;
            case 'month':
                label = dateObj.toLocaleDateString('en-US', { 
                    month: 'short', 
                    year: 'numeric' 
                });
                break;
            case 'year':
                label = dateObj.getFullYear().toString();
                break;
        }
        
        chartData.push([
            label,
            data.income || 0,
            data.expense || 0,
            data.balance || 0
        ]);
    });

    const data = google.visualization.arrayToDataTable(chartData);
    const options = {
        title: `${period.charAt(0).toUpperCase() + period.slice(1)}ly Financial Overview`,
        titleTextStyle: {
            fontSize: 16,
            bold: true
        },
        colors: ['#4CAF50', '#F44336', '#2196F3'],
        chartArea: { width: '80%', height: '70%' },
        legend: { position: 'top', alignment: 'center' },
        seriesType: 'bars',
        series: {
            0: { targetAxisIndex: 0 },
            1: { targetAxisIndex: 0 },
            2: { 
                type: 'line',
                targetAxisIndex: 1,
                lineWidth: 3,
                pointSize: 7
            }
        },
        vAxes: {
            0: {
                title: 'Amount (₹)',
                format: '₹#,###', 
                gridlines: { count: 8 }
            },
            1: {
                title: 'Net Balance (₹)',
                format: '₹#,###', 
                gridlines: { count: 0 }
            }
        },
        hAxis: {
            title: period.charAt(0).toUpperCase() + period.slice(1),
            slantedText: true,
            slantedTextAngle: 45
        },
        animation: {
            startup: true,
            duration: 1000,
            easing: 'out'
        },
        bar: { groupWidth: '70%' }
    };

    const chart = new google.visualization.ComboChart(document.getElementById('monthly_chart_div'));
    chart.draw(data, options);
}

        // Helper function to get week number
        function getWeekNumber(date) {
            const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
            const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
        }

        // Navigation event listeners
        document.querySelectorAll('.individual').forEach(item => {
            item.addEventListener('click', function() {
                const pageId = this.id;
                window.location.href = pageId + '.php';
            });
        });
    </script>
</body>
</html>