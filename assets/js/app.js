/*
 * Page behaviour shared by all screens.
 * Kept in a file (not inline) so the Content-Security-Policy can block inline scripts.
 */
$(function () {
    // Sortable, searchable tables.
    $('table.data-table').DataTable({ order: [] });

    // Ask for confirmation before submitting forms marked with data-confirm.
    $(document).on('submit', 'form[data-confirm]', function (event) {
        if (!window.confirm($(this).data('confirm'))) {
            event.preventDefault();
        }
    });
});
