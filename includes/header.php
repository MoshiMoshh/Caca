<?php
declare(strict_types=1);

/**
 * Layout bagian atas. Halaman pemanggil boleh menyetel $pageTitle lebih dulu.
 */

$pageTitle = $pageTitle ?? 'Data Mahasiswa';
$flash = take_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> &middot; Sistem Informasi Data Mahasiswa</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<main class="page">
    <div class="app-card">
        <div class="app-card__header">
            <h1 class="app-card__title"><?= e($pageTitle) ?></h1>
        </div>
        <div class="app-card__body">
<?php if ($flash !== null): ?>
            <div class="app-alert app-alert--<?= e($flash['type'] === 'success' ? 'success' : 'danger') ?>" role="alert">
                <?= e((string) $flash['message']) ?>
            </div>
<?php endif; ?>
