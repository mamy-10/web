<?php
require_once '../config/db.php';

$planets = $pdo->query("SELECT id, name, gravity_multiplier FROM planets ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$earthWeight = trim($_POST['earth_weight'] ?? '');
$selectedPlanetId = (int)($_POST['planet_id'] ?? ($planets[0]['id'] ?? 0));
$error = '';
$result = null;
$selectedPlanet = null;

foreach ($planets as $planet) {
    if ((int)$planet['id'] === $selectedPlanetId) {
        $selectedPlanet = $planet;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$selectedPlanet) {
        $error = 'Lütfen geçerli bir gök cismi seç.';
    } elseif (!is_numeric($earthWeight) || (float)$earthWeight <= 0) {
        $error = 'Lütfen geçerli bir kilo değeri gir.';
    } else {
        $result = (float)$earthWeight * (float)$selectedPlanet['gravity_multiplier'];
    }
}

include '../includes/header.php';
?>
<div class="form-card">
    <h1>Gezegenlerde Ağırlık Hesaplama</h1>
    <p class="muted">
        Dünya'daki kilonu gir ve farklı gezegenlerde yaklaşık kaç kilogram hissedileceğini gör.
    </p>
    <?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Dünya'daki kilon</label>
            <input name="earth_weight" type="number" step="0.1" min="0" placeholder="Örn: 60" value="<?php echo e($earthWeight); ?>">
        </div>
        <div class="form-group">
            <label>Gök cismi seç</label>
            <select name="planet_id">
                <?php foreach ($planets as $planet): ?>
                    <option value="<?php echo e($planet['id']); ?>" <?php echo (int)$planet['id'] === $selectedPlanetId ? 'selected' : ''; ?>>
                        <?php echo e($planet['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Hesapla</button>
    </form>
    <?php if ($result !== null && $selectedPlanet): ?>
        <p class="muted">Bu gök cisminde yaklaşık <strong><?php echo e(number_format($result, 2, ',', '.')); ?> kg</strong> ağırlığında hissedersin.</p>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
