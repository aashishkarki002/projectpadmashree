<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Tracker Settings</title>
    <link rel="stylesheet" href="css/setting.css">
    <link rel="stylesheet" href="css/home.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            margin: 50px auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn2 {
            background-color: #e0e0e0;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.2s;
        }

        .btn2:hover {
            background-color: #d0d0d0;
        }

        .status-message {
            margin-top: 8px;
            padding: 8px;
            border-radius: 4px;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .bank-account-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 8px 0;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }

        .esewa-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .settings-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 4px;
            display: none;
            z-index: 1001;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
    </style>
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
        </div>

        <div class="mid-bar">
            <div class="settings-container">
                <h1>Settings</h1>

                <form id="settingsForm">
                    <!-- General Settings Section -->
                    <div class="settings-section">
                        <h2>General Settings</h2>
                        <div class="form-group">
                            <label for="currency">Currency Selection</label>
                            <select id="currency" class="currency-select" name="currency">
                                <option value="NPR">NPR (रू)</option>
                                <option value="INR">INR (₹)</option>
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="CNY">CNY (¥)</option>
                            </select>
                            <div class="currency-info">
                                <span class="currency-symbol">रू</span>
                                <span>Nepali Rupee</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="startDay">Month Start Day</label>
                            <input type="number" id="startDay" name="startDay" min="1" max="31" value="1">
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="your@email.com">
                        </div>
                    </div>

                    <!-- Payment Methods Section -->
                    <div class="settings-section">
                        <h2>Payment Methods</h2>
                        
                        <div class="form-group">
                            <h3>Bank Accounts</h3>
                            <div id="bankAccounts"></div>
                            <button type="button" class="btn2" id="addBankBtn">+ Add Bank Account</button>
                        </div>

                        <div class="form-group">
                            <h3>eSewa Account</h3>
                            <div class="esewa-container">
                                <input type="tel" id="esewaId" name="esewaId" 
                                       placeholder="eSewa ID (Mobile Number)" 
                                       pattern="[0-9]{10}" 
                                       title="Please enter a valid 10-digit mobile number">
                                <button type="button" class="btn2" id="linkEsewaBtn">Link eSewa</button>
                            </div>
                            <div id="esewaStatus" class="status-message"></div>
                        </div>
                    </div>

                    <!-- Budget Alerts Section -->
                    <div class="settings-section">
                        <h2>Budget Alerts</h2>
                        <div class="form-group">
                            <label for="threshold">Budget Warning Threshold (%)</label>
                            <input type="number" id="threshold" name="threshold" min="1" max="100" value="80">
                        </div>
                        <button type="submit" class="btn1">Save Settings</button>
                    </div>
                </form>

                <!-- Bank Account Modal -->
                <div id="bankModal" class="modal">
                    <div class="modal-content">
                        <h2>Add Bank Account</h2>
                        <form id="bankForm">
                            <div class="form-group">
                                <label for="bankName">Bank Name</label>
                                <select id="bankName" required>
                                    <option value="">Select a bank</option>
                                    <option value="nabil">Nabil Bank</option>
                                    <option value="nic">NIC Asia Bank</option>
                                    <option value="global">Global IME Bank</option>
                                    <option value="nmb">NMB Bank</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="accountNumber">Account Number</label>
                                <input type="text" id="accountNumber" required 
                                       pattern="[0-9]{10,16}" 
                                       title="Please enter a valid account number (10-16 digits)">
                            </div>
                            <div class="form-group">
                                <label for="accountHolder">Account Holder Name</label>
                                <input type="text" id="accountHolder" required>
                            </div>
                            <div class="modal-buttons">
                                <button type="submit" class="btn1">Add Account</button>
                                <button type="button" class="btn2" id="cancelBank">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="notification" class="notification"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Settings functionality
            const loadSettings = () => {
                const settings = JSON.parse(localStorage.getItem('budgetTrackerSettings') || '{}');
                document.getElementById('currency').value = settings.currency || 'NPR';
                document.getElementById('startDay').value = settings.startDay || 1;
                document.getElementById('email').value = settings.email || '';
                document.getElementById('threshold').value = settings.threshold || 80;
                updateCurrencyInfo(document.getElementById('currency').value);
            };

            const updateCurrencyInfo = (currency) => {
                const currencyInfo = {
                    NPR: { symbol: 'रू', name: 'Nepali Rupee' },
                    INR: { symbol: '₹', name: 'Indian Rupee' },
                    USD: { symbol: '$', name: 'US Dollar' },
                    EUR: { symbol: '€', name: 'Euro' },
                    CNY: { symbol: '¥', name: 'Chinese Yuan' }
                };

                const info = currencyInfo[currency] || currencyInfo.NPR;
                document.querySelector('.currency-symbol').textContent = info.symbol;
                document.querySelector('.currency-info span:last-child').textContent = info.name;
            };

            const saveSettings = (e) => {
                e.preventDefault();
                const settings = {
                    currency: document.getElementById('currency').value,
                    startDay: document.getElementById('startDay').value,
                    email: document.getElementById('email').value,
                    threshold: document.getElementById('threshold').value
                };

                localStorage.setItem('budgetTrackerSettings', JSON.stringify(settings));
                showNotification('Settings saved successfully!', 'success');
            };

            const showNotification = (message, type) => {
                const notification = document.getElementById('notification');
                notification.textContent = message;
                notification.className = `notification status-${type}`;
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);
            };

            // Bank account functionality
            const bankModal = document.getElementById('bankModal');
            const addBankBtn = document.getElementById('addBankBtn');
            const cancelBank = document.getElementById('cancelBank');
            const bankForm = document.getElementById('bankForm');
            const bankAccountsList = document.getElementById('bankAccounts');

            const loadBankAccounts = () => {
                const accounts = JSON.parse(localStorage.getItem('bankAccounts') || '[]');
                bankAccountsList.innerHTML = accounts.map(account => `
                    <div class="bank-account-item">
                        <div>
                            <strong>${account.bankName}</strong><br>
                            Account: ${account.accountNumber.slice(-4).padStart(account.accountNumber.length, '*')}
                        </div>
                        <button type="button" class="btn2 remove-bank" 
                                data-account="${account.accountNumber}">Remove</button>
                    </div>
                `).join('');
            };

            addBankBtn.onclick = () => bankModal.style.display = 'block';
            cancelBank.onclick = () => {
                bankModal.style.display = 'none';
                bankForm.reset();
            };

            bankForm.onsubmit = (e) => {
                e.preventDefault();
                const newAccount = {
                    bankName: document.getElementById('bankName').value,
                    accountNumber: document.getElementById('accountNumber').value,
                    accountHolder: document.getElementById('accountHolder').value
                };

                const accounts = JSON.parse(localStorage.getItem('bankAccounts') || '[]');
                accounts.push(newAccount);
                localStorage.setItem('bankAccounts', JSON.stringify(accounts));

                bankModal.style.display = 'none';
                bankForm.reset();
                loadBankAccounts();
                showNotification('Bank account added successfully!', 'success');
            };

            bankAccountsList.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-bank')) {
                    const accountNumber = e.target.dataset.account;
                    const accounts = JSON.parse(localStorage.getItem('bankAccounts') || '[]');
                    const updatedAccounts = accounts.filter(acc => acc.accountNumber !== accountNumber);
                    localStorage.setItem('bankAccounts', JSON.stringify(updatedAccounts));
                    loadBankAccounts();
                    showNotification('Bank account removed successfully!', 'success');
                }
            });

            // eSewa functionality
            const linkEsewaBtn = document.getElementById('linkEsewaBtn');
            const esewaStatus = document.getElementById('esewaStatus');

            linkEsewaBtn.onclick = () => {
                const esewaId = document.getElementById('esewaId').value;
                
                if (!esewaId.match(/^\d{10}$/)) {
                    esewaStatus.textContent = 'Please enter a valid 10-digit mobile number';
                    esewaStatus.className = 'status-message status-error';
                    return;
                }

                esewaStatus.textContent = 'Linking eSewa account...';
                esewaStatus.className = 'status-message';

                setTimeout(() => {
                    localStorage.setItem('esewaLinked', esewaId);
                    esewaStatus.textContent = 'eSewa account linked successfully!';
                    esewaStatus.className = 'status-message status-success';
                }, 1500);
            };

            const loadEsewaAccount = () => {
                const esewaId = localStorage.getItem('esewaLinked');
                if (esewaId) {
                    document.getElementById('esewaId').value = esewaId;
                    esewaStatus.textContent = 'eSewa account is linked';
                    esewaStatus.className = 'status-message status-success';
                }
            };

            // Event listeners
            document.getElementById('settingsForm').addEventListener('submit', saveSettings);
            document.getElementById('currency').addEventListener('change', (e) => {
                updateCurrencyInfo(e.target.value);
            });

            // Initialize everything
            loadSettings();
            loadBankAccounts();
            loadEsewaAccount();