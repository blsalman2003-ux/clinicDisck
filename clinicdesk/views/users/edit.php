<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('admin');
$pageTitle = 'Edit User';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Edit User</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('users','index') ?>">Users</a></li>
            <li class="breadcrumb-item active">Edit</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
      <div class="card card-warning">
        <div class="card-header"><h3 class="card-title">Edit: <?= e($user['name']) ?></h3></div>
        <form method="POST" action="<?= url('users','update') ?>">
          <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
          <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Full Name</label>
                  <input type="text" name="name" class="form-control"
                         value="<?= e($user['name']) ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Email <small class="text-muted">(cannot change)</small></label>
                  <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Phone</label>
                  <input type="text" name="phone" class="form-control"
                         value="<?= e($user['phone'] ?? '') ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Role <small class="text-muted">(cannot change)</small></label>
                  <input type="text" class="form-control"
                         value="<?= ucfirst(e($user['role'])) ?>" disabled>
                </div>
              </div>
            </div>

            <?php if ($user['role'] === 'doctor' && $doctorRecord): ?>
            <hr><h5 class="text-info"><i class="fas fa-user-md mr-1"></i>Doctor Details</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Specialization</label>
                  <select name="specialization_id" class="form-control">
                    <?php foreach ($specializations as $sp): ?>
                    <option value="<?= (int)$sp['id'] ?>"
                      <?= $doctorRecord['specialization_id']==$sp['id']?'selected':'' ?>>
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
                         step="0.01" value="<?= e($doctorRecord['consultation_fee']) ?>">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Available Days</label>
              <div class="d-flex flex-wrap">
                <?php
                $activeDays = explode(',', $doctorRecord['available_days'] ?? '');
                foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day):
                ?>
                <div class="custom-control custom-checkbox mr-3">
                  <input type="checkbox" class="custom-control-input"
                         id="day_<?= $day ?>" name="available_days[]" value="<?= $day ?>"
                         <?= in_array($day, $activeDays) ? 'checked' : '' ?>>
                  <label class="custom-control-label" for="day_<?= $day ?>"><?= $day ?></label>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="form-group">
              <label>Bio</label>
              <textarea name="bio" class="form-control" rows="3"><?= e($doctorRecord['bio'] ?? '') ?></textarea>
            </div>
            <?php endif; ?>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-warning">
              <i class="fas fa-save mr-1"></i> Save Changes
            </button>
            <a href="<?= url('users','index') ?>" class="btn btn-default ml-2">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
