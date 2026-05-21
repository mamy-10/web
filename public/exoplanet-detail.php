<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$message = '';

$stmt = $pdo->prepare("SELECT * FROM exoplanets WHERE id = ?");
$stmt->execute([$id]);
$exoplanet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exoplanet) {
    http_response_code(404);
    die("Ötegezegen bulunamadı.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    requireLogin();
    verifyCsrfToken();
    $action = $_POST['favorite_action'] ?? 'add';
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
    $check = $pdo->prepare("SELECT id FROM exoplanet_favorites WHERE user_id = ? AND exoplanet_id = ?");
    $check->execute([$_SESSION['user_id'], $exoplanet['id']]);
    $favorite = $check->fetch();

    if ($action === 'remove') {
        if ($favorite) {
            $stmt = $pdo->prepare("DELETE FROM exoplanet_favorites WHERE user_id = ? AND exoplanet_id = ?");
            $stmt->execute([$_SESSION['user_id'], $exoplanet['id']]);
            $message = 'Favorilerinden çıkarıldı.';
        } else {
            $message = 'Bu ötegezegen favorilerinde değil.';
        }
    } elseif ($favorite) {
        $message = 'Bu ötegezegen zaten favorilerinde.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO exoplanet_favorites (user_id, exoplanet_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $exoplanet['id']]);
        $message = 'Favorilerine eklendi.';
    }
}

$isFavorite = false;
if (isLoggedIn()) {
    $check = $pdo->prepare("SELECT id FROM exoplanet_favorites WHERE user_id = ? AND exoplanet_id = ?");
    $check->execute([$_SESSION['user_id'], $exoplanet['id']]);
    $isFavorite = (bool)$check->fetch();
}

include '../includes/header.php';
?>
<div class="grid two">
    <div>
        <img class="apod-img" src="<?php echo e($exoplanet['image_url']); ?>" alt="<?php echo e($exoplanet['name']); ?> görseli">
    </div>
    <div class="card">
        <h1><?php echo e($exoplanet['name']); ?></h1>
        <?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>
        <p class="muted"><?php echo e($exoplanet['system_name']); ?> · <?php echo e($exoplanet['planet_type'] ?? 'Tür bilgisi yok'); ?></p>
        <p><?php echo nl2br(e($exoplanet['description'])); ?></p>

        <div class="info-grid">
            <div><strong>Sistem</strong><span><?php echo e($exoplanet['system_name']); ?></span></div>
            <div><strong>Gezegen Türü</strong><span><?php echo e($exoplanet['planet_type'] ?? '-'); ?></span></div>
            <div><strong>Uzaklık</strong><span><?php echo $exoplanet['distance_light_years'] !== null ? e($exoplanet['distance_light_years']) . ' ışık yılı' : '-'; ?></span></div>
            <div><strong>Keşif Yılı</strong><span><?php echo e($exoplanet['discovery_year'] ?? '-'); ?></span></div>
            <div><strong>Keşif Yöntemi</strong><span><?php echo e($exoplanet['discovery_method'] ?? '-'); ?></span></div>
            <div><strong>Yaşanabilirlik</strong><span><?php echo e($exoplanet['habitability_note'] ?? 'Veri yok'); ?></span></div>
        </div>
        <?php if (isLoggedIn()): ?>
            <form method="POST">
                <?php echo csrfField(); ?>
                <?php if ($isFavorite): ?>
                    <input type="hidden" name="favorite_action" value="remove">
                    <button class="danger" type="submit">Favorilerden Çıkar</button>
                <?php else: ?>
                    <input type="hidden" name="favorite_action" value="add">
                    <button type="submit">Favorilere Ekle</button>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <p class="muted">Favorilere eklemek için giriş yapmalısın.</p>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
