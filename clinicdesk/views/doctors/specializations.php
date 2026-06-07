<?php
Auth::requireRole('admin');
$pageTitle = 'Specializations';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Specializations</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('doctors','index') ?>">Doctors</a></li>
            <li class="breadcrumb-item active">Specializations</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>
      <div class="row">
        <!-- Add form -->
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Add Specialization</h3></div>
            <form method="POST" action="<?= url('doctors','store_spec') ?>">
              <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
              <div class="card-body">
                <div class="form-group">
                  <label>Name</label>
                  <input type="text" name="name" class="form-control"
                         placeholder="e.g. Cardiology" required>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fas fa-plus mr-1"></i> Add
                </button>
              </div>
            </form>
          </div>
        </div>
        <!-- List -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-header"><h3 class="card-title">All Specializations</h3></div>
            <div class="card-body p-0">
              <table class="table table-striped mb-0">
                <thead class="thead-light">
                  <tr><th>#</th><th>Name</th><th>Action</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($specializations as $sp): ?>
                  <tr>
                    <td><?= (int)$sp['id'] ?></td>
                    <td><?= e($sp['name']) ?></td>
                    <td>
                      <form method="POST" action="<?= url('doctors','delete_spec') ?>" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="id" value="<?= (int)$sp['id'] ?>">
                        <button type="submit" class="btn btn-xs btn-danger"
                                onclick="return confirm('Delete this specialization?')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
