<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>404 Not Found | <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition">
<div class="wrapper">
  <section class="content" style="padding-top:80px;">
    <div class="error-page">
      <h2 class="headline text-danger">404</h2>
      <div class="error-content">
        <h3><i class="fas fa-times-circle text-danger"></i> Page Not Found</h3>
        <p>The page you are looking for does not exist.</p>
        <a href="<?= url('dashboard') ?>" class="btn btn-primary">
          <i class="fas fa-home mr-1"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </section>
</div>
</body>
</html>
