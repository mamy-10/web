<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = '';
$mission = ['title'=>'', 'agency'=>'', 'launch_year'=>'', 'description'=>'', 'image_url'=>''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM missions WHERE id = ?");
    $stmt->execute([$id]);
    $mission = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$mission) { die('Görev bulunamadı.'); }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrfToken();
    $mission = [
        'title' => trim($_POST['title'] ?? ''),
        'agency' => trim($_POST['agency'] ?? ''),
        'launch_year' => $_POST['launch_year'] ?? '',
        'description' => trim($_POST['description'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? '') !== '' ? trim($_POST['image_url'] ?? '') : null
    ];
    $currentYear = (int)date('Y') + 20;

    if (mb_strlen($mission['title']) < 3) {
        $error = 'Başlık en az 3 karakter olmalı.';
    } elseif (mb_strlen($mission['agency']) < 2) {
        $error = 'Kurum adı en az 2 karakter olmalı.';
    } elseif (!filter_var($mission['launch_year'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1900, 'max_range' => $currentYear]])) {
        $error = 'Fırlatma yılı 1900 ile ' . $currentYear . ' arasında olmalı.';
    } elseif (mb_strlen($mission['description']) < 20) {
        $error = 'Açıklama en az 20 karakter olmalı.';
    } elseif ($mission['image_url'] !== null && !filter_var($mission['image_url'], FILTER_VALIDATE_URL)) {
        $error = 'Görsel URL geçerli bir bağlantı olmalı.';
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE missions SET title=?, agency=?, launch_year=?, description=?, image_url=? WHERE id=?");
            $stmt->execute([$mission['title'], $mission['agency'], $mission['launch_year'], $mission['description'], $mission['image_url'], $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO missions (title, agency, launch_year, description, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$mission['title'], $mission['agency'], $mission['launch_year'], $mission['description'], $mission['image_url']]);
        }
        redirectTo('admin/missions-manage.php');
    }
}

include '../../includes/header.php';
?>
<div class="form-card">
    <h1>Uzay Görevi Formu</h1>
    <?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST" novalidate>
        <?php echo csrfField(); ?>
        <div class="form-group"><label>Başlık</label><input name="title" value="<?php echo e($mission['title']); ?>" required></div>
        <div class="form-group"><label>Kurum</label><input name="agency" value="<?php echo e($mission['agency']); ?>" required></div>
        <div class="form-group"><label>Fırlatma Yılı</label><input name="launch_year" type="number" min="1900" max="<?php echo (int)date('Y') + 20; ?>" value="<?php echo e($mission['launch_year']); ?>" required></div>
        <div class="form-group"><label>Açıklama</label><textarea name="description" rows="6" required><?php echo e($mission['description']); ?></textarea></div>
        <div class="form-group"><label>Görsel URL</label><input name="image_url" type="url" value="<?php echo e($mission['image_url'] ?? ''); ?>" placeholder="https://..."></div>
        <button>Kaydet</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>
