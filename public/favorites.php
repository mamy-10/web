<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS exoplanet_favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        exoplanet_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_exoplanet_favorite (user_id, exoplanet_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (exoplanet_id) REFERENCES exoplanets(id) ON DELETE CASCADE
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS galaxy_favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        galaxy_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_galaxy_favorite (user_id, galaxy_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (galaxy_id) REFERENCES galaxies(id) ON DELETE CASCADE
    )
");

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['favorite_type'] ?? '') === 'galaxy') {
    verifyCsrfToken();
    $galaxyId = filter_input(INPUT_POST, 'galaxy_id', FILTER_VALIDATE_INT);

    if ($galaxyId) {
        $stmt = $pdo->prepare("DELETE FROM galaxy_favorites WHERE user_id = ? AND galaxy_id = ?");
        $stmt->execute([$_SESSION['user_id'], $galaxyId]);
    }

    redirectTo('favorites.php');
}

$stmt = $pdo->prepare("
    SELECT planets.* FROM favorites
    JOIN planets ON favorites.planet_id = planets.id
    WHERE favorites.user_id = ?
    ORDER BY favorites.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT exoplanets.* FROM exoplanet_favorites
    JOIN exoplanets ON exoplanet_favorites.exoplanet_id = exoplanets.id
    WHERE exoplanet_favorites.user_id = ?
    ORDER BY exoplanet_favorites.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$exoplanetFavorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT galaxies.* FROM galaxy_favorites
    JOIN galaxies ON galaxy_favorites.galaxy_id = galaxies.id
    WHERE galaxy_favorites.user_id = ?
    ORDER BY galaxy_favorites.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$galaxyFavorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<h1>Favorilerim</h1>
<?php if (!$favorites && !$exoplanetFavorites && !$galaxyFavorites): ?><div class="alert error">Henüz favori eklemedin.</div><?php endif; ?>

<?php if ($favorites): ?>
<h2>Gök Cisimleri</h2>
<div class="grid">
<?php foreach ($favorites as $planet): ?>
    <div class="card">
        <img class="card-img" src="<?php echo e($planet['image_url']); ?>" alt="<?php echo e($planet['name']); ?> görseli">
        <h3><?php echo e($planet['name']); ?></h3>
        <p><?php echo e(mb_substr($planet['description'], 0, 120)); ?>...</p>
        <a class="btn-small" href="planet-detail.php?id=<?php echo e($planet['id']); ?>">Detay</a>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($exoplanetFavorites): ?>
<h2>Ötegezegenler</h2>
<div class="grid">
<?php foreach ($exoplanetFavorites as $exoplanet): ?>
    <div class="card">
        <img class="card-img" src="<?php echo e($exoplanet['image_url']); ?>" alt="<?php echo e($exoplanet['name']); ?> görseli">
        <h3><?php echo e($exoplanet['name']); ?></h3>
        <p class="muted"><?php echo e($exoplanet['system_name']); ?> · <?php echo e($exoplanet['planet_type'] ?? '-'); ?></p>
        <p><?php echo e(mb_substr($exoplanet['description'], 0, 120)); ?>...</p>
        <a class="btn-small" href="exoplanet-detail.php?id=<?php echo e($exoplanet['id']); ?>">Detay</a>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($galaxyFavorites): ?>
<h2>Galaksiler</h2>
<div class="grid">
<?php foreach ($galaxyFavorites as $galaxy): ?>
    <div class="card">
        <img class="card-img" src="<?php echo e($galaxy['image_url']); ?>" alt="<?php echo e($galaxy['name']); ?> görseli">
        <h3><?php echo e($galaxy['name']); ?></h3>
        <p class="muted"><?php echo e($galaxy['galaxy_type']); ?> · <?php echo e($galaxy['constellation'] ?? '-'); ?></p>
        <p><?php echo e(mb_substr($galaxy['description'], 0, 120)); ?>...</p>
        <form method="POST">
            <?php echo csrfField(); ?>
            <input type="hidden" name="favorite_type" value="galaxy">
            <input type="hidden" name="galaxy_id" value="<?php echo e($galaxy['id']); ?>">
            <button class="danger" type="submit">Favorilerden Çıkar</button>
        </form>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>
