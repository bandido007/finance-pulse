// Basic initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('Finance Pulse initialized');
});

document.querySelector('form').addEventListener('submit', function(e) {
    const fundSelect = document.getElementById('fund_id');
    if (fundSelect.value) {
        const selectedOption = fundSelect.options[fundSelect.selectedIndex];
        const remaining = parseFloat(selectedOption.dataset.remaining);
        const amount = parseFloat(document.getElementById('amount').value);
        
        if (amount > remaining) {
            alert('Expense amount exceeds available fund balance');
            e.preventDefault();
        }
    }
});