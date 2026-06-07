<?php
Auth::requireRole('admin');
$pageTitle = 'Edit Doctor';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
$activeDays = explode(',', $doctor['available_days'] ?? '');
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Edit Doctor</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('doctors','index') ?>">Doctors</a></li>
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
        <div class="card-header"><h3 class="card-title">Dr. <?= e($doctor['name']) ?></h3></div>
        <form method="POST" action="<?= url('doctors','update') ?>" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token"  value="<?= CSRF::generateToken() ?>">
          <input type="hidden" name="doctor_id"   value="<?= (int)$doctor['id'] ?>">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Specialization</label>
                  <select name="specialization_id" class="form-control">
                    <?php foreach ($specializations as $sp): ?>
                    <option value="<?= (int)$sp['id'] ?>"
                      <?= $doctor['specialization_id']==$sp['id']?'selected':'' ?>>
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
                         step="0.01" value="<?= e($doctor['consultation_fee']) ?>">
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
                         <?= in_array($day, $activeDays) ? 'checked' : '' ?>>
                  <label class="custom-control-label" for="day_<?= $day ?>"><?= $day ?></label>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="form-group">
              <label>Profile Photo <small class="text-muted">(JPEG/PNG, max 1MB)</small></label>
              <?php if (!empty($doctor['photo'])): ?>
              <div class="mb-2">
                <img src="<?= BASE_URL ?>/public/uploads/doctor_photos/<?= e($doctor['photo']) ?>"
                     alt="Photo" style="height:60px;border-radius:4px;">
              </div>
              <?php endif; ?>
              <div class="input-group">
                <div class="custom-file">
                  <input type="file" class="custom-file-input" name="photo" accept="image/*">
                  <label class="custom-file-label">Choose file...</label>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Bio</label>
              <textarea name="bio" class="form-control" rows="4"><?= e($doctor['bio'] ?? '') ?></textarea>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-warning">
              <i class="fas fa-save mr-1"></i> Save Changes
            </button>
            <a href="<?= url('doctors','index') ?>" class="btn btn-default ml-2">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
