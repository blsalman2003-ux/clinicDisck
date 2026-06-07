<?php
Auth::requireRole('admin');
$pageTitle = 'Doctors';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Doctors</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Doctors</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-user-md mr-2"></i>All Doctors</h3>
          <div class="card-tools">
            <a href="<?= url('doctors','specializations') ?>" class="btn btn-sm btn-outline-info mr-2">
              <i class="fas fa-stethoscope mr-1"></i> Specializations
            </a>
            <a href="<?= url('users','create') ?>?role=doctor" class="btn btn-sm btn-primary">
              <i class="fas fa-plus mr-1"></i> Add Doctor
            </a>
          </div>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th>Name</th><th>Specialization</th><th>Fee</th>
                <th>Available Days</th><th>Status</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($doctors)): ?>
              <tr><td colspan="6" class="text-center text-muted py-4">No doctors found.</td></tr>
              <?php else: ?>
              <?php foreach ($doctors as $d): ?>
              <tr>
                <td><?= e($d['name']) ?></td>
                <td><span class="badge badge-info"><?= e($d['specialization_name']) ?></span></td>
                <td>$<?= number_format($d['consultation_fee'], 2) ?></td>
                <td><small><?= e($d['available_days']) ?></small></td>
                <td>
                  <?php if ($d['is_active']): ?>
                    <span class="badge badge-success">Active</span>
                  <?php else: ?>
                    <span class="badge badge-secondary">Suspended</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="<?= url('doctors','edit') ?>&id=<?= (int)$d['id'] ?>"
                     class="btn btn-xs btn-warning">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if ($pager->totalPages() > 1): ?>
        <div class="card-footer clearfix">
          <?php require_once __DIR__ . '/../partials/pagination.php'; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
