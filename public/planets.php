<?php
require_once '../config/db.php';
include '../includes/header.php';

$q = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? '';
$allowedTypes = ['gezegen', 'cüce gezegen', 'uydu'];

$sql = "SELECT * FROM planets WHERE 1";
$params = [];

if ($q !== '') {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

if (in_array($type, $allowedTypes, true)) {
    $sql .= " AND type = ?";
    $params[] = $type;
} else {
    $type = '';
}

$sql .= " ORDER BY name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$planets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Gezegenler ve Gök Cisimleri</h1>
<p class="muted">Gezegenleri ara, filtrele ve detaylarını incele.</p>

<form class="search-bar" method="GET">
    <input name="q" placeholder="Mars, Jüpiter, Dünya..." value="<?php echo e($q); ?>">
    <select name="type">
        <option value="">Tüm türler</option>
        <option value="gezegen" <?php if($type==='gezegen') echo 'selected'; ?>>Gezegen</option>
        <option value="cüce gezegen" <?php if($type==='cüce gezegen') echo 'selected'; ?>>Cüce Gezegen</option>
        <option value="uydu" <?php if($type==='uydu') echo 'selected'; ?>>Uydu</option>
    </select>
    <button>Ara / Filtrele</button>
</form>

<div class="grid">
<?php foreach ($planets as $planet): ?>
    <div class="card">
        <img class="card-img" src="<?php echo e($planet['image_url']); ?>" alt="<?php echo e($planet['name']); ?> görseli">
        <h3><?php echo e($planet['name']); ?></h3>
        <p><?php echo e(mb_substr($planet['description'], 0, 120)); ?>...</p>
        <p class="muted">Tür: <?php echo e($planet['type']); ?> · Yerçekimi: <?php echo e($planet['gravity_multiplier']); ?> g</p>
        <a class="btn-small" href="planet-detail.php?id=<?php echo e($planet['id']); ?>">Detay</a>
    </div>
<?php endforeach; ?>
</div>
<?php if (!$planets): ?><div class="alert error">Aramana uygun kayıt bulunamadı.</div><?php endif; ?>
<?php include '../includes/footer.php'; ?>
