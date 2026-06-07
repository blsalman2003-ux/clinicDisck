<?php
$currentUser = Auth::currentUser();
$role        = $currentUser['role'];

function isActive(string $page, string $action = ''): string {
    $currentPage   = $_GET['page']   ?? 'dashboard';
    $currentAction = $_GET['action'] ?? 'index';
    if ($action === '') {
        return $currentPage === $page ? 'active' : '';
    }
    return ($currentPage === $page && $currentAction === $action) ? 'active' : '';
}
?>
<!-- Main Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

  <!-- Brand logo -->
  <a href="<?= url('dashboard') ?>" class="brand-link">
    <i class="fas fa-clinic-medical brand-image ml-3" style="font-size:1.8rem;color:#007bff;"></i>
    <span class="brand-text font-weight-bold ml-2"><?= APP_NAME ?></span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">

    <!-- User panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <i class="fas fa-user-circle fa-2x" style="color:#adb5bd;margin-top:4px;"></i>
      </div>
      <div class="info">
        <a href="<?= url('users', 'profile') ?>" class="d-block text-truncate" style="max-width:140px;">
          <?= e($currentUser['name']) ?>
        </a>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

        <!-- Dashboard — all roles -->
        <li class="nav-item">
          <a href="<?= url('dashboard') ?>" class="nav-link <?= isActive('dashboard') ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <?php if ($role === 'admin'): ?>
        <!-- ── Admin links ──────────────────────────── -->
        <li class="nav-header">MANAGEMENT</li>

        <li class="nav-item">
          <a href="<?= url('users', 'index') ?>" class="nav-link <?= isActive('users') ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Users</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('doctors', 'index') ?>" class="nav-link <?= isActive('doctors') ?>">
            <i class="nav-icon fas fa-user-md"></i>
            <p>Doctors</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('doctors', 'specializations') ?>" class="nav-link <?= isActive('doctors', 'specializations') ?>">
            <i class="nav-icon fas fa-stethoscope"></i>
            <p>Specializations</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('appointments', 'index') ?>" class="nav-link <?= isActive('appointments') ?>">
            <i class="nav-icon fas fa-calendar-check"></i>
            <p>Appointments</p>
          </a>
        </li>

        <li class="nav-header">REPORTS</li>
        <li class="nav-item">
          <a href="<?= url('reports', 'index') ?>" class="nav-link <?= isActive('reports') ?>">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Reports</p>
          </a>
        </li>
        <?php endif; ?>

        <?php if ($role === 'doctor'): ?>
        <!-- ── Doctor links ─────────────────────────── -->
        <li class="nav-header">MY PRACTICE</li>

        <li class="nav-item">
          <a href="<?= url('appointments', 'index') ?>" class="nav-link <?= isActive('appointments') ?>">
            <i class="nav-icon fas fa-calendar-alt"></i>
            <p>My Schedule</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('prescriptions', 'index') ?>" class="nav-link <?= isActive('prescriptions') ?>">
            <i class="nav-icon fas fa-file-medical"></i>
            <p>Prescriptions</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('doctors', 'profile') ?>" class="nav-link <?= isActive('doctors', 'profile') ?>">
            <i class="nav-icon fas fa-id-card"></i>
            <p>My Profile</p>
          </a>
        </li>
        <?php endif; ?>

        <?php if ($role === 'patient'): ?>
        <!-- ── Patient links ────────────────────────── -->
        <li class="nav-header">MY HEALTH</li>

        <li class="nav-item">
          <a href="<?= url('appointments', 'book') ?>" class="nav-link <?= isActive('appointments', 'book') ?>">
            <i class="nav-icon fas fa-plus-circle"></i>
            <p>Book Appointment</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('appointments', 'index') ?>" class="nav-link <?= isActive('appointments') ?>">
            <i class="nav-icon fas fa-calendar-check"></i>
            <p>My Appointments</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= url('prescriptions', 'index') ?>" class="nav-link <?= isActive('prescriptions') ?>">
            <i class="nav-icon fas fa-pills"></i>
            <p>My Prescriptions</p>
          </a>
        </li>
        <?php endif; ?>

        <!-- Profile — all roles -->
        <li class="nav-header">ACCOUNT</li>
        <li class="nav-item">
          <a href="<?= url('users', 'profile') ?>" class="nav-link <?= isActive('users', 'profile') ?>">
            <i class="nav-icon fas fa-user-cog"></i>
            <p>Edit Profile</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
  <!-- /Sidebar -->
</aside>
<!-- /Main Sidebar -->
