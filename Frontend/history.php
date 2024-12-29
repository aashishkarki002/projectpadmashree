<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/history.css">
</head>

<body>
    <div class="main">
        <div class="top-bar">
            <div class="logo">
                <img src="img/img.png" alt="" class="img">
            </div>
            <div class="profile">
                <button class="profile-trigger" onclick="toggleDropdown()">
                    <div class="profile-logo">
                        <img src="./img/profile.jpg" alt="profile">
                    </div>
                    <h1>Profile</h1>
                </button>
                <div class="dropdown-content" id="myDropdown">
                    <div class="profile-info">
                        <div class="profile-info-header">
                            <img src="./img/profile.jpg" alt="profile">
                            <span> Afno Budget</span>
                        </div>
                    </div>
                    <a href="setting.php" class="dropdown-item">Settings & privacy</a>
                    <a href="logout.php" class="dropdown-item">Log Out</a>
                </div>
            </div>
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
    </div>

    <div class="mid-bar">
        <div class="sub-mid">
            <div class="">
                <h3 class="title">Recent Transaction</h3>
                <div class="calendar">
                    <div class="">
                        <label class="calendar-title">Start Date:</label>
                        <input type="date" id="startDate" class="">
                    </div>
                    <div class="">
                        <label class="calendar-title">End Date:</label>
                        <input type="date" id="endDate" class="">
                    </div>
                    <div class="button">
                        <button onclick="fetchTransactions()" class="filter">
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <div id="loadingIndicator" class="hidden">
                <div class="">
                    <div class=""></div>
                </div>
            </div>
            
            <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>

            <div class="">
                <table class="transaction-table">
                    <thead class="">                   
                    </thead>
                    <tbody id="transactionTable" class="transacation-table"></tbody>
                </table>
            </div>

            <div class="page">
                <button id="prevButton" onclick="previousPage()" class="previous">
                    Previous
                </button>
                <span id="pageInfo" class="page-info">Page 1</span>
                <button id="nextButton" onclick="nextPage()" class="next">
                    Next
                </button>
            </div>
        </div>
    </div>
    </div>

    <script src="navigation.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        let currentPage = 1;
        let totalPages = 1;

        function showLoading(show) {
            document.getElementById('loadingIndicator').style.display = show ? 'block' : 'none';
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            if (message) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            } else {
                errorDiv.style.display = 'none';
            }
        }

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: '2-digit' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        function formatAmount(amount) {
            const sign = amount >= 0 ? '+' : '-';
            return `${sign} ${new Intl.NumberFormat('ne-IN', {
                style: 'currency',
                currency: 'NPR'
            }).format(Math.abs(amount))}`;
        }

        function formatAmount(amount) {
            return new Intl.NumberFormat('ne-IN', {
                style: 'currency',
                currency: 'NPR'
            }).format(amount);

        }

        function updatePagination() {
            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
            document.getElementById('prevButton').disabled = currentPage === 1;
            document.getElementById('nextButton').disabled = currentPage === totalPages;
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                fetchTransactions();
            }
        }

        function nextPage() {
            if (currentPage < totalPages) {
                currentPage++;
                fetchTransactions();
            }
        }

        async function fetchTransactions() {
            showLoading(true);
            showError(null);

            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            try {
                let url = `/projectpadmashree/backend/history_fetch.php?page=${currentPage}`;
                if (startDate && endDate) {
                    url += `&start_date=${startDate}&end_date=${endDate}`;
                }

                const response = await fetch(url);
                const data = await response.json();

                if (data.status === 'success') {
                    const tableBody = document.getElementById('transactionTable');
                    tableBody.innerHTML = '';

                    data.data.transactions.forEach(transaction => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';
                        const amountColor = parseFloat(transaction.amount) >= 0 ? 'text-green-600' : 'text-red-600';
                        row.innerHTML = `
                         <div class="transaction">
                        <div class="transaction-info">
                         <div class="category">${transaction.category}</div>
                            <div class="date">${formatDate(transaction.date)}</div>
                           </div>
                            <div class="amount ${amountColor} ">${formatAmount(Math.abs(transaction.amount))}</div>
                            </div>
                        `;
                        tableBody.appendChild(row);
                    });

                    totalPages = data.data.pagination.total_pages;
                    updatePagination();
                } else {
                    throw new Error(data.message || 'Failed to fetch transactions');
                }
            } catch (error) {
                showError(error.message);
            } finally {
                showLoading(false);
            }
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', fetchTransactions);

        function toggleDropdown() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        // Close dropdown when clicking outside
        window.onclick = function (event) {
            if (!event.target.matches('.profile-trigger') &&
                !event.target.matches('.profile-trigger *')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>

</html>