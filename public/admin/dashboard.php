<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

$planetCount = $pdo->query("SELECT COUNT(*) FROM planets")->fetchColumn();
$exoplanetCount = $pdo->query("SELECT COUNT(*) FROM exoplanets")->fetchColumn();
$galaxyCount = $pdo->query("SELECT COUNT(*) FROM galaxies")->fetchColumn();
$missionCount = $pdo->query("SELECT COUNT(*) FROM missions")->fetchColumn();
$eventCount = $pdo->query("SELECT COUNT(*) FROM space_events")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pendingCount = $pdo->query("SELECT COUNT(*) FROM content_suggestions WHERE status='pending'")->fetchColumn();
$lastPlanets = $pdo->query("SELECT name, type, created_at FROM planets ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$pendingSuggestions = $pdo->query("SELECT title, category, suggested_by_name, created_at FROM content_suggestions WHERE status='pending' ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
include '../../includes/header.php';
?>
<h1>Editör Paneli</h1>
<div class="grid stats-grid">
    <div class="card"><h3>Gök Cisimleri</h3><p class="stat-number"><?php echo e($planetCount); ?></p><a class="btn-small" href="planets-manage.php">Yönet</a></div>
    <div class="card"><h3>Ötegezegenler</h3><p class="stat-number"><?php echo e($exoplanetCount); ?></p><a class="btn-small" href="exoplanets-manage.php">Yönet</a></div>
    <div class="card"><h3>Galaksiler</h3><p class="stat-number"><?php echo e($galaxyCount); ?></p><a class="btn-small" href="galaxy-manage.php">Yönet</a></div>
    <div class="card"><h3>Öneriler</h3><p class="stat-number"><?php echo e($pendingCount); ?></p><a class="btn-small" href="suggestions-manage.php">İncele</a></div>
    <div class="card"><h3>Uzay Görevleri</h3><p class="stat-number"><?php echo e($missionCount); ?></p><a class="btn-small" href="missions-manage.php">Yönet</a></div>
    <div class="card"><h3>Kullanıcılar</h3><p class="stat-number"><?php echo e($userCount); ?></p><a class="btn-small" href="users-manage.php">Yönet</a></div>
</div>

<div class="grid two dashboard-lists">
    <div class="card">
        <h2>Bekleyen İçerik Önerileri</h2>
        <?php if (!$pendingSuggestions): ?><p class="muted">Bekleyen öneri yok.</p><?php endif; ?>
        <?php foreach ($pendingSuggestions as $s): ?>
            <div class="list-row"><strong><?php echo e($s['title']); ?></strong><span><?php echo e($s['category']); ?> · <?php echo e($s['suggested_by_name']); ?></span></div>
        <?php endforeach; ?>
        <a class="btn-small" href="suggestions-manage.php">Tüm önerileri aç</a>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
