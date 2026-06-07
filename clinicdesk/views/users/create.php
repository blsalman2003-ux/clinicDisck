<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('admin');
$pageTitle = 'Add User';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Add User</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('users','index') ?>">Users</a></li>
            <li class="breadcrumb-item active">Add</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

      <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">User Information</h3></div>
        <form method="POST" action="<?= url('users','store') ?>" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Full Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control"
                         value="<?= old('name') ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Email Address <span class="text-danger">*</span></label>
                  <input type="email" name="email" class="form-control"
                         value="<?= old('email') ?>" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Role <span class="text-danger">*</span></label>
                  <select name="role" id="roleSelect" class="form-control" required>
                    <option value="">— Select Role —</option>
                    <option value="patient" <?= old('role')==='patient'?'selected':'' ?>>Patient</option>
                    <option value="doctor"  <?= old('role')==='doctor' ?'selected':'' ?>>Doctor</option>
                    <option value="admin"   <?= old('role')==='admin'  ?'selected':'' ?>>Admin</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Phone</label>
                  <input type="text" name="phone" class="form-control"
                         value="<?= old('phone') ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Temporary Password <span class="text-danger">*</span></label>
                  <input type="password" name="password" class="form-control"
                         placeholder="Min 8 characters" required>
                </div>
              </div>
            </div>

            <!-- Doctor-specific fields (shown only when role=doctor) -->
            <div id="doctorFields" style="display:none;">
              <hr><h5 class="text-info"><i class="fas fa-user-md mr-1"></i> Doctor Details</h5>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Specialization <span class="text-danger">*</span></label>
                    <select name="specialization_id" class="form-control">
                      <option value="">— Select —</option>
                      <?php foreach ($specializations as $sp): ?>
                      <option value="<?= (int)$sp['id'] ?>"
                        <?= old('specialization_id')==$sp['id']?'selected':'' ?>>
                        <?= e($sp['name']) ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Consultation Fee ($)</label>
                    <input type="number" name="consultation_fee" class="form-control"
                           step="0.01" min="0" value="<?= old('consultation_fee','0') ?>">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Available Days</label>
                <div class="d-flex flex-wrap">
                  <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
                  <div class="custom-control custom-checkbox mr-3">
                    <input type="checkbox" class="custom-control-input"
                           id="day_<?= $day ?>" name="available_days[]" value="<?= $day ?>"
                           <?= in_array($day,['Sun','Mon','Tue','Wed','Thu'])?'checked':'' ?>>
                    <label class="custom-control-label" for="day_<?= $day ?>"><?= $day ?></label>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" class="form-control" rows="3"><?= old('bio') ?></textarea>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i> Create User
            </button>
            <a href="<?= url('users','index') ?>" class="btn btn-default ml-2">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<?php
$oldRole = old('role');
$extraJs = <<<JS
<script>
document.getElementById('roleSelect').addEventListener('change', function() {
  document.getElementById('doctorFields').style.display =
    this.value === 'doctor' ? 'block' : 'none';
});

if ('$oldRole' === 'doctor') {
  document.getElementById('doctorFields').style.display = 'block';
}
</script>
JS;
?>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
