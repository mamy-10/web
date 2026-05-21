<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verifyCsrfToken();
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    if ($deleteId) {
        $stmt = $pdo->prepare("DELETE FROM exoplanets WHERE id = ?");
        $stmt->execute([$deleteId]);
    }
    redirectTo('admin/exoplanets-manage.php');
}

$exoplanets = $pdo->query("SELECT * FROM exoplanets ORDER BY distance_light_years ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
include '../../includes/header.php';
?>
<h1>Ötegezegen Yönetimi</h1>
<a class="btn" href="celestial-form.php?type=exoplanet">Yeni Ötegezegen Ekle</a>
<div class="table-card">
<table>
    <tr><th>Ad</th><th>Sistem</th><th>Tür</th><th>Uzaklık</th><th>Keşif</th><th>İşlem</th></tr>
    <?php foreach ($exoplanets as $exoplanet): ?>
    <tr>
        <td><?php echo e($exoplanet['name']); ?></td>
        <td><?php echo e($exoplanet['system_name']); ?></td>
        <td><?php echo e($exoplanet['planet_type'] ?? '-'); ?></td>
        <td><?php echo $exoplanet['distance_light_years'] !== null ? e($exoplanet['distance_light_years']) . ' ışık yılı' : '-'; ?></td>
        <td><?php echo e($exoplanet['discovery_year'] ?? '-'); ?></td>
        <td class="actions-inline">
            <a class="btn-small" href="celestial-form.php?type=exoplanet&id=<?php echo e($exoplanet['id']); ?>">Düzenle</a>
            <form method="POST" onsubmit="return confirm('Bu ötegezegen silinsin mi?')">
                <?php echo csrfField(); ?>
                <input type="hidden" name="delete_id" value="<?php echo e($exoplanet['id']); ?>">
                <button class="btn-small danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include '../../includes/footer.php'; ?>
