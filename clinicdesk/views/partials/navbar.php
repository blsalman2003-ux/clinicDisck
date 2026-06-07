<?php

$currentUser = Auth::currentUser();
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

  <!-- Left: sidebar toggle -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="<?= url('dashboard') ?>" class="nav-link"><?= APP_NAME ?></a>
    </li>
  </ul>

  <!-- Right: user menu + logout -->
  <ul class="navbar-nav ml-auto">

    <!-- Role badge -->
    <li class="nav-item d-none d-sm-inline-block">
      <span class="nav-link">
        <?php
          $roleColor = match($currentUser['role']) {
            'admin'   => 'danger',
            'doctor'  => 'info',
            'patient' => 'success',
            default   => 'secondary',
          };
        ?>
        <span class="badge badge-<?= $roleColor ?>">
          <?= ucfirst(e($currentUser['role'])) ?>
        </span>
      </span>
    </li>

    <!-- User dropdown -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-user-circle"></i>
        <span class="ml-1"><?= e($currentUser['name']) ?></span>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a href="<?= url('users', 'profile') ?>" class="dropdown-item">
          <i class="fas fa-user mr-2"></i> My Profile
        </a>
        <div class="dropdown-divider"></div>
        <!-- Logout is a POST form — GET logout is a CSRF vulnerability -->
        <form method="POST" action="<?= url('auth', 'logout') ?>">
          <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
          <button type="submit" class="dropdown-item text-danger">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </button>
        </form>
      </div>
    </li>

  </ul>
</nav>
<!-- /Navbar -->
