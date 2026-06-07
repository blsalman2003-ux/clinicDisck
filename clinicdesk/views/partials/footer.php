</div><!-- ./wrapper -->

<!-- AdminLTE JS -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/dist/js/adminlte.min.js"></script>

<!-- DataTables -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function () {
  if ($.fn.DataTable) {
    $('.dt-table').DataTable({ responsive: true, autoWidth: false });
  }

  $(document).on('change', '.custom-file-input', function () {
    var name = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(name || 'Choose file...');
  });
});
</script>

<?php if (!empty($extraJs)) echo $extraJs; ?>

</body>
</html>
