<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>403 Forbidden | <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition">
<div class="wrapper">
  <section class="content" style="padding-top:80px;">
    <div class="error-page">
      <h2 class="headline text-warning">403</h2>
      <div class="error-content">
        <h3><i class="fas fa-exclamation-triangle text-warning"></i> Access Denied</h3>
        <p>You do not have permission to view this page.</p>
        <a href="<?= url('dashboard') ?>" class="btn btn-primary">
          <i class="fas fa-home mr-1"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </section>
</div>
</body>
</html>
