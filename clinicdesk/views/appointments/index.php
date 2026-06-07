<?php
Auth::requireRole('admin','doctor','patient');
$role = Auth::role();
$pageTitle = $role === 'doctor' ? 'My Schedule' : ($role === 'patient' ? 'My Appointments' : 'All Appointments');
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
            <li class="breadcrumb-item active">Appointments</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

      <!-- Today's list — doctor only -->
      <?php if ($role === 'doctor' && !empty($todayAppointments)): ?>
      <div class="card card-outline card-info mb-3">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-calendar-day mr-2"></i>Today — <?= date('d M Y') ?></h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead><tr><th>Time</th><th>Patient</th><th>Reason</th><th>Status</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($todayAppointments as $t): ?>
              <tr>
                <td><?= formatTime($t['appt_time']) ?></td>
                <td><?= e($t['patient_name']) ?></td>
                <td><?= e($t['reason'] ?? '—') ?></td>
                <td><span class="badge badge-<?= statusBadge($t['status']) ?>"><?= ucfirst(e($t['status'])) ?></span></td>
                <td>
                  <a href="<?= url('appointments','detail') ?>&id=<?= (int)$t['id'] ?>"
                     class="btn btn-xs btn-outline-primary">View</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

      <!-- Filter bar -->
      <div class="card card-default collapsed-card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filters</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <form method="GET" action="<?= url('appointments','index') ?>">
            <input type="hidden" name="page"   value="appointments">
            <input type="hidden" name="action" value="index">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" class="form-control form-control-sm">
                    <option value="">All</option>
                    <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($_GET['status']??'')===$s?'selected':'' ?>>
                      <?= ucfirst($s) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <?php if ($role === 'admin'): ?>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Doctor</label>
                  <select name="doctor_id" class="form-control form-control-sm">
                    <option value="">All Doctors</option>
                    <?php foreach ($doctors as $d): ?>
                    <option value="<?= (int)$d['id'] ?>" <?= ($_GET['doctor_id']??'')==$d['id']?'selected':'' ?>>
                      <?= e($d['name']) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Patient Name</label>
                  <input type="text" name="patient_name" class="form-control form-control-sm"
                         value="<?= e($_GET['patient_name'] ?? '') ?>">
                </div>
              </div>
              <?php endif; ?>
              <div class="col-md-2">
                <div class="form-group">
                  <label>From</label>
                  <input type="date" name="start_date" class="form-control form-control-sm"
                         value="<?= e($_GET['start_date'] ?? '') ?>">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>To</label>
                  <input type="date" name="end_date" class="form-control form-control-sm"
                         value="<?= e($_GET['end_date'] ?? '') ?>">
                </div>
              </div>
              <div class="col-md-1 d-flex align-items-end pb-3">
                <button type="submit" class="btn btn-sm btn-primary mr-1">
                  <i class="fas fa-search"></i>
                </button>
                <a href="<?= url('appointments','index') ?>" class="btn btn-sm btn-default">
                  <i class="fas fa-times"></i>
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Appointments table -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-calendar-check mr-2"></i>Appointments</h3>
          <?php if ($role === 'patient'): ?>
          <div class="card-tools">
            <a href="<?= url('appointments','book') ?>" class="btn btn-sm btn-success">
              <i class="fas fa-plus mr-1"></i> Book New
            </a>
          </div>
          <?php endif; ?>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <?php if ($role !== 'patient'): ?><th>Patient</th><?php endif; ?>
                <?php if ($role !== 'doctor'):  ?><th>Doctor</th><?php endif; ?>
                <th>Specialization</th><th>Date</th><th>Time</th>
                <th>Status</th><th>Reason</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($appointments)): ?>
              <tr><td colspan="8" class="text-center text-muted py-4">No appointments found.</td></tr>
              <?php else: ?>
              <?php foreach ($appointments as $a): ?>
              <tr>
                <?php if ($role !== 'patient'): ?>
                <td><?= e($a['patient_name']) ?></td>
                <?php endif; ?>
                <?php if ($role !== 'doctor'): ?>
                <td><?= e($a['doctor_name']) ?></td>
                <?php endif; ?>
                <td><small><?= e($a['specialization_name']) ?></small></td>
                <td><?= formatDate($a['appt_date']) ?></td>
                <td><?= formatTime($a['appt_time']) ?></td>
                <td>
                  <span class="badge badge-<?= statusBadge($a['status']) ?>">
                    <?= ucfirst(e($a['status'])) ?>
                  </span>
                </td>
                <td><small><?= e($a['reason'] ?? '—') ?></small></td>
                <td>
                  <a href="<?= url('appointments','detail') ?>&id=<?= (int)$a['id'] ?>"
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
        <?php if (isset($pager) && $pager->totalPages() > 1): ?>
        <div class="card-footer clearfix">
          <?php require_once __DIR__ . '/../partials/pagination.php'; ?>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
