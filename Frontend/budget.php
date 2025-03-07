<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Management</title>
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./css/budget.css">
    <style>
   
    </style>
</head>
<body>

<div class="main">
<?php
    include("header.php"); 
    ?>
    <?php
    include("sidebar.php");
    
    ?>
<div class="mid-bar">
    <div class="container">
    <div id="dateRangeSelector"></div>
        <div class="card">
            <div class="card-header">
                <h2>Add New Budget</h2>
            </div>
            <form id="budgetForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required></select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" required>
                    </div>
                </div>
                <button type="submit">Add Budget</button>
            </form>
        </div>

    

        <div class="card">
            <div class="card-header">
                <h2>Current Budgets</h2>
            </div>
            <ul id="budgetList" class="budget-list"></ul>
        </div>
    </div>
    </div>
    </div>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
    // Initialize date range selector
    let currentDateRange = {
        startDate: new Date(),
        endDate: new Date()
    };

    // Initialize categories
    loadCategories();
    
    // Load initial budgets
    loadBudgets();

    // Set up form submission handler
    setupFormHandler();

    // Set minimum date for date inputs to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').setAttribute('min', today);
    document.getElementById('end_date').setAttribute('min', today);
});

function loadCategories() {
    fetch("get_categories.php")
        .then(response => response.json())
        .then(categories => {
            let categorySelect = document.getElementById("category");
            categorySelect.innerHTML = ''; // Clear existing options
            categories.forEach(category => {
                let option = document.createElement("option");
                option.value = category.id;
                option.textContent = category.category_name;
                categorySelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error fetching categories:", error);
            showNotification("Error loading categories", "error");
        });
}

function setupFormHandler() {
    const form = document.getElementById('budgetForm');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    // Add date input validation
    startDateInput.addEventListener('change', function() {
        endDateInput.setAttribute('min', this.value);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Basic form validation
        const amount = document.getElementById('amount').value;
        if (amount <= 0) {
            showNotification("Amount must be greater than 0", "error");
            return;
        }

        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        if (endDate < startDate) {
            showNotification("End date must be after start date", "error");
            return;
        }

        const formData = new FormData(this);
        
        fetch("../backend/budget_manager.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, "success");
                loadBudgets();
                form.reset();
            } else {
                showNotification(data.message || "Error adding budget", "error");
            }
        })
        .catch(error => {
            console.error("Error submitting budget:", error);
            showNotification("Error submitting budget", "error");
        });
    });
}

function loadBudgets(startDate = null, endDate = null) {
    let url = "../backend/get_budgets.php";
    if (startDate && endDate) {
        url += `?start_date=${startDate.toISOString().split('T')[0]}&end_date=${endDate.toISOString().split('T')[0]}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(budgets => {
            updateBudgetDisplay(budgets);
        })
        .catch(error => {
            console.error("Error loading budgets:", error);
            showNotification("Error loading budgets", "error");
        });
}

function updateBudgetDisplay(budgets) {
    const budgetList = document.getElementById("budgetList");

    
    budgetList.innerHTML = "";

    let totalBudget = 0;
    let totalSpent = 0;

    budgets.forEach(budget => {
        totalBudget += parseFloat(budget.amount) || 0;
        totalSpent += parseFloat(budget.spent) || 0;

        const percentage = budget.amount > 0 ? (budget.spent / budget.amount) * 100 : 0;
        const progressClass = getProgressClass(percentage);

        const listItem = createBudgetListItem(budget, percentage, progressClass);
        budgetList.appendChild(listItem);
    });

 
}

function createBudgetListItem(budget, percentage, progressClass) {
    const listItem = document.createElement("li");
    listItem.className = "budget-item";
    
    const startDate = new Date(budget.start_date).toLocaleDateString();
    const endDate = new Date(budget.end_date).toLocaleDateString();

    listItem.innerHTML = `
        <div class="budget-item-header">
            <span class="budget-category">${budget.category_name}</span>
            <span class="budget-dates">${startDate} - ${endDate}</span>
        </div>
        <div class="budget-amount">
            <span>$${parseFloat(budget.spent).toLocaleString()} / $${parseFloat(budget.amount).toLocaleString()}</span>
            <span>${percentage.toFixed(1)}%</span>
        </div>
        <div class="progress-container">
            <div class="progress-bar ${progressClass}" 
                 style="width: ${Math.min(percentage, 100)}%;"></div>
        </div>
        <div class="budget-actions">
            <button onclick="editBudget(${budget.id})" class="edit-btn">Edit</button>
            <button onclick="deleteBudget(${budget.id})" class="delete-btn">Delete</button>
        </div>
    `;

    return listItem;
}

function updateOverallProgress(totalSpent, totalBudget, progressBar, progressText) {
    const overallPercentage = totalBudget > 0 ? (totalSpent / totalBudget) * 100 : 0;
    const progressClass = getProgressClass(overallPercentage);
    
    progressBar.style.width = `${Math.min(overallPercentage, 100)}%`;
    progressBar.className = `progress-bar ${progressClass}`;
    progressText.textContent = `Total Spent: $${totalSpent.toLocaleString()} / $${totalBudget.toLocaleString()} (${overallPercentage.toFixed(1)}%)`;
}

function getProgressClass(percentage) {
    if (percentage >= 90) return 'danger';
    if (percentage >= 75) return 'warning';
    return 'success';
}

function handleDateRangeChange(dateRange) {
    currentDateRange = dateRange;
    loadBudgets(dateRange.startDate, dateRange.endDate);
}

function editBudget(budgetId) {
    // Implement edit functionality
    fetch(`../backend/get_budget.php?id=${budgetId}`)
        .then(response => response.json())
        .then(budget => {
            document.getElementById('category').value = budget.category_id;
            document.getElementById('amount').value = budget.amount;
            document.getElementById('start_date').value = budget.start_date;
            document.getElementById('end_date').value = budget.end_date;
            
            // Add hidden input for budget ID
            let hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'budget_id';
            hiddenInput.value = budgetId;
            document.getElementById('budgetForm').appendChild(hiddenInput);
        })
        .catch(error => {
            console.error("Error fetching budget details:", error);
            showNotification("Error fetching budget details", "error");
        });
}

function deleteBudget(budgetId) {
    if (confirm("Are you sure you want to delete this budget?")) {
        fetch(`../backend/delete_budget.php`, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: budgetId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification("Budget deleted successfully", "success");
                loadBudgets(currentDateRange.startDate, currentDateRange.endDate);
            } else {
                showNotification(data.message || "Error deleting budget", "error");
            }
        })
        .catch(error => {
            console.error("Error deleting budget:", error);
            showNotification("Error deleting budget", "error");
        });
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 4px;
        color: white;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    }

    .notification.success {
        background-color: var(--success-color);
    }

    .notification.error {
        background-color: var(--danger-color);
    }

    .notification.info {
        background-color: var(--primary-color);
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .budget-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .edit-btn, .delete-btn {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    .edit-btn {
        background-color: var(--warning-color);
    }

    .delete-btn {
        background-color: var(--danger-color);
    }
`;
document.head.appendChild(style);
    </script>
    <script src="navigation.js"></script>
</body>
</html>