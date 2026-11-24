<?php
$title = $title ?? 'Administrador';
$user = $_SESSION['user'] ?? ['name' => 'Administrador', 'email' => ''];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?> | Administración</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
  <div class="flex h-screen overflow-hidden">
    <?php require __DIR__ . '/../partials/admin-sidebar.php'; ?>

    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
      <?php require __DIR__ . '/../partials/admin-header.php'; ?>

      <main id="adminMain" class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-4 sm:p-6 lg:p-8">
        <div class="mx-auto w-full max-w-[1600px]">
          <?= $content ?? '' ?>
        </div>
      </main>
    </div>
  </div>

  <script>window.APP_URL = <?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES) ?>;</script>
  <script src="<?= BASE_URL ?>/assets/js/app.js" defer></script>
  <script src="<?= BASE_URL ?>/assets/js/admin.js" defer></script>
  <?php if (!empty($pageScript)): ?>
    <script src="<?= BASE_URL ?>/assets/js/<?= htmlspecialchars($pageScript) ?>" defer></script>
  <?php endif; ?>
</body>
</html>
