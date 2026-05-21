<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = '';
$event = ['title'=>'', 'event_date'=>'', 'category'=>'', 'description'=>''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM space_events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$event) { die('Gök olayı bulunamadı.'); }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrfToken();
    $event = [
        'title' => trim($_POST['title'] ?? ''),
        'event_date' => $_POST['event_date'] ?? '',
        'category' => trim($_POST['category'] ?? ''),
        'description' => trim($_POST['description'] ?? '')
    ];
    $dateOk = DateTime::createFromFormat('Y-m-d', $event['event_date']) !== false;

    if (mb_strlen($event['title']) < 3) {
        $error = 'Başlık en az 3 karakter olmalı.';
    } elseif (!$dateOk) {
        $error = 'Geçerli bir tarih seçmelisin.';
    } elseif (mb_strlen($event['category']) < 3) {
        $error = 'Kategori en az 3 karakter olmalı.';
    } elseif (mb_strlen($event['description']) < 20) {
        $error = 'Açıklama en az 20 karakter olmalı.';
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE space_events SET title=?, event_date=?, category=?, description=? WHERE id=?");
            $stmt->execute([$event['title'], $event['event_date'], $event['category'], $event['description'], $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO space_events (title, event_date, category, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$event['title'], $event['event_date'], $event['category'], $event['description']]);
        }
        redirectTo('admin/events-manage.php');
    }
}

include '../../includes/header.php';
?>
<div class="form-card">
    <h1>Gök Olayı Formu</h1>
    <?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST" novalidate>
        <?php echo csrfField(); ?>
        <div class="form-group"><label>Başlık</label><input name="title" value="<?php echo e($event['title']); ?>" required></div>
        <div class="form-group"><label>Tarih</label><input name="event_date" type="date" value="<?php echo e($event['event_date']); ?>" required></div>
        <div class="form-group"><label>Kategori</label><input name="category" value="<?php echo e($event['category']); ?>" required></div>
        <div class="form-group"><label>Açıklama</label><textarea name="description" rows="6" required><?php echo e($event['description']); ?></textarea></div>
        <button>Kaydet</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>
