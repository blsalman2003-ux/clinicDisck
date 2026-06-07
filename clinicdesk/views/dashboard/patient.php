<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('patient');
$pageTitle = 'My Dashboard';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">My Health Dashboard</h1></div>
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

      <!-- ── Next Appointment Card ────────────────────────────── -->
      <?php if (!empty($nextAppointment)): $a = $nextAppointment; ?>
      <div class="callout callout-info">
        <h5><i class="fas fa-calendar-check mr-2"></i>Your Next Appointment</h5>
        <p class="mb-0">
          <strong><?= e($a['doctor_name']) ?></strong>
          (<?= e($a['specialization_name']) ?>)
          — <?= formatDate($a['appt_date']) ?> at <?= formatTime($a['appt_time']) ?>
          &nbsp;<span class="badge badge-<?= statusBadge($a['status']) ?>"><?= ucfirst(e($a['status'])) ?></span>
        </p>
      </div>
      <?php else: ?>
      <div class="callout callout-warning">
        <h5><i class="fas fa-info-circle mr-2"></i>No Upcoming Appointments</h5>
        <a href="<?= url('appointments','book') ?>" class="btn btn-sm btn-primary mt-1">
          <i class="fas fa-plus mr-1"></i> Book an Appointment
        </a>
      </div>
      <?php endif; ?>

      <!-- ── Stat Cards ───────────────────────────────────────── -->
      <div class="row">
        <div class="col-lg-4 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?= (int)($stats['active'] ?? 0) ?></h3>
              <p>Active Appointments</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <a href="<?= url('appointments','index') ?>" class="small-box-footer">
              View <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <div class="col-lg-4 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?= (int)($stats['completed'] ?? 0) ?></h3>
              <p>Completed Visits</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="<?= url('appointments','index') ?>?status=completed" class="small-box-footer">
              View <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <div class="col-lg-4 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?= (int)($prescriptionCount ?? 0) ?></h3>
              <p>Prescriptions Available</p>
            </div>
            <div class="icon"><i class="fas fa-pills"></i></div>
            <a href="<?= url('prescriptions','index') ?>" class="small-box-footer">
              View <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- ── Quick Action ─────────────────────────────────────── -->
      <div class="row">
        <div class="col-12">
          <a href="<?= url('appointments','book') ?>" class="btn btn-primary btn-lg">
            <i class="fas fa-plus-circle mr-2"></i> Book New Appointment
          </a>
          <a href="<?= url('appointments','index') ?>" class="btn btn-outline-secondary btn-lg ml-2">
            <i class="fas fa-list mr-2"></i> My Appointments
          </a>
        </div>
      </div>

    </div>
  </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
