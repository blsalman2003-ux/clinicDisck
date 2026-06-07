<?php

$queryParams = $_GET;
unset($queryParams['p']);
$baseUrl = BASE_URL . '/index.php?' . http_build_query($queryParams) . '&p=';
?>
<nav aria-label="Page navigation">
  <ul class="pagination pagination-sm mb-0">

    <li class="page-item <?= !$pager->hasPrev() ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= $baseUrl . ($pager->currentPage() - 1) ?>">
        <i class="fas fa-chevron-left"></i>
      </a>
    </li>

    <?php for ($i = 1; $i <= $pager->totalPages(); $i++): ?>
    <li class="page-item <?= $i === $pager->currentPage() ? 'active' : '' ?>">
      <a class="page-link" href="<?= $baseUrl . $i ?>"><?= $i ?></a>
    </li>
    <?php endfor; ?>

    <li class="page-item <?= !$pager->hasNext() ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= $baseUrl . ($pager->currentPage() + 1) ?>">
        <i class="fas fa-chevron-right"></i>
      </a>
    </li>

  </ul>
</nav>
<small class="text-muted ml-3">
  Showing page <?= $pager->currentPage() ?> of <?= $pager->totalPages() ?>
  (<?= $pager->totalItems() ?> total)
</small>
