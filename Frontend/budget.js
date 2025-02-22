class BudgetTracker {
    constructor() {
        this.budgetItems = [];
        this.apiUrl = '../backend/budget_manager.php'; // Update this to match your PHP file location
        this.initializeElements();
        this.attachEventListeners();
        this.loadBudgetItems();
    }

    initializeElements() {
        this.categorySelect = document.getElementById('category');
        this.amountInput = document.getElementById('amount');
        this.addButton = document.getElementById('addButton');
        this.budgetItemsContainer = document.getElementById('budgetItems');
        this.totalBudgetElement = document.getElementById('totalBudget');
        this.totalSpentElement = document.getElementById('totalSpent');
        this.totalProgressElement = document.getElementById('totalProgress');
        this.timePeriod = document.getElementById('timePeriod');
    }

    attachEventListeners() {
        this.addButton.addEventListener('click', () => this.addBudgetItem());
        this.timePeriod.addEventListener('change', () => this.loadBudgetItems());
    }

    async loadBudgetItems() {
        try {
            const response = await fetch(`${this.apiUrl}?timePeriod=${this.timePeriod.value}`);
            this.budgetItems = await response.json();
            this.render();
        } catch (error) {
            console.error('Error loading budget items:', error);
        }
    }

    async addBudgetItem() {
        const category = this.categorySelect.value;
        const budget = parseFloat(this.amountInput.value);
        const timePeriod = this.timePeriod.value;

        if (category && budget > 0) {
            try {
                const response = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        category,
                        budget,
                        timePeriod
                    })
                });

                const newItem = await response.json();
                this.budgetItems.push(newItem);
                this.categorySelect.value = '';
                this.amountInput.value = '';
                this.render();
            } catch (error) {
                console.error('Error adding budget item:', error);
            }
        }
    }

    async updateSpent(id, spent) {
        try {
            await fetch(this.apiUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id,
                    spent: parseFloat(spent)
                })
            });

            const item = this.budgetItems.find(item => item.id === id);
            if (item) {
                item.spent = Math.min(parseFloat(spent) || 0, item.budget);
                this.render();
            }
        } catch (error) {
            console.error('Error updating spent amount:', error);
        }
    }

    async removeItem(id) {
        try {
            await fetch(`${this.apiUrl}?id=${id}`, {
                method: 'DELETE'
            });

            this.budgetItems = this.budgetItems.filter(item => item.id !== id);
            this.render();
        } catch (error) {
            console.error('Error removing budget item:', error);
        }
    }

    calculateTotals() {
        const totalBudget = this.budgetItems.reduce((sum, item) => sum + item.budget, 0);
        const totalSpent = this.budgetItems.reduce((sum, item) => sum + item.spent, 0);
        return { totalBudget, totalSpent };
    }

    render() {
        this.budgetItemsContainer.innerHTML = '';
        
        this.budgetItems.forEach(item => {
            const percentage = (item.spent / item.budget) * 100;
            const itemElement = document.createElement('div');
            itemElement.className = 'budget-item';
            itemElement.innerHTML = `
                <div class="budget-item-header">
                    <span class="budget-item-title">${item.category}</span>
                    <button class="delete-btn" onclick="budgetTracker.removeItem(${item.id})">Delete</button>
                </div>
                <div class="budget-details">
                    <span>Budget: $${item.budget.toFixed(2)}</span>
                    <input type="number" 
                           class="spent-input" 
                           value="${item.spent}" 
                           min="0" 
                           max="${item.budget}"
                           onchange="budgetTracker.updateSpent(${item.id}, this.value)">
                </div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: ${percentage}%"></div>
                </div>
            `;
            this.budgetItemsContainer.appendChild(itemElement);
        });

        const { totalBudget, totalSpent } = this.calculateTotals();
        this.totalBudgetElement.textContent = `$${totalBudget.toFixed(2)}`;
        this.totalSpentElement.textContent = `$${totalSpent.toFixed(2)}`;
        this.totalProgressElement.style.width = `${(totalSpent / totalBudget) * 100 || 0}%`;
    }
}

const budgetTracker = new BudgetTracker();