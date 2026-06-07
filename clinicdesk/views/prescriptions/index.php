<?php
Auth::requireRole('admin', 'doctor', 'patient');
$role      = Auth::role();
$pageTitle = $role === 'doctor' ? 'Prescriptions I Wrote' : 'My Prescriptions';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0"><?= e($pageTitle) ?></h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Prescriptions</li>
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
          <h3 class="card-title">
            <i class="fas fa-file-medical mr-2"></i><?= e($pageTitle) ?>
          </h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped mb-0">
            <thead class="thead-light">
              <tr>
                <?php if ($role === 'doctor'): ?>
                  <th>Patient</th>
                <?php else: ?>
                  <th>Doctor</th>
                <?php endif; ?>
                <th>Date</th>
                <th>Diagnosis</th>
                <th>Medications</th>
                <th>PDF</th>
                <?php if ($role === 'doctor'): ?>
                  <th>Action</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($prescriptions)): ?>
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  <i class="fas fa-file-medical fa-2x mb-2 d-block"></i>
                  No prescriptions found.
                </td>
              </tr>
              <?php else: ?>
              <?php foreach ($prescriptions as $p): ?>
              <tr>
                <?php if ($role === 'doctor'): ?>
                  <td><?= e($p['patient_name']) ?></td>
                <?php else: ?>
                  <td>
                    Dr. <?= e($p['doctor_name']) ?><br>
                    <small class="text-muted"><?= e($p['specialization_name']) ?></small>
                  </td>
                <?php endif; ?>

                <td><?= formatDate($p['appt_date']) ?></td>
                <td><small><?= e(mb_strimwidth($p['diagnosis'], 0, 60, '...')) ?></small></td>
                <td><small><?= e(mb_strimwidth($p['medications'], 0, 60, '...')) ?></small></td>
                <td>
                  <?php if ($p['file_path']): ?>
                  <a href="<?= url('prescriptions','download') ?>&id=<?= (int)$p['appointment_id'] ?>"
                     class="btn btn-sm btn-success">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                  </a>
                  <?php else: ?>
                  <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>
                <?php if ($role === 'doctor'): ?>
                <td>
                  <a href="<?= url('appointments','detail') ?>&id=<?= (int)$p['appointment_id'] ?>"
                     class="btn btn-xs btn-outline-primary">
                    <i class="fas fa-eye"></i> View
                  </a>
                </td>
                <?php endif; ?>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
