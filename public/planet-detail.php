<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$message = '';
$error = '';
$earthWeight = '';
$calculatedWeight = null;

$stmt = $pdo->prepare("SELECT * FROM planets WHERE id = ?");
$stmt->execute([$id]);
$planet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$planet) {
    http_response_code(404);
    die("Gezegen bulunamadı.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['form_type'] ?? '') === 'weight') {
    $earthWeight = trim($_POST['earth_weight'] ?? '');

    if (!is_numeric($earthWeight) || (float)$earthWeight <= 0) {
        $error = 'Lütfen geçerli bir kilo değeri gir.';
    } else {
        $calculatedWeight = (float)$earthWeight * (float)$planet['gravity_multiplier'];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['form_type'] ?? '') === 'favorite') {
    requireLogin();
    verifyCsrfToken();
    $action = $_POST['favorite_action'] ?? 'add';
    $check = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND planet_id = ?");
    $check->execute([$_SESSION['user_id'], $planet['id']]);
    $favorite = $check->fetch();

    if ($action === 'remove') {
        if ($favorite) {
            $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND planet_id = ?");
            $stmt->execute([$_SESSION['user_id'], $planet['id']]);
            $message = 'Favorilerinden çıkarıldı.';
        } else {
            $message = 'Bu gezegen favorilerinde değil.';
        }
    } elseif ($favorite) {
        $message = 'Bu gezegen zaten favorilerinde.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, planet_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $planet['id']]);
        $message = 'Favorilerine eklendi.';
    }
}

$isFavorite = false;
if (isLoggedIn()) {
    $check = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND planet_id = ?");
    $check->execute([$_SESSION['user_id'], $planet['id']]);
    $isFavorite = (bool)$check->fetch();
}

include '../includes/header.php';
?>
<div class="grid two">
    <div>
        <img class="apod-img" src="<?php echo e($planet['image_url']); ?>" alt="<?php echo e($planet['name']); ?> görseli">
    </div>
    <div class="card">
        <h1><?php echo e($planet['name']); ?></h1>
        <?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
        <p class="muted">Tür: <?php echo e($planet['type']); ?></p>
        <p><?php echo nl2br(e($planet['description'])); ?></p>
        <div class="info-grid">
            <div><strong>Yerçekimi</strong><span><?php echo e($planet['gravity_multiplier']); ?> g</span></div>
            <div><strong>Yarıçap</strong><span><?php echo $planet['radius_km'] !== null ? e(number_format((float)$planet['radius_km'], 0, ',', '.')) . ' km' : '-'; ?></span></div>
            <div><strong>Güneş Uzaklığı</strong><span><?php echo $planet['distance_from_sun_million_km'] !== null ? e($planet['distance_from_sun_million_km']) . ' milyon km' : '-'; ?></span></div>
            <?php if ($planet['type'] !== 'uydu'): ?>
            <div class="detail-stat">
            <span class="label">Uydu</span>
            <span class="value"><?php echo e($planet['moons']); ?></span>
            </div>
            <?php endif; ?>
            <div><strong>Ortalama Sıcaklık</strong><span><?php echo $planet['average_temperature_c'] !== null ? e($planet['average_temperature_c']) . ' °C' : '-'; ?></span></div>
        </div>
        <div class="weight-box">
            <h3>Bu gök cisminde ağırlığın</h3>
            <p class="muted">Dünya'daki kilonu girerek yaklaşık ağırlığını hesaplayabilirsin.</p>
            <form method="POST">
                <input type="hidden" name="form_type" value="weight">
                <div class="form-group">
                    <label>Dünya'daki kilon</label>
                    <input name="earth_weight" type="number" step="0.1" min="0" placeholder="Örn: 60" value="<?php echo e($earthWeight); ?>">
                </div>
                <button type="submit">Hesapla</button>
            </form>
            <?php if ($calculatedWeight !== null): ?>
                <p class="muted">Bu gök cisminde yaklaşık <strong><?php echo e(number_format($calculatedWeight, 2, ',', '.')); ?> kg</strong> ağırlığında hissedersin.</p>
            <?php endif; ?>
        </div>
        <?php if (isLoggedIn()): ?>
            <form method="POST">
                <input type="hidden" name="form_type" value="favorite">
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
