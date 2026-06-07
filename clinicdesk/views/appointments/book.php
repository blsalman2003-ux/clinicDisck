<?php
Auth::requireRole('patient');
$pageTitle = 'Book Appointment';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Book Appointment</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Book</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card card-success">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-calendar-plus mr-2"></i>New Appointment</h3>
            </div>
            <form method="POST" action="<?= url('appointments','store') ?>">
              <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
              <div class="card-body">

                <!-- Doctor -->
                <div class="form-group">
                  <label>Select Doctor <span class="text-danger">*</span></label>
                  <select name="doctor_id" id="doctorSelect" class="form-control" required>
                    <option value="">— Choose a Doctor —</option>
                    <?php foreach ($doctors as $d): ?>
                    <option value="<?= (int)$d['id'] ?>"
                            data-days="<?= e($d['available_days']) ?>"
                            data-fee="<?= e($d['consultation_fee']) ?>"
                            <?= old('doctor_id')==$d['id']?'selected':'' ?>>
                      Dr. <?= e($d['name']) ?> — <?= e($d['specialization_name']) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                  <!-- Available days shown dynamically -->
                  <small id="availableDaysInfo" class="text-info mt-1 d-block"></small>
                </div>

                <div class="row">
                  <!-- Date -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Preferred Date <span class="text-danger">*</span></label>
                      <input type="date" name="appt_date" id="apptDate" class="form-control"
                             min="<?= date('Y-m-d') ?>"
                             value="<?= old('appt_date') ?>" required>
                    </div>
                  </div>
                  <!-- Time slot -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Time Slot <span class="text-danger">*</span></label>
                      <select name="appt_time" class="form-control" required>
                        <option value="">— Select Time —</option>
                        <?php foreach ($timeSlots as $slot): ?>
                        <option value="<?= $slot ?>" <?= old('appt_time')===$slot?'selected':'' ?>>
                          <?= formatTime($slot . ':00') ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Reason -->
                <div class="form-group">
                  <label>Reason for Visit</label>
                  <input type="text" name="reason" class="form-control"
                         placeholder="Brief description..." value="<?= old('reason') ?>">
                </div>

                <!-- Fee info -->
                <div id="feeInfo" class="callout callout-info" style="display:none;">
                  <h5><i class="fas fa-info-circle mr-1"></i>Consultation Fee</h5>
                  <p class="mb-0">Fee: <strong id="feeAmount"></strong></p>
                </div>

              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-success btn-lg">
                  <i class="fas fa-check mr-1"></i> Confirm Booking
                </button>
                <a href="<?= url('appointments','index') ?>" class="btn btn-default btn-lg ml-2">
                  Cancel
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php $extraJs = <<<'JS'
<script>
document.getElementById('doctorSelect').addEventListener('change', function() {
  const opt = this.options[this.selectedIndex];
  const days = opt.dataset.days || '';
  const fee  = opt.dataset.fee  || '';
  const info = document.getElementById('availableDaysInfo');
  const feeBox = document.getElementById('feeInfo');

  if (days) {
    info.textContent = '✅ Available: ' + days.replace(/,/g, ', ');
    feeBox.style.display = 'block';
    document.getElementById('feeAmount').textContent = '$' + parseFloat(fee).toFixed(2);
  } else {
    info.textContent = '';
    feeBox.style.display = 'none';
  }
});

if (document.getElementById('doctorSelect').value) {
  document.getElementById('doctorSelect').dispatchEvent(new Event('change'));
}
</script>
JS;
?>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
