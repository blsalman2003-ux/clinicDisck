<?php
Auth::requireRole('doctor');
$pageTitle = 'Add Prescription';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Add Prescription</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('appointments','index') ?>">Appointments</a></li>
            <li class="breadcrumb-item active">Prescription</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

      <!-- Appointment summary -->
      <div class="callout callout-info">
        <h5>Patient: <?= e($appt['patient_name']) ?></h5>
        <p class="mb-0">
          <?= formatDate($appt['appt_date']) ?> at <?= formatTime($appt['appt_time']) ?> —
          Reason: <?= e($appt['reason'] ?? 'Not specified') ?>
        </p>
      </div>

      <div class="card card-success">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-file-medical mr-2"></i>Prescription Details</h3>
        </div>
        <form method="POST" action="<?= url('prescriptions','store') ?>" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
          <input type="hidden" name="appt_id"   value="<?= (int)$appt['id'] ?>">
          <div class="card-body">
            <div class="form-group">
              <label>Diagnosis <span class="text-danger">*</span></label>
              <textarea name="diagnosis" class="form-control" rows="3" required
                        placeholder="Primary diagnosis..."><?= old('diagnosis') ?></textarea>
            </div>
            <div class="form-group">
              <label>Medications <span class="text-danger">*</span></label>
              <textarea name="medications" class="form-control" rows="4" required
                        placeholder="List medications, dosage, frequency..."><?= old('medications') ?></textarea>
            </div>
            <div class="form-group">
              <label>Additional Notes</label>
              <textarea name="notes" class="form-control" rows="2"
                        placeholder="Follow-up instructions, lifestyle advice..."><?= old('notes') ?></textarea>
            </div>
            <div class="form-group">
              <label>Attach PDF <small class="text-muted">(optional, max 3MB)</small></label>
              <div class="input-group">
                <div class="custom-file">
                  <input type="file" class="custom-file-input" name="prescription_file" accept=".pdf">
                  <label class="custom-file-label">Choose PDF file...</label>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save mr-1"></i> Save Prescription
            </button>
            <a href="<?= url('appointments','detail') ?>&id=<?= (int)$appt['id'] ?>"
               class="btn btn-default ml-2">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
