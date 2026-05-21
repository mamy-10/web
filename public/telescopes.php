<?php
require_once '../config/db.php';
include '../includes/header.php';
$q = trim($_GET['q'] ?? '');
$platform = trim($_GET['platform'] ?? '');
$sql = "SELECT * FROM telescopes WHERE 1";
$params = [];
if ($q !== '') {
    $sql .= " AND (name LIKE ? OR agency LIKE ? OR wavelength LIKE ? OR description LIKE ? OR discoveries LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($platform !== '') {
    $sql .= " AND platform = ?";
    $params[] = $platform;
}
$sql .= " ORDER BY platform ASC, launch_or_first_light_year ASC, name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$telescopes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Bilimsel Teleskoplar</h1>
<p class="muted">Yer tabanlı ve uzay tabanlı teleskopları; dalga boyu, kurum, görev ve keşif katkılarıyla karşılaştır.</p>
<form class="search-bar" method="GET">
    <input name="q" placeholder="Hubble, Webb, ALMA, kızılötesi..." value="<?php echo e($q); ?>">
    <select name="platform">
        <option value="">Tüm platformlar</option>
        <option value="Yer tabanlı" <?php echo $platform === 'Yer tabanlı' ? 'selected' : ''; ?>>Yer tabanlı</option>
        <option value="Uzay tabanlı" <?php echo $platform === 'Uzay tabanlı' ? 'selected' : ''; ?>>Uzay tabanlı</option>
    </select>
    <button>Ara</button>
</form>
<div class="grid two">
<?php foreach ($telescopes as $telescope): ?>
    <article class="card">
        <span class="badge mini"><?php echo e($telescope['platform']); ?></span>
        <h3><?php echo e($telescope['name']); ?></h3>
        <p class="muted"><?php echo e($telescope['agency']); ?> · <?php echo e($telescope['location_or_orbit']); ?></p>
        <p><?php echo e($telescope['description']); ?></p>
        <div class="info-grid compact">
            <div><strong>Dalga boyu</strong><span><?php echo e($telescope['wavelength']); ?></span></div>
            <div><strong>İlk ışık / fırlatma</strong><span><?php echo e($telescope['launch_or_first_light_year'] ?? '-'); ?></span></div>
            <div><strong>Odak</strong><span><?php echo e($telescope['main_goal']); ?></span></div>
            <div><strong>Katkı</strong><span><?php echo e($telescope['discoveries']); ?></span></div>
        </div>
    </article>
<?php endforeach; ?>
</div>
<?php if (!$telescopes): ?><div class="alert error">Aramana uygun teleskop bulunamadı.</div><?php endif; ?>
<?php include '../includes/footer.php'; ?>
