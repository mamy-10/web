<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verifyCsrfToken();
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    if ($deleteId) {
        $stmt = $pdo->prepare("DELETE FROM missions WHERE id = ?");
        $stmt->execute([$deleteId]);
    }
    redirectTo('admin/missions-manage.php');
}

$missions = $pdo->query("SELECT * FROM missions ORDER BY launch_year")->fetchAll(PDO::FETCH_ASSOC);
include '../../includes/header.php';
?>
<h1>Görev Yönetimi</h1>
<a class="btn" href="mission-form.php">Yeni Görev Ekle</a>
<div class="table-card">
<table>
    <tr><th>Yıl</th><th>Başlık</th><th>Kurum</th><th>İşlem</th></tr>
    <?php foreach ($missions as $mission): ?>
    <tr>
        <td><?php echo e($mission['launch_year']); ?></td>
        <td><?php echo e($mission['title']); ?></td>
        <td><?php echo e($mission['agency']); ?></td>
        <td class="actions-inline">
            <a class="btn-small" href="mission-form.php?id=<?php echo e($mission['id']); ?>">Düzenle</a>
            <form method="POST" onsubmit="return confirm('Bu görev silinsin mi?')">
                <?php echo csrfField(); ?>
                <input type="hidden" name="delete_id" value="<?php echo e($mission['id']); ?>">
                <button class="btn-small danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include '../../includes/footer.php'; ?>
