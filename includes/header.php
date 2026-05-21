<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';

$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo e(APP_NAME); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="brand">
    <a href="<?php echo BASE_URL; ?>/index.php">
        🪐 AstroGuide
    </a>
</div>
    <nav>
        <a class="<?php echo $current_page == 'astronomy-terms.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/astronomy-terms.php">Sözlük</a>

    <a class="<?php echo $current_page == 'planet-types.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/planet-types.php">Gezegen Türleri</a>

    <a class="<?php echo $current_page == 'planets.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/planets.php">Gök Cisimleri</a>

    <a class="<?php echo $current_page == 'exoplanets.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/exoplanets.php">Ötegezegenler</a>

    <a class="<?php echo $current_page == 'galaxies.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/galaxies.php">Galaksiler</a>

    <a class="<?php echo $current_page == 'today-in-space.php' ? 'active' : ''; ?>" href="today-in-space.php">Bugün Uzay Tarihinde</a>

    <a class="<?php echo $current_page == 'live-distances.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/live-distances.php">Uzaklıklar</a>

    <a class="<?php echo $current_page == 'astronomy-calendar.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/astronomy-calendar.php">Astronomi Takvimi</a>

    <a class="<?php echo $current_page == 'telescopes.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/telescopes.php">Teleskoplar</a>

    <a class="<?php echo $current_page == 'missions.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/missions.php">Görevler</a>

    <a class="<?php echo $current_page == 'events.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/events.php">Gök Olayları</a>

    <a class="<?php echo $current_page == 'apod.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/apod.php">Günün Görseli</a>

    <?php if (isContributor()): ?>
    <a class="<?php echo $current_page == 'suggest.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/suggest.php">Öneri Gönder</a>
    <?php endif; ?>
        <?php if (isEditor()): ?><a href="<?php echo BASE_URL; ?>/admin/dashboard.php">Editör Paneli</a><?php endif; ?>
        <?php if (isLoggedIn()): ?>
            <a class="<?php echo $current_page == 'favorites.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/favorites.php">Favorilerim</a>
            <a href="<?php echo BASE_URL; ?>/logout.php" class="btn-small">Çıkış</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/login.php" class="btn-small">Giriş</a>
            <a href="<?php echo BASE_URL; ?>/register.php" class="btn-small outline">Kayıt</a>
        <?php endif; ?>
    </nav>
</header>
<main>
    
