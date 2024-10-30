jQuery(document).ready(function($) {
    // JavaScript function to open a specific tab
    function openTab(tabName) {
        // Hide all tab contents
        $('.tab-content').hide();
        
        // Remove the "active" class from all tab buttons
        $('.tab-button').removeClass('active');
        
        // Show the selected tab content
        $('#' + tabName).show();
        
        // Add the "active" class to the clicked tab button
        $('button[data-tab="' + tabName + '"]').addClass('active');
    }

    // Set a default tab to be active on page load
    openTab('tab1');

    // Event handler for tab button clicks
    $('.tab-button').click(function(event) {
        event.preventDefault(); // Prevent the default button behavior
        var tabName = $(this).data('tab');
        openTab(tabName);
    });
});
