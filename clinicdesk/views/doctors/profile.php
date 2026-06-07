<?php
Auth::requireRole('doctor');
$pageTitle = 'My Profile';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
$activeDays = explode(',', $doctor['available_days'] ?? '');
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">My Doctor Profile</h1>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
      <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Profile Details</h3></div>
        <form method="POST" action="<?= url('doctors','update_profile') ?>" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
          <input type="hidden" name="doctor_id"  value="<?= (int)$doctor['id'] ?>">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 text-center">
                <?php if (!empty($doctor['photo'])): ?>
                <img src="<?= BASE_URL ?>/public/uploads/doctor_photos/<?= e($doctor['photo']) ?>"
                     class="img-fluid rounded-circle mb-2" style="max-height:120px;" alt="Photo">
                <?php else: ?>
                <i class="fas fa-user-md fa-5x text-muted mb-2"></i>
                <?php endif; ?>
                <div class="form-group">
                  <input type="file" class="form-control-file" name="photo" accept="image/*">
                  <small class="text-muted">JPEG/PNG, max 1MB</small>
                </div>
              </div>
              <div class="col-md-9">
                <p><strong>Name:</strong> <?= e($doctor['name']) ?></p>
                <p><strong>Email:</strong> <?= e($doctor['email']) ?></p>
                <p><strong>Specialization:</strong>
                  <span class="badge badge-info"><?= e($doctor['specialization_name']) ?></span>
                </p>
                <div class="form-group">
                  <label>Consultation Fee ($)</label>
                  <input type="number" name="consultation_fee" class="form-control"
                         step="0.01" value="<?= e($doctor['consultation_fee']) ?>">
                </div>
                <div class="form-group">
                  <label>Available Days</label>
                  <div class="d-flex flex-wrap">
                    <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
                    <div class="custom-control custom-checkbox mr-3">
                      <input type="checkbox" class="custom-control-input"
                             id="d_<?= $day ?>" name="available_days[]" value="<?= $day ?>"
                             <?= in_array($day, $activeDays) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="d_<?= $day ?>"><?= $day ?></label>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="form-group">
                  <label>Bio</label>
                  <textarea name="bio" class="form-control" rows="3"><?= e($doctor['bio'] ?? '') ?></textarea>
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
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
