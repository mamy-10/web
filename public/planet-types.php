<?php
require_once '../config/db.php';
include '../includes/header.php';
$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM planet_types WHERE 1";
$params = [];
if ($q !== '') {
    $sql .= " AND (name LIKE ? OR summary LIKE ? OR description LIKE ? OR examples LIKE ?)";
    $params = ["%$q%", "%$q%", "%$q%", "%$q%"];
}
$sql .= " ORDER BY display_order ASC, name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$types = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Gezegen Türleri</h1>
<p class="muted">Kayalık gezegen, gaz devi, buz devi ve ötegezegen sınıfları gibi temel türleri karşılaştır.</p>
<form class="search-bar" method="GET">
    <input name="q" placeholder="Gaz devi, süper Dünya, sıcak Jüpiter..." value="<?php echo e($q); ?>">
    <button>Ara</button>
</form>
<div class="grid two">
<?php foreach ($types as $type): ?>
    <article class="card">
        <h3><?php echo e($type['name']); ?></h3>
        <p><strong>Özet:</strong> <?php echo e($type['summary']); ?></p>
        <p><?php echo e($type['description']); ?></p>
        <div class="info-grid compact">
            <div><strong>Tipik yapı</strong><span><?php echo e($type['composition']); ?></span></div>
            <div><strong>Örnekler</strong><span><?php echo e($type['examples']); ?></span></div>
        </div>
        <p class="muted"><strong>Neden önemli?</strong> <?php echo e($type['importance']); ?></p>
    </article>
<?php endforeach; ?>
</div>
<?php include '../includes/footer.php'; ?>
