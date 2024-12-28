document.addEventListener("DOMContentLoaded", function () {
    // Modified error display function to safely handle DOM elements
    function showError(message) {
      const container = document.querySelector('.transactions-container');
      if (!container) {
        console.error('Transactions container not found');
        return;
      }
  
      const errorDiv = document.createElement('div');
      errorDiv.className = 'error-message';
      errorDiv.textContent = message;
      
      // Check if there's an existing error message and remove it
      const existingError = container.querySelector('.error-message');
      if (existingError) {
        existingError.remove();
      }
      
      // Insert at the beginning of the container
      container.insertBefore(errorDiv, container.firstChild);
      
      // Remove after 5 seconds
      setTimeout(() => {
        if (errorDiv && errorDiv.parentNode) {
          errorDiv.remove();
        }
      }, 5000);
    }
  
    // Modified fetch function to rely on PHP session
    async function fetchTransactions(timeFilter = "all", typeFilter = "all") {
        try {
            const response = await fetch(
                `../Backend/fetch_history.php?timeFilter=${timeFilter}&typeFilter=${typeFilter}&includeStats=true`,
                {
                    // Add credentials to ensure session cookies are sent
                    credentials: 'include'
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.status === "success") {
                if (Array.isArray(data.transactions)) {
                    displayTransactions(data.transactions);
                    if (data.stats) {
                        updateStats(data.stats);
                    }
                } else {
                    showError("Invalid transaction data received");
                }
            } else if (data.status === "unauthorized") {
                // Redirect to login page if session expired
                window.location.href = "../login.php";
            } else {
                showError(data.message || "Failed to load transactions");
            }
        } catch (error) {
            showError("Failed to fetch transactions. Please try again later.");
            console.error("Fetch error:", error);
        }
    }
  
    function displayTransactions(transactions) {
      const transactionsList = document.querySelector(".transactions-list");
      transactionsList.innerHTML = "";
  
      if (transactions.length === 0) {
        transactionsList.innerHTML =
          '<div class="no-transactions">No transactions found</div>';
        return;
      }
  
      transactions.forEach((transaction) => {
        const transactionElement = document.createElement("div");
        transactionElement.className = "transaction-item";
  
        transactionElement.innerHTML = `
                  <div class="transaction-info">
                      <div class="transaction-date">${formatDate(
                        transaction.transaction_date
                      )}</div>
                      <div class="transaction-details">
                          <div class="transaction-description">${
                            transaction.description
                            
                          }</div>
                          <div class="transaction-category">${
                            transaction.category_name || "Uncategorized"
                          }</div>
                      </div>
                  </div>
                  <div class="transaction-amount ${transaction.transaction_type}">
                      ${
                        transaction.transaction_type === "income" ? "+" : "-"
                      }${Math.abs(transaction.amount).toFixed(2)}
                  </div>
              `;
  
        transactionsList.appendChild(transactionElement);
      });
    }
  
    function updateStats(stats) {
      // Add elements to your HTML to display these stats
      if (document.getElementById("totalIncome")) {
        document.getElementById("totalIncome").textContent = `$${parseFloat(
          stats.total_income
        ).toFixed(2)}`;
        document.getElementById("totalExpense").textContent = `$${parseFloat(
          stats.total_expense
        ).toFixed(2)}`;
        document.getElementById("totalTransactions").textContent =
          stats.total_transactions;
      }
    }
  
    function formatDate(dateString) {
      return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    }
  
    // Event listeners for filters
    document.getElementById("timeFilter").addEventListener("change", function () {
      const timeFilter = this.value;
      const typeFilter = document.getElementById("typeFilter").value;
      fetchTransactions(timeFilter, typeFilter);
    });
  
    document.getElementById("typeFilter").addEventListener("change", function () {
      const timeFilter = document.getElementById("timeFilter").value;
      const typeFilter = this.value;
      fetchTransactions(timeFilter, typeFilter);
    });
  
    // Initial fetch
    fetchTransactions();
  });
  