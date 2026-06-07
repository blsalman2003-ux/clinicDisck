<?php
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('admin');
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Users</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-users mr-2"></i>All Users</h3>
          <div class="card-tools d-flex">
            <!-- Search & Filter -->
            <form method="GET" action="<?= url('users','index') ?>" class="d-flex mr-2">
              <input type="hidden" name="page" value="users">
              <input type="hidden" name="action" value="index">
              <input type="text" name="search" class="form-control form-control-sm mr-1"
                     placeholder="Search name/email..." value="<?= e($search ?? '') ?>">
              <select name="role" class="form-control form-control-sm mr-1">
                <option value="">All Roles</option>
                <?php foreach (['admin','doctor','patient'] as $r): ?>
                <option value="<?= $r ?>" <?= ($roleFilter??'') === $r ? 'selected' : '' ?>>
                  <?= ucfirst($r) ?>
                </option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-sm btn-default">
                <i class="fas fa-search"></i>
              </button>
            </form>
            <a href="<?= url('users','create') ?>" class="btn btn-sm btn-primary">
              <i class="fas fa-plus mr-1"></i> Add User
            </a>
          </div>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th>#</th><th>Name</th><th>Email</th><th>Role</th>
                <th>Phone</th><th>Status</th><th>Created</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($users)): ?>
              <tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
              <?php else: ?>
              <?php foreach ($users as $u): ?>
              <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= e($u['name']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td>
                  <span class="badge badge-<?= match($u['role']){
                    'admin'=>'danger','doctor'=>'info',default=>'success'} ?>">
                    <?= ucfirst(e($u['role'])) ?>
                  </span>
                </td>
                <td><?= e($u['phone'] ?? '—') ?></td>
                <td>
                  <?php if ($u['is_active']): ?>
                    <span class="badge badge-success">Active</span>
                  <?php else: ?>
                    <span class="badge badge-secondary">Suspended</span>
                  <?php endif; ?>
                </td>
                <td><?= formatDate($u['created_at']) ?></td>
                <td>
                  <a href="<?= url('users','edit') ?>&id=<?= (int)$u['id'] ?>"
                     class="btn btn-xs btn-info" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <?php if ($u['id'] !== Auth::currentUser()['id']): ?>
                  <form method="POST" action="<?= url('users','toggle') ?>" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                    <button type="submit" class="btn btn-xs btn-<?= $u['is_active'] ? 'warning' : 'success' ?>"
                            title="<?= $u['is_active'] ? 'Suspend' : 'Activate' ?>"
                            onclick="return confirm('<?= $u['is_active'] ? 'Suspend' : 'Activate' ?> this user?')">
                      <i class="fas fa-<?= $u['is_active'] ? 'ban' : 'check' ?>"></i>
                    </button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <?php if ($pager->totalPages() > 1): ?>
        <div class="card-footer clearfix">
          <?php require_once __DIR__ . '/../partials/pagination.php'; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
