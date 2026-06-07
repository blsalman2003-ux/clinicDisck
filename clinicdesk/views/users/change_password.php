<?php
Auth::requireRole('admin','doctor','patient');
$pageTitle = 'Change Password';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">Change Password</h1>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-key mr-2"></i>Change Password</h3></div>
            <form method="POST" action="<?= url('users','change_password') ?>">
              <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
              <div class="card-body">
                <div class="form-group">
                  <label>Current Password</label>
                  <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>New Password</label>
                  <input type="password" name="new_password" class="form-control"
                         placeholder="Min 8 chars, uppercase + number" required>
                </div>
                <div class="form-group">
                  <label>Confirm New Password</label>
                  <input type="password" name="confirm_password" class="form-control" required>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-warning">
                  <i class="fas fa-save mr-1"></i> Update Password
                </button>
                <a href="<?= url('dashboard') ?>" class="btn btn-default ml-2">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
