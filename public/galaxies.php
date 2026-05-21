<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$message = '';

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    requireLogin();
    verifyCsrfToken();
    $galaxyId = filter_input(INPUT_POST, 'galaxy_id', FILTER_VALIDATE_INT);
    $action = $_POST['favorite_action'] ?? 'add';

    if ($galaxyId) {
        $stmt = $pdo->prepare("SELECT id, name FROM galaxies WHERE id = ?");
        $stmt->execute([$galaxyId]);
        $galaxy = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($galaxy) {
            $check = $pdo->prepare("SELECT id FROM galaxy_favorites WHERE user_id = ? AND galaxy_id = ?");
            $check->execute([$_SESSION['user_id'], $galaxyId]);
            $favorite = $check->fetch();

            if ($action === 'remove') {
                if ($favorite) {
                    $stmt = $pdo->prepare("DELETE FROM galaxy_favorites WHERE user_id = ? AND galaxy_id = ?");
                    $stmt->execute([$_SESSION['user_id'], $galaxyId]);
                    $message = $galaxy['name'] . ' favorilerinden çıkarıldı.';
                } else {
                    $message = $galaxy['name'] . ' favorilerinde değil.';
                }
            } elseif ($favorite) {
                $message = $galaxy['name'] . ' zaten favorilerinde.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO galaxy_favorites (user_id, galaxy_id) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user_id'], $galaxyId]);
                $message = $galaxy['name'] . ' favorilerine eklendi.';
            }
        }
    }
}

include '../includes/header.php';
$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM galaxies WHERE 1";
$params = [];
if ($q !== '') {
    $sql .= " AND (name LIKE ? OR galaxy_type LIKE ? OR constellation LIKE ? OR description LIKE ?)";
    $params = ["%$q%", "%$q%", "%$q%", "%$q%"];
}
$sql .= " ORDER BY distance_light_years ASC, name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$favoriteGalaxyIds = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT galaxy_id FROM galaxy_favorites WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $favoriteGalaxyIds = array_flip($stmt->fetchAll(PDO::FETCH_COLUMN));
}
?>
<h1>Yakın Galaksiler</h1>
<p class="muted">Samanyolu çevresindeki galaksileri ve temel özelliklerini keşfet.</p>
<?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>
<form class="search-bar" method="GET">
    <input name="q" placeholder="Andromeda, sarmal, Macellan..." value="<?php echo e($q); ?>">
    <button>Ara</button>
</form>
<div class="grid">
<?php foreach ($items as $item): ?>
    <div class="card">
        <img class="card-img" src="<?php echo e($item['image_url']); ?>" alt="<?php echo e($item['name']); ?> görseli">
        <h3><?php echo e($item['name']); ?></h3>
        <p class="muted"><?php echo e($item['galaxy_type']); ?> · <?php echo e($item['constellation']); ?></p>
        <p><?php echo e($item['description']); ?></p>
        <p><strong>Uzaklık:</strong> <?php echo $item['distance_light_years'] !== null ? e(number_format((float)$item['distance_light_years'], 0, ',', '.')).' ışık yılı' : '-'; ?></p>
        <?php if (isLoggedIn()): ?>
            <form method="POST">
                <?php echo csrfField(); ?>
                <input type="hidden" name="galaxy_id" value="<?php echo e($item['id']); ?>">
                <?php if (isset($favoriteGalaxyIds[$item['id']])): ?>
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
<?php endforeach; ?>
</div>
<?php include '../includes/footer.php'; ?>
