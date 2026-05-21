<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verifyCsrfToken();
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    if ($deleteId) {
        $stmt = $pdo->prepare("DELETE FROM galaxies WHERE id = ?");
        $stmt->execute([$deleteId]);
    }
    redirectTo('admin/galaxy-manage.php');
}

$galaxies = $pdo->query("SELECT * FROM galaxies ORDER BY distance_light_years ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
include '../../includes/header.php';
?>
<h1>Galaksi Yönetimi</h1>
<a class="btn" href="celestial-form.php?type=galaxy">Yeni Galaksi Ekle</a>
<div class="table-card">
<table>
    <tr><th>Ad</th><th>Tür</th><th>Takımyıldız</th><th>Uzaklık</th><th>İşlem</th></tr>
    <?php foreach ($galaxies as $galaxy): ?>
    <tr>
        <td><?php echo e($galaxy['name']); ?></td>
        <td><?php echo e($galaxy['galaxy_type']); ?></td>
        <td><?php echo e($galaxy['constellation'] ?? '-'); ?></td>
        <td><?php echo $galaxy['distance_light_years'] !== null ? e(number_format((float)$galaxy['distance_light_years'], 0, ',', '.')) . ' ışık yılı' : '-'; ?></td>
        <td class="actions-inline">
            <a class="btn-small" href="celestial-form.php?type=galaxy&id=<?php echo e($galaxy['id']); ?>">Düzenle</a>
            <form method="POST" onsubmit="return confirm('Bu galaksi silinsin mi?')">
                <?php echo csrfField(); ?>
                <input type="hidden" name="delete_id" value="<?php echo e($galaxy['id']); ?>">
                <button class="btn-small danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include '../../includes/footer.php'; ?>
