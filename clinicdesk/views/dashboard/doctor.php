<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('doctor');
$pageTitle = 'My Dashboard';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">My Dashboard</h1></div>
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

      <!-- ── Stat Cards ───────────────────────────────────────── -->
      <div class="row">
        <div class="col-lg-4 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?= (int)($stats['total'] ?? 0) ?></h3>
              <p>Appointments This Month</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <a href="<?= url('appointments','index') ?>" class="small-box-footer">
              View all <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <div class="col-lg-4 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?= (int)($stats['pending'] ?? 0) ?></h3>
              <p>Pending</p>
            </div>
            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            <a href="<?= url('appointments','index') ?>?status=pending" class="small-box-footer">
              View <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <div class="col-lg-4 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?= (int)($stats['completed'] ?? 0) ?></h3>
              <p>Completed</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="<?= url('appointments','index') ?>?status=completed" class="small-box-footer">
              View <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- ── Today's Appointments ─────────────────────────────── -->
      <div class="card card-outline card-primary">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-calendar-day mr-2"></i>
            Today's Appointments — <?= date('l, d M Y') ?>
          </h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped mb-0">
            <thead>
              <tr><th>Time</th><th>Patient</th><th>Reason</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
              <?php if (empty($todayAppointments)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-3">
                  <i class="fas fa-coffee mr-1"></i> No appointments scheduled for today.
                </td>
              </tr>
              <?php else: ?>
              <?php foreach ($todayAppointments as $appt): ?>
              <tr>
                <td><?= formatTime($appt['appt_time']) ?></td>
                <td><?= e($appt['patient_name']) ?></td>
                <td><?= e($appt['reason'] ?? '—') ?></td>
                <td>
                  <span class="badge badge-<?= statusBadge($appt['status']) ?>">
                    <?= ucfirst(e($appt['status'])) ?>
                  </span>
                </td>
                <td>
                  <a href="<?= url('appointments','detail') ?>&id=<?= (int)$appt['id'] ?>"
                     class="btn btn-xs btn-outline-primary">
                    <i class="fas fa-eye"></i> View
                  </a>
                </td>
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
