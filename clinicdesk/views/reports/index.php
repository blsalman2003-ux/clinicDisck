<?php
Auth::requireRole('admin');
$pageTitle = 'Reports';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Appointment Reports</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Reports</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

      <!-- Filter form -->
      <div class="card card-primary">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-filter mr-2"></i>Report Filters</h3></div>
        <form method="GET" action="<?= url('reports','index') ?>">
          <input type="hidden" name="page"   value="reports">
          <input type="hidden" name="action" value="index">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Start Date <span class="text-danger">*</span></label>
                  <input type="date" name="start_date" class="form-control"
                         value="<?= e($_GET['start_date'] ?? '') ?>" required>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>End Date <span class="text-danger">*</span></label>
                  <input type="date" name="end_date" class="form-control"
                         value="<?= e($_GET['end_date'] ?? '') ?>" required>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Doctor</label>
                  <select name="doctor_id" class="form-control">
                    <option value="">All Doctors</option>
                    <?php foreach ($doctors as $d): ?>
                    <option value="<?= (int)$d['id'] ?>"
                      <?= ($_GET['doctor_id']??'')==$d['id']?'selected':'' ?>>
                      <?= e($d['name']) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" class="form-control">
                    <option value="">All</option>
                    <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($_GET['status']??'')===$s?'selected':'' ?>>
                      <?= ucfirst($s) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-search mr-1"></i> Generate Report
            </button>
          </div>
        </form>
      </div>

      <!-- Results -->
      <?php if ($hasFilter): ?>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            Results: <?= formatDate($_GET['start_date']) ?> — <?= formatDate($_GET['end_date']) ?>
            <span class="badge badge-primary ml-2"><?= count($results) ?> appointments</span>
          </h3>
          <div class="card-tools">
            <?php if (!empty($results)): ?>
            <a href="<?= BASE_URL ?>/index.php?<?= http_build_query(array_merge($_GET, ['export'=>'csv'])) ?>"
               class="btn btn-sm btn-success">
              <i class="fas fa-file-csv mr-1"></i> Export CSV
            </a>
            <?php endif; ?>
          </div>
        </div>

        <!-- Summary row -->
        <?php if (!empty($summary)): ?>
        <div class="card-body pb-0">
          <div class="row">
            <?php foreach ($summary as $st => $count): ?>
            <div class="col-md-3 col-6">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-<?= statusBadge($st) ?>">
                  <i class="fas fa-calendar-check"></i>
                </span>
                <div class="info-box-content">
                  <span class="info-box-text"><?= ucfirst($st) ?></span>
                  <span class="info-box-number"><?= $count ?></span>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="card-body p-0">
          <table class="table table-striped table-sm mb-0">
            <thead class="thead-light">
              <tr>
                <th>Patient</th><th>Doctor</th><th>Specialization</th>
                <th>Date</th><th>Time</th><th>Status</th><th>Reason</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($results)): ?>
              <tr><td colspan="7" class="text-center text-muted py-4">No appointments in this range.</td></tr>
              <?php else: ?>
              <?php foreach ($results as $r): ?>
              <tr>
                <td><?= e($r['patient_name']) ?></td>
                <td><?= e($r['doctor_name']) ?></td>
                <td><small><?= e($r['specialization_name']) ?></small></td>
                <td><?= formatDate($r['appt_date']) ?></td>
                <td><?= formatTime($r['appt_time']) ?></td>
                <td>
                  <span class="badge badge-<?= statusBadge($r['status']) ?>">
                    <?= ucfirst(e($r['status'])) ?>
                  </span>
                </td>
                <td><small><?= e($r['reason'] ?? '—') ?></small></td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
