<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | <?= APP_NAME ?></title>
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">

<div class="login-box">

  <!-- Logo -->
  <div class="login-logo">
    <a href="#">
      <i class="fas fa-clinic-medical text-primary" style="font-size:2.5rem;"></i><br>
      <b><?= APP_NAME ?></b>
    </a>
    <p class="text-muted mt-1" style="font-size:.9rem;">Clinic Management Dashboard</p>
  </div>

  <!-- Flash message (error from failed login) -->
  <?php if (!empty($_SESSION['flash'])):
      $flash = $_SESSION['flash'];
      unset($_SESSION['flash']);
  ?>
  <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?= e($flash['message']) ?>
  </div>
  <?php endif; ?>

  <!-- Login card -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to your account</p>

      <form method="POST" action="<?= url('auth', 'login') ?>">
        <!-- CSRF token — every POST form must have this -->
        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

        <!-- Email -->
        <div class="input-group mb-3">
          <input type="email"
                 name="email"
                 class="form-control"
                 placeholder="Email address"
                 value="<?= old('email') ?>"
                 required
                 autofocus>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
          </div>
        </div>

        <!-- Password -->
        <div class="input-group mb-3">
          <input type="password"
                 name="password"
                 class="form-control"
                 placeholder="Password"
                 required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>

        <!-- Submit -->
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">
              <i class="fas fa-sign-in-alt mr-1"></i> Sign In
            </button>
          </div>
        </div>

      </form>

      <!-- No registration link — admin creates all accounts -->
      <p class="mt-3 mb-0 text-center text-muted" style="font-size:.85rem;">
        <i class="fas fa-info-circle"></i>
        Contact your administrator if you need an account.
      </p>

    </div>
    <!-- /card-body -->
  </div>
  <!-- /card -->

</div>
<!-- /login-box -->

<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
