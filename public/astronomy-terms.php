<?php
require_once '../config/db.php';
include '../includes/header.php';
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$sql = "SELECT * FROM astronomy_terms WHERE 1";
$params = [];
if ($q !== '') {
    $sql .= " AND (term LIKE ? OR short_definition LIKE ? OR detailed_definition LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
}
$sql .= " ORDER BY category ASC, term ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$terms = $stmt->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT DISTINCT category FROM astronomy_terms ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);
?>
<h1>Astronomi Sözlüğü</h1>
<p class="muted">Temel astronomi kavramlarını kısa tanım + detaylı açıklama şeklinde öğren.</p>
<form class="search-bar" method="GET">
    <input name="q" placeholder="Kara delik, tayf, kırmızıya kayma..." value="<?php echo e($q); ?>">
    <select name="category">
        <option value="">Tüm kategoriler</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?php echo e($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>><?php echo e($cat); ?></option>
        <?php endforeach; ?>
    </select>
    <button>Ara</button>
</form>
<div class="grid two">
<?php foreach ($terms as $term): ?>
    <article class="card glossary-card">
        <span class="badge mini"><?php echo e($term['category']); ?></span>
        <h3><?php echo e($term['term']); ?></h3>
        <p><strong>Kısa tanım:</strong> <?php echo e($term['short_definition']); ?></p>
        <p><?php echo e($term['detailed_definition']); ?></p>
        <?php if (!empty($term['example'])): ?>
            <p class="muted"><strong>Örnek:</strong> <?php echo e($term['example']); ?></p>
        <?php endif; ?>
    </article>
<?php endforeach; ?>
</div>
<?php if (!$terms): ?><div class="alert error">Aramana uygun astronomi terimi bulunamadı.</div><?php endif; ?>
<?php include '../includes/footer.php'; ?>
