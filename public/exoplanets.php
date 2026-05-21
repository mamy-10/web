<?php
require_once '../config/db.php';
include '../includes/header.php';
$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM exoplanets WHERE 1";
$params = [];
if ($q !== '') {
    $sql .= " AND (name LIKE ? OR system_name LIKE ? OR description LIKE ? OR habitability_note LIKE ?)";
    $params = ["%$q%", "%$q%", "%$q%", "%$q%"];
}
$sql .= " ORDER BY distance_light_years ASC, name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Ötegezegenler</h1>
<p class="muted">Güneş Sistemi dışındaki gezegenleri, keşif yöntemlerini ve yaşanabilirlik notlarını incele.</p>
<form class="search-bar" method="GET">
    <input name="q" placeholder="TRAPPIST, Kepler, yaşanabilir..." value="<?php echo e($q); ?>">
    <button>Ara</button>
</form>
<div class="grid">
<?php foreach ($items as $item): ?>
    <div class="card">
        <img class="card-img" src="<?php echo e($item['image_url']); ?>" alt="<?php echo e($item['name']); ?> görseli">
        <h3><?php echo e($item['name']); ?></h3>
        <p class="muted"><?php echo e($item['system_name']); ?> · <?php echo e($item['planet_type']); ?></p>
        <p><?php echo e(mb_substr($item['description'], 0, 150)); ?>...</p>
        <div class="info-grid compact">
            <div><strong>Uzaklık</strong><span><?php echo $item['distance_light_years'] !== null ? e($item['distance_light_years']).' ışık yılı' : '-'; ?></span></div>
            <div><strong>Keşif</strong><span><?php echo e($item['discovery_year'] ?? '-'); ?></span></div>
            <div><strong>Yöntem</strong><span><?php echo e($item['discovery_method'] ?? '-'); ?></span></div>
        </div>
        <p><strong>Yaşanabilirlik:</strong> <?php echo e($item['habitability_note'] ?? 'Veri yok'); ?></p>
        <a class="btn-small" href="exoplanet-detail.php?id=<?php echo e($item['id']); ?>">Detay</a>
    </div>
<?php endforeach; ?>
</div>
<?php if (!$items): ?><div class="alert error">Aramana uygun ötegezegen bulunamadı.</div><?php endif; ?>
<?php include '../includes/footer.php'; ?>
