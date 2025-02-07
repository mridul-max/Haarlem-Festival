// Get the select dropdown element
var selectElement = document.getElementById('userType');

// Listen for changes to the select dropdown
selectElement.addEventListener('change', function(event) {
    var selectedValue = event.target.value.toLowerCase();
    // Get the table rows
    var tableRows = document.querySelectorAll('tbody tr');

    for (var i = 0; i < tableRows.length; i++) {
        var row = tableRows[i];

        // If the selected option is "All", show all rows
        if (selectedValue === '') {
            row.style.display = '';
        } else {
            // Otherwise, show rows that match the selected option and hide the rest
            var userTypeCell = row.querySelector('td[data-th="Role"]');
            if (userTypeCell.innerText.trim().toLowerCase() === selectedValue) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
});
