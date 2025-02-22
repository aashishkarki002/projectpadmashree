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
    <?php
     include"header.php"
     ?>

    <?php include 'sidebar.php'; ?> 

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

        function deleteTransaction(transactionId, transactionType) {
    // Show confirmation before deleting
    if (!confirm('Are you sure you want to delete this transaction?')) {
        return;
    }

    // Construct the request data
    const requestData = {
        transaction_id: parseInt(transactionId),
        transaction_type: transactionType
    };

    // Make the API call
    fetch('/projectpadmashree/backend/delete_transaction.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {

            const row = document.querySelector(`[data-id='${transactionId}']`);
            if (row) {
                row.remove();
            }
            fetchTransactions();
        } else {
            alert(data.message || 'Error deleting transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
       
    });
}

// Helper functions (make sure these exist in your code)
function showLoading(show) {
    const loader = document.getElementById('loadingIndicator');
    if (loader) {
        loader.style.display = show ? 'block' : 'none';
    }
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
        if (message) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        } else {
            errorDiv.style.display = 'none';
        }
    }
}


        async function fetchTransactions() {
    showLoading(true);
    showError(null);

    try {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        let url = `/projectpadmashree/backend/history_fetch.php?page=${currentPage}`;
        if (startDate && endDate) url += `&start_date=${startDate}&end_date=${endDate}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.status === 'success') {
            const tableBody = document.getElementById('transactionTable');
            tableBody.innerHTML = '';

            data.data.transactions.forEach(transaction => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                
                // Ensure `transaction.amount` is a number
                const amount = parseFloat(transaction.amount);
                const transactionType = amount >= 0 ? 'income' : 'expense';

                // Ensure `transaction.id` is correctly passed
                const transactionId = transaction.id;

                row.innerHTML = `
                    <div class="transaction">
                        <div class="transaction-info">
                            <div class="category">${transaction.category}</div>
                            <div class="date">${formatDate(transaction.date)}</div>
                        </div>
                        <div class="transaction-actions">
                            <div class="amount ${amount >= 0 ? 'text-green-600' : 'text-red-600'}">
                                ${formatAmount(Math.abs(amount))}
                            </div>
                            <button onclick="deleteTransaction(${transactionId}, '${transactionType}')" class="delete-btn">
                                <i class="fas fa-trash-alt text-red-500"></i>
                            </button>
                        </div>
                    </div>
                `;
                tableBody.appendChild(row);
                console.log("Backend response:", data);
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