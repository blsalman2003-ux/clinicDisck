<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?><?= APP_NAME ?></title>

  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">

  <?php if (!empty($extraCss)) echo $extraCss; ?>
</head>
<body class="hold-transition <?= isset($bodyClass) ? e($bodyClass) : 'sidebar-mini' ?>">
<div class="wrapper">
