<?php
Auth::requireRole('admin','doctor','patient');
$pageTitle = 'My Profile';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">My Profile</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Profile</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
      <div class="row">
        <div class="col-md-4">
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <div class="text-center">
                <?php if (!empty($user['avatar'])): ?>
                  <img class="profile-user-img img-fluid img-circle"
                       src="<?= BASE_URL ?>/public/uploads/avatars/<?= e($user['avatar']) ?>"
                       alt="Avatar">
                <?php else: ?>
                  <i class="fas fa-user-circle" style="font-size:5rem;color:#adb5bd;"></i>
                <?php endif; ?>
              </div>
              <h3 class="profile-username text-center mt-2"><?= e($user['name']) ?></h3>
              <p class="text-muted text-center">
                <span class="badge badge-<?= match($user['role']){
                  'admin'=>'danger','doctor'=>'info',default=>'success'} ?>">
                  <?= ucfirst(e($user['role'])) ?>
                </span>
              </p>
              <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                  <b>Email</b> <span class="float-right"><?= e($user['email']) ?></span>
                </li>
                <li class="list-group-item">
                  <b>Phone</b> <span class="float-right"><?= e($user['phone'] ?? '—') ?></span>
                </li>
                <li class="list-group-item">
                  <b>Member Since</b>
                  <span class="float-right"><?= formatDate($user['created_at']) ?></span>
                </li>
              </ul>
              <a href="<?= url('users','change_password') ?>" class="btn btn-outline-warning btn-block">
                <i class="fas fa-key mr-1"></i> Change Password
              </a>
            </div>
          </div>
        </div>

        <div class="col-md-8">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Edit Profile</h3></div>
            <form method="POST" action="<?= url('users','update_profile') ?>"
                  enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
              <div class="card-body">
                <div class="form-group">
                  <label>Full Name</label>
                  <input type="text" name="name" class="form-control"
                         value="<?= e($user['name']) ?>" required>
                </div>
                <div class="form-group">
                  <label>Phone</label>
                  <input type="text" name="phone" class="form-control"
                         value="<?= e($user['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                  <label>Profile Picture <small class="text-muted">(JPEG/PNG, max 1MB)</small></label>
                  <div class="input-group">
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" name="avatar"
                             accept="image/jpeg,image/png">
                      <label class="custom-file-label">Choose file...</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save mr-1"></i> Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
