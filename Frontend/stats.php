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
            <div class="dash">Statistics</div>
            
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
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(initializeCharts);

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
            fetch('../backend/chart.php')
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

                    // Draw current month pie chart
                    drawPieChart(data.currentMonth);
                    
                    // Draw monthly trend chart
                    drawMonthlyChart(data.monthly);
                })
                .catch(error => {
                    hideLoading();
                    showError(`Failed to load chart data: ${error.message}`);
                    console.error('Chart error:', error);
                });
        }

        function drawPieChart(currentMonth) {
            const chartData = [
                ['Category', 'Amount'],
                ['Income', currentMonth.income || 0],
                ['Expense', currentMonth.expense || 0]
            ];

            const data = google.visualization.arrayToDataTable(chartData);
            const options = {
                title: 'Current Month Income vs Expense',
                pieHole: 0.4,
                colors: ['#4CAF50', '#F44336'],
                legend: { position: 'bottom' },
                chartArea: { width: '90%', height: '80%' }
            };

            const chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }

        function drawMonthlyChart(monthlyData) {
            const chartData = [['Month', 'Income', 'Expense', 'Balance']];
            
            monthlyData.forEach(month => {
                chartData.push([
                    month.month,
                    month.income || 0,
                    month.expense || 0,
                    month.balance || 0
                ]);
            });

            const data = google.visualization.arrayToDataTable(chartData);
            const options = {
                title: 'Monthly Financial Trend',
                curveType: 'function',
                legend: { position: 'bottom' },
                colors: ['#4CAF50', '#F44336', '#2196F3'],
                chartArea: { width: '90%', height: '80%' },
                vAxis: { title: 'Amount' },
                hAxis: { title: 'Month' }
            };

            const chart = new google.visualization.LineChart(document.getElementById('monthly_chart_div'));
            chart.draw(data, options);
        }

        // Add click event listeners for navigation
        document.querySelectorAll('.individual').forEach(item => {
            item.addEventListener('click', function() {
                const pageId = this.id;
                window.location.href = pageId + '.php';
            });
        });
    </script>
</body>
</html>