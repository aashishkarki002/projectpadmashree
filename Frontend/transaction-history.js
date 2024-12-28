document.addEventListener("DOMContentLoaded", function () {
    const timeFilter = document.getElementById("timeFilter");
    const typeFilter = document.getElementById("typeFilter");
    const transactionSummary = document.getElementById("transactionSummary");

    // API Base URL
    const API_URL = "http://localhost/projectpadmashree/backend/history_fetch"; // Adjust as needed

    // Fetch Transactions Function
    async function fetchTransactions() {
        const timeValue = timeFilter.value;
        const typeValue = typeFilter.value;

        // Define the endpoint based on the type
        const endpoint = typeValue === "income"
            ? `${API_URL}/income_fetch.php`
            : typeValue === "expense"
            ? `${API_URL}/expense_fetch.php`
            : `${API_URL}/combined_fetch.php`; // Use a combined endpoint for all transactions

        // Build query parameters for time filter
        const queryParams = new URLSearchParams();
        if (timeValue === "today") {
            queryParams.set("start_date", new Date().toISOString().split("T")[0]); // Today's date
        } else if (timeValue === "week") {
            const today = new Date();
            const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
            queryParams.set("start_date", startOfWeek.toISOString().split("T")[0]);
        } else if (timeValue === "month") {
            const today = new Date();
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            queryParams.set("start_date", startOfMonth.toISOString().split("T")[0]);
        }
        // Optionally set limit (pagination)
        queryParams.set("limit", 10); // Adjust limit as needed

        try {
            // Fetch Data
            const response = await fetch(`${endpoint}?${queryParams.toString()}`);
            const data = await response.json();

            if (data.status === "success") {
                renderTransactions(data.data.transactions);
            } else {
                transactionSummary.innerHTML = `<p>${data.message || "No transactions found"}</p>`;
            }
        } catch (error) {
            console.error("Error fetching transactions:", error);
            transactionSummary.innerHTML = `<p>Error loading transactions</p>`;
        }
    }

    // Render Transactions in the DOM
    function renderTransactions(transactions) {
        if (transactions.length === 0) {
            transactionSummary.innerHTML = "<p>No transactions available</p>";
            return;
        }

        // Generate HTML for Transactions
        const transactionHTML = transactions
            .map(
                (transaction) => `
            <div class="transaction-item">
                <div class="transaction-category">${transaction.category}</div>
                <div class="transaction-amount">${transaction.amount}</div>
                <div class="transaction-date">${transaction.date}</div>
                <div class="transaction-notes">${transaction.notes || ""}</div>
            </div>
        `
            )
            .join("");

        transactionSummary.innerHTML = transactionHTML;
    }

    // Add Event Listeners
    timeFilter.addEventListener("change", fetchTransactions);
    typeFilter.addEventListener("change", fetchTransactions);

    // Initial Fetch
    fetchTransactions();
});
