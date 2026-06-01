<!-- Vendor JS Files (jQuery already loaded in head.php) -->
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>

<!-- Bootstrap Bundle JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script src="assets/vendor/quill/quill.min.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.bootstrap5.js"></script>

<!-- ECharts for charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>

<script>
  // Initialize DataTables
  $(document).ready(function() {
    $('.datatable').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
      language: {
        search: 'Search:',
        lengthMenu: 'Show _MENU_ entries',
        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
        paginate: {
          first: 'First',
          last: 'Last',
          next: 'Next',
          previous: 'Previous'
        }
      }
    });
  });
</script>     
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
