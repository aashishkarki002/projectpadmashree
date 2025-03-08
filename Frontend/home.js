$(document).ready(function() {
    // Initial load of account summary
    updateAccountSummary();

    // Income form submission
    $('form[action="../backend/income_insert.php"]').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const form = this;
        
        // Validate the form
        const category = formData.get('category');
        const amount = formData.get('amount');
        const date = formData.get('date');
        
        if (!category || !amount || !date) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }

        $.ajax({
            url: '../backend/income_insert.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        // Show success message without budget prompt
                        Swal.fire({
                            title: 'Success!',
                            text: 'Income added successfully',
                            icon: 'success'
                        }).then(() => {
                            // Reset the form
                            form.reset();
                            
                            // Update the displayed amounts without page reload
                            updateAccountSummary();
                        });
                    } else {
                        Swal.fire('Error', data.error || 'Failed to add income', 'error');
                    }
                } catch (e) {
                    console.error("Parse error:", e);
                    console.log('Raw response:', response);
                    Swal.fire('Error', 'Invalid server response', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Ajax error:", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                Swal.fire('Error', 'Failed to process request', 'error');
            }
        });
    });

    // Expense form submission
    $('form[action="../backend/expense_insert.php"]').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const form = this;
        
        // Validate the form
        const category = formData.get('category');
        const amount = formData.get('amount');
        const date = formData.get('date');
        
        if (!category || !amount || !date) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }

        $.ajax({
            url: '../backend/expense_insert.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Expense added successfully',
                            icon: 'success'
                        }).then(() => {
                            // Reset the form
                            form.reset();
                            
                            // Update the displayed amounts without page reload
                            updateAccountSummary();
                        });
                    } else {
                        Swal.fire('Error', data.error || 'Failed to add expense', 'error');
                    }
                } catch (e) {
                    console.error("Parse error:", e);
                    console.log('Raw response:', response);
                    Swal.fire('Error', 'Invalid server response', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Ajax error:", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                Swal.fire('Error', 'Failed to process request', 'error');
            }
        });
    });
    
    // Helper function to update account summary
    function updateAccountSummary() {
        $.ajax({
            url: '../backend/fetch.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (!response.error) {
                    $('.income-amt').html("NPR " + formatToNPR(response.total_income));
                    $('.expense-amt').html("NPR " + formatToNPR(response.total_expense));
                    $('.balance-amt').html("NPR " + formatToNPR(response.balance));
                } else {
                    console.error("Error in response:", response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    // Helper function to format numbers with commas (e.g., 1000 -> 1,000)
    function formatToNPR(value) {
        return value.toLocaleString('en-US');
    }
});
