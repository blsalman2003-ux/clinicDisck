<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('admin');
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Dashboard</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

      <div class="row">

        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?= (int)($stats['patients'] ?? 0) ?></h3>
              <p>Total Patients</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="<?= url('users','index') ?>?role=patient" class="small-box-footer">
              View all <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?= (int)($stats['doctors'] ?? 0) ?></h3>
              <p>Total Doctors</p>
            </div>
            <div class="icon"><i class="fas fa-user-md"></i></div>
            <a href="<?= url('doctors','index') ?>" class="small-box-footer">
              View all <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?= (int)($stats['today'] ?? 0) ?></h3>
              <p>Appointments Today</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-day"></i></div>
            <a href="<?= url('appointments','index') ?>" class="small-box-footer">
              View all <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3><?= (int)($stats['week_total'] ?? 0) ?></h3>
              <p>Appointments This Week</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-week"></i></div>
            <a href="<?= url('reports','index') ?>" class="small-box-footer">
              View report <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>This Week by Status</h3>
            </div>
            <div class="card-body p-0">
              <ul class="list-group list-group-flush">
                <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <?= ucfirst($s) ?>
                  <span class="badge badge-<?= statusBadge($s) ?> badge-pill">
                    <?= (int)($stats['week_status'][$s] ?? 0) ?>
                  </span>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Recent Appointments</h3>
              <div class="card-tools">
                <a href="<?= url('appointments','index') ?>" class="btn btn-sm btn-outline-primary">
                  View all
                </a>
              </div>
            </div>
            <div class="card-body p-0">
              <table class="table table-striped table-sm mb-0">
                <thead>
                  <tr>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($recentAppointments)): ?>
                  <tr><td colspan="4" class="text-center text-muted py-3">No appointments yet.</td></tr>
                  <?php else: ?>
                  <?php foreach ($recentAppointments as $appt): ?>
                  <tr>
                    <td><?= e($appt['patient_name']) ?></td>
                    <td><?= e($appt['doctor_name']) ?></td>
                    <td><?= formatDate($appt['appt_date']) ?></td>
                    <td>
                      <span class="badge badge-<?= statusBadge($appt['status']) ?>">
                        <?= ucfirst(e($appt['status'])) ?>
                      </span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
