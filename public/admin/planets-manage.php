<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verifyCsrfToken();
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    if ($deleteId) {
        $stmt = $pdo->prepare("DELETE FROM planets WHERE id = ?");
        $stmt->execute([$deleteId]);
    }
    redirectTo('admin/planets-manage.php');
}

$planets = $pdo->query("SELECT * FROM planets ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
include '../../includes/header.php';
?>
<h1>Gezegen Yönetimi</h1>
<a class="btn" href="celestial-form.php?type=planet">Yeni Gök Cismi Ekle</a>
<div class="table-card">
<table>
    <tr><th>Ad</th><th>Tür</th><th>Yerçekimi</th><th>Uydu</th><th>İşlem</th></tr>
    <?php foreach ($planets as $planet): ?>
    <tr>
        <td><?php echo e($planet['name']); ?></td>
        <td><?php echo e($planet['type']); ?></td>
        <td><?php echo e($planet['gravity_multiplier']); ?> g</td>
        <td><?php echo e($planet['moons'] ?? '-'); ?></td>
        <td class="actions-inline">
            <a class="btn-small" href="celestial-form.php?type=planet&id=<?php echo e($planet['id']); ?>">Düzenle</a>
            <form method="POST" onsubmit="return confirm('Bu gezegen silinsin mi?')">
                <?php echo csrfField(); ?>
                <input type="hidden" name="delete_id" value="<?php echo e($planet['id']); ?>">
                <button class="btn-small danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include '../../includes/footer.php'; ?>
