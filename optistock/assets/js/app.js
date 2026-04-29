$(function () {
  // Sidebar mobile toggle
  $('#sidebarToggle').on('click', function () {
    $('#sidebar').toggleClass('open');
    $('#sidebarOverlay').toggleClass('show');
  });
  $('#sidebarClose, #sidebarOverlay').on('click', function () {
    $('#sidebar').removeClass('open');
    $('#sidebarOverlay').removeClass('show');
  });

  // Auto-dismiss alerts
  setTimeout(function () {
    $('.alert.fade.show').alert('close');
  }, 4000);

  // Confirm delete
  $(document).on('click', '.btn-confirm-delete', function (e) {
    if (!confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
      e.preventDefault();
    }
  });

  // DataTable default init (tables with class .datatable)
  if ($.fn.DataTable) {
    $('.datatable').DataTable({
      responsive: true,
      pageLength: 25,
      language: {
        search: '<span class="material-icons align-middle" style="font-size:16px;">search</span> _INPUT_',
        searchPlaceholder: 'Search...'
      }
    });
  }
});
