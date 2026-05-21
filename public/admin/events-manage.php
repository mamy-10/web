<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verifyCsrfToken();
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    if ($deleteId) {
        $stmt = $pdo->prepare("DELETE FROM space_events WHERE id = ?");
        $stmt->execute([$deleteId]);
    }
    redirectTo('admin/events-manage.php');
}

$events = $pdo->query("SELECT * FROM space_events ORDER BY event_date")->fetchAll(PDO::FETCH_ASSOC);
include '../../includes/header.php';
?>
<h1>Gök Olayı Yönetimi</h1>
<a class="btn" href="event-form.php">Yeni Gök Olayı Ekle</a>
<div class="table-card">
<table>
    <tr><th>Tarih</th><th>Başlık</th><th>Kategori</th><th>İşlem</th></tr>
    <?php foreach ($events as $event): ?>
    <tr>
        <td><?php echo e($event['event_date']); ?></td>
        <td><?php echo e($event['title']); ?></td>
        <td><?php echo e($event['category']); ?></td>
        <td class="actions-inline">
            <a class="btn-small" href="event-form.php?id=<?php echo e($event['id']); ?>">Düzenle</a>
            <form method="POST" onsubmit="return confirm('Bu gök olayı silinsin mi?')">
                <?php echo csrfField(); ?>
                <input type="hidden" name="delete_id" value="<?php echo e($event['id']); ?>">
                <button class="btn-small danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include '../../includes/footer.php'; ?>
