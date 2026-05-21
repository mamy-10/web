<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
    $action = $_POST['action'] ?? '';
    $note = trim($_POST['admin_note'] ?? '');
    if (!$id || !in_array($action, ['approved','rejected'], true)) {
        $error = 'Geçersiz işlem.';
    } else {
        $stmt = $pdo->prepare("UPDATE content_suggestions SET status=?, admin_note=?, reviewed_at=NOW() WHERE id=?");
        $stmt->execute([$action, $note, $id]);
        $message = $action === 'approved' ? 'Öneri onaylandı.' : 'Öneri reddedildi.';
    }
}
$status = $_GET['status'] ?? 'pending';
$allowed = ['pending','approved','rejected','all'];
if (!in_array($status, $allowed, true)) $status = 'pending';
$sql = "SELECT s.*, u.email FROM content_suggestions s LEFT JOIN users u ON s.user_id=u.id";
$params = [];
if ($status !== 'all') { $sql .= " WHERE s.status=?"; $params[] = $status; }
$sql .= " ORDER BY s.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
include '../../includes/header.php';
?>
<h1>İçerik Önerileri</h1>
<?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
<p><a class="btn-small" href="?status=pending">Bekleyen</a> <a class="btn-small outline" href="?status=approved">Onaylanan</a> <a class="btn-small outline" href="?status=rejected">Reddedilen</a> <a class="btn-small outline" href="?status=all">Tümü</a></p>
<div class="grid two">
<?php foreach ($items as $item): ?>
    <div class="card">
        <p class="muted"><?php echo e($item['category']); ?> · <?php echo e($item['status']); ?> · <?php echo e($item['created_at']); ?></p>
        <h2><?php echo e($item['title']); ?></h2>
        <p><?php echo nl2br(e($item['description'])); ?></p>
        <p class="muted">Gönderen: <?php echo e($item['suggested_by_name'] ?: 'Bilinmiyor'); ?> <?php if($item['email']) echo '· '.e($item['email']); ?></p>
        <?php if ($item['source_url']): ?><p><a href="<?php echo e($item['source_url']); ?>" target="_blank" rel="noopener">Kaynağı aç</a></p><?php endif; ?>
        <?php if ($item['admin_note']): ?><p><strong>Editör notu:</strong> <?php echo e($item['admin_note']); ?></p><?php endif; ?>
        <?php if ($item['status'] === 'pending'): ?>
            <form method="POST" class="inline-actions">
                <?php echo csrfField(); ?>
                <input type="hidden" name="id" value="<?php echo e($item['id']); ?>">
                <input name="admin_note" placeholder="İsteğe bağlı editör notu">
                <button name="action" value="approved">Onayla</button>
                <button name="action" value="rejected" class="danger">Reddet</button>
            </form>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
<?php if (!$items): ?><div class="alert info">Bu filtrede öneri bulunamadı.</div><?php endif; ?>
<?php include '../../includes/footer.php'; ?>
