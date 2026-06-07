<?php
Auth::requireRole('admin','doctor','patient');
$role = Auth::role();
$pageTitle = 'Appointment #' . $appt['id'];
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Appointment #<?= (int)$appt['id'] ?></h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('appointments','index') ?>">Appointments</a></li>
            <li class="breadcrumb-item active">#<?= (int)$appt['id'] ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

      <div class="row">
        <!-- Left: Appointment Info -->
        <div class="col-md-7">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Details</h3>
              <div class="card-tools">
                <span class="badge badge-<?= statusBadge($appt['status']) ?> badge-lg" style="font-size:.9rem;">
                  <?= ucfirst(e($appt['status'])) ?>
                </span>
              </div>
            </div>
            <div class="card-body">
              <table class="table table-borderless">
                <tr><th style="width:35%">Patient</th><td><?= e($appt['patient_name']) ?></td></tr>
                <tr><th>Doctor</th><td>Dr. <?= e($appt['doctor_name']) ?></td></tr>
                <tr><th>Specialization</th><td><?= e($appt['specialization_name']) ?></td></tr>
                <tr><th>Date</th><td><?= formatDate($appt['appt_date']) ?></td></tr>
                <tr><th>Time</th><td><?= formatTime($appt['appt_time']) ?></td></tr>
                <tr><th>Reason</th><td><?= e($appt['reason'] ?? '—') ?></td></tr>
                <tr><th>Fee</th><td>$<?= number_format($appt['consultation_fee'], 2) ?></td></tr>
                <?php if ($appt['doctor_notes']): ?>
                <tr><th>Doctor Notes</th><td><?= e($appt['doctor_notes']) ?></td></tr>
                <?php endif; ?>
                <?php if ($appt['cancellation_reason']): ?>
                <tr><th>Cancellation Reason</th>
                    <td class="text-danger"><?= e($appt['cancellation_reason']) ?></td></tr>
                <?php endif; ?>
              </table>
            </div>
          </div>

          <!-- Prescription info (if exists) -->
          <?php if ($prescription): ?>
          <div class="card card-success card-outline">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-file-medical mr-2"></i>Prescription</h3></div>
            <div class="card-body">
              <p><strong>Diagnosis:</strong> <?= e($prescription['diagnosis']) ?></p>
              <p><strong>Medications:</strong> <?= nl2br(e($prescription['medications'])) ?></p>
              <?php if ($prescription['notes']): ?>
              <p><strong>Notes:</strong> <?= e($prescription['notes']) ?></p>
              <?php endif; ?>
              <?php if ($prescription['file_path'] && in_array($role, ['patient','admin'])): ?>
              <a href="<?= url('prescriptions','download') ?>&id=<?= (int)$appt['id'] ?>"
                 class="btn btn-sm btn-success">
                <i class="fas fa-download mr-1"></i> Download PDF
              </a>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Right: Actions -->
        <div class="col-md-5">

          <!-- Doctor: status update form -->
          <?php if (in_array($role, ['doctor','admin']) && $appt['status'] !== 'cancelled'): ?>
          <div class="card card-warning card-outline">
            <div class="card-header"><h3 class="card-title">Update Status</h3></div>
            <form method="POST" action="<?= url('appointments','status') ?>">
              <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
              <input type="hidden" name="appt_id"   value="<?= (int)$appt['id'] ?>">
              <div class="card-body">
                <div class="form-group">
                  <label>New Status</label>
                  <select name="status" class="form-control">
                    <?php
                    $allowed = match($appt['status']) {
                        'pending'   => ['confirmed','cancelled'],
                        'confirmed' => ['completed','cancelled'],
                        default     => [],
                    };
                    foreach ($allowed as $s): ?>
                    <option value="<?= $s ?>"><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Notes (optional)</label>
                  <textarea name="doctor_notes" class="form-control" rows="3"><?= e($appt['doctor_notes'] ?? '') ?></textarea>
                </div>
              </div>
              <?php if (!empty($allowed)): ?>
              <div class="card-footer">
                <button type="submit" class="btn btn-warning btn-block">
                  <i class="fas fa-sync mr-1"></i> Update Status
                </button>
              </div>
              <?php endif; ?>
            </form>

            <!-- Add prescription button when completed -->
            <?php if ($appt['status'] === 'completed' && !$prescription && $role === 'doctor'): ?>
            <div class="card-footer">
              <a href="<?= url('prescriptions','add') ?>&appt_id=<?= (int)$appt['id'] ?>"
                 class="btn btn-success btn-block">
                <i class="fas fa-file-medical mr-1"></i> Add Prescription
              </a>
            </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <!-- Patient: cancel form -->
          <?php if ($role === 'patient' && in_array($appt['status'], ['pending','confirmed'])): ?>
          <div class="card card-danger card-outline">
            <div class="card-header"><h3 class="card-title">Cancel Appointment</h3></div>
            <form method="POST" action="<?= url('appointments','cancel') ?>">
              <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
              <input type="hidden" name="appt_id"   value="<?= (int)$appt['id'] ?>">
              <div class="card-body">
                <div class="form-group">
                  <label>Reason for Cancellation <span class="text-danger">*</span></label>
                  <textarea name="cancel_reason" class="form-control" rows="3"
                            placeholder="Min 10 characters..." required></textarea>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-danger btn-block"
                        onclick="return confirm('Cancel this appointment?')">
                  <i class="fas fa-times mr-1"></i> Cancel Appointment
                </button>
              </div>
            </form>
          </div>
          <?php endif; ?>

          <a href="<?= url('appointments','index') ?>" class="btn btn-default btn-block">
            <i class="fas fa-arrow-left mr-1"></i> Back to Appointments
          </a>
        </div>
      </div>

    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
