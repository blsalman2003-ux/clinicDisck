<?php

if (!empty($_SESSION['flash'])):
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    unset($_SESSION['old_input']);
    $type = in_array($flash['type'], ['success','danger','warning','info'], true)
            ? $flash['type'] : 'info';
?>
<div class="alert alert-<?= $type ?> alert-dismissible fade show mx-3 mt-3" role="alert">
  <i class="fas fa-<?= match($type) {
      'success' => 'check-circle',
      'danger'  => 'exclamation-circle',
      'warning' => 'exclamation-triangle',
      default   => 'info-circle',
  } ?> mr-2"></i>
  <?= e($flash['message']) ?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php endif; ?>
