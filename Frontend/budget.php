<?php include "header.php"; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Management</title>
    <link rel="stylesheet" href="./css/home.css">
    <style>
        
        :root {
            --primary-color: #2563eb;
            --success-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-color: #1e293b;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: var(--card-background);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        h2 {
            color: var(--text-color);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        label {
            font-weight: 500;
            color: var(--text-color);
        }

        input, select {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #1d4ed8;
        }

        .progress-container {
            background-color: #f1f5f9;
            border-radius: 0.5rem;
            overflow: hidden;
            height: 1rem;
            margin: 0.5rem 0;
        }

        .progress-bar {
            height: 100%;
            background-color: var(--success-color);
            transition: width 0.5s ease-in-out;
            border-radius: 0.5rem;
            position: relative;
        }

        .progress-bar.warning {
            background-color: var(--warning-color);
        }

        .progress-bar.danger {
            background-color: var(--danger-color);
        }

        .budget-list {
            list-style: none;
            display: grid;
            gap: 1rem;
        }

        .budget-item {
            background: var(--card-background);
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
        }

        .budget-item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .budget-category {
            font-weight: 600;
        }

        .budget-dates {
            color: #64748b;
            font-size: 0.875rem;
        }

        .budget-amount {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .overall-progress {
            margin-top: 1rem;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="main">
<?php include "sidebar.php"; ?>

<div class="mid-bar">
    <div class="container">
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
                <h2>Overall Budget Progress</h2>
            </div>
            <div class="progress-container">
                <div class="progress-bar" id="overallProgressBar" style="width: 0%;">
                    <span class="progress-text"></span>
                </div>
            </div>
            <p class="overall-progress" id="overallProgressText"></p>
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
    fetch("get_categories.php")
        .then(response => response.json())
        .then(categories => {
            let categorySelect = document.getElementById("category");
            categories.forEach(category => {
                let option = document.createElement("option");
                option.value = category.id;
                option.textContent = category.category_name;
                categorySelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error fetching categories:", error));
    })
    document.getElementById('budgetForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("../backend/budget_manager.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            loadBudgets(); // Reload budget list
            this.reset(); // Reset form fields
        }
    })
    .catch(error => console.error("Error submitting budget:", error));
});


      
        function loadBudgets() {
            fetch("../backend/get_budgets.php")
                .then(response => response.json())
                .then(budgets => {
                    const budgetList = document.getElementById("budgetList");
                    const overallProgressBar = document.getElementById("overallProgressBar");
                    const overallProgressText = document.getElementById("overallProgressText");
                    budgetList.innerHTML = "";

                    let totalBudget = 0;
                    let totalSpent = 0;

                    budgets.forEach(budget => {
                        totalBudget += parseFloat(budget.amount) || 0;
                        totalSpent += parseFloat(budget.spent) || 0;

                        const percentage = budget.amount > 0 ? (budget.spent / budget.amount) * 100 : 0;
                        const progressClass = percentage >= 90 ? 'danger' : 
                                           percentage >= 75 ? 'warning' : 
                                           'success';

                        const listItem = document.createElement("li");
                        listItem.className = "budget-item";
                        listItem.innerHTML = `
                            <div class="budget-item-header">
                                <span class="budget-category">${budget.category_name}</span>
                                <span class="budget-dates">${budget.start_date} - ${budget.end_date}</span>
                            </div>
                            <div class="budget-amount">
                                <span>$${budget.spent.toLocaleString()} / $${budget.amount.toLocaleString()}</span>
                                <span>${percentage.toFixed(1)}%</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar ${progressClass}" 
                                     style="width: ${Math.min(percentage, 100)}%;"></div>
                            </div>
                        `;
                        budgetList.appendChild(listItem);
                    });

                    const overallPercentage = totalBudget > 0 ? (totalSpent / totalBudget) * 100 : 0;
                    const progressClass = overallPercentage >= 90 ? 'danger' : 
                                       overallPercentage >= 75 ? 'warning' : 
                                       'success';
                    
                    overallProgressBar.style.width = `${Math.min(overallPercentage, 100)}%`;
                    overallProgressBar.className = `progress-bar ${progressClass}`;
                    overallProgressText.textContent = `Total Spent: $${totalSpent.toLocaleString()} / $${totalBudget.toLocaleString()} (${overallPercentage.toFixed(1)}%)`;
                })
                .catch(error => console.error("Error loading budgets:", error));
        }

        loadBudgets();

        // Form submission handling
        document.getElementById('budgetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add your form submission logic here
            loadBudgets(); // Reload budgets after submission
        });
    </script>
    <script src="navigation.js"></script>
</body>
</html>