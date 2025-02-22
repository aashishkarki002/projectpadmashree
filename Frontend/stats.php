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
<body>
    <div class="main">
    <?php
     include"header.php"
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
            const chartData = [[period.charAt(0).toUpperCase() + period.slice(1), 'Income', 'Expense', 'Net Balance']];
            
            trendData.forEach(data => {
                // Format date label based on period
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