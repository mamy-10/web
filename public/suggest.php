<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
$message = '';
$error = '';
$title = '';
$description = '';
$source_url = '';
$category = 'gezegen';
$allowed = ['gezegen','uydu','cüce gezegen','ötegezegen','galaksi','görev','gök olayı','diğer'];
$canSuggest = isContributor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$canSuggest) {
        http_response_code(403);
        die('Bu işlem sadece destekçi / içerik önerici hesaplar için kullanılabilir.');
    }

    verifyCsrfToken();
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $source_url = trim($_POST['source_url'] ?? '');
    $category = $_POST['category'] ?? 'diğer';
    if (!in_array($category, $allowed, true)) $category = 'diğer';
    if (mb_strlen($title) < 3) {
        $error = 'Başlık en az 3 karakter olmalı.';
    } elseif (mb_strlen($description) < 20) {
        $error = 'Açıklama en az 20 karakter olmalı. Ne eklenmesini istediğini biraz anlat.';
    } elseif ($source_url !== '' && !filter_var($source_url, FILTER_VALIDATE_URL)) {
        $error = 'Kaynak bağlantısı geçerli bir URL olmalı.';
    } else {
        $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
        $name = isLoggedIn() ? ($_SESSION['name'] ?? 'Kullanıcı') : 'Misafir';
        $stmt = $pdo->prepare("INSERT INTO content_suggestions (user_id, suggested_by_name, category, title, description, source_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $name, $category, $title, $description, $source_url]);
        $message = 'Önerin editör paneline gönderildi. Editör onaylarsa içerik olarak eklenebilir.';
        $title = $description = $source_url = '';
        $category = 'gezegen';
    }
}
include '../includes/header.php';
?>
<div class="form-card wide">
    <h1>İçerik Öner</h1>
    <?php if (!$canSuggest): ?>
        <div class="alert info">
            İçerik önerisi göndermek için destekçi / içerik önerici hesabıyla giriş yapmalısın.
        </div>
        <p class="muted">Standart gezgin hesapları siteyi gezebilir ve favori ekleyebilir; öneri gönderme yetkisi destekçi hesaplara ayrılmıştır.</p>
        <div class="actions">
            <?php if (isLoggedIn()): ?>
                <a class="btn" href="index.php">Ana Sayfaya Dön</a>
            <?php else: ?>
                <a class="btn" href="login.php">Giriş Yap</a>
                <a class="btn secondary" href="register.php">Destekçi Hesap Oluştur</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
    <p class="muted">Gezegen, uydu, ötegezegen, galaksi veya görev öner. Öneriler editör panelinde onay/red sürecine düşer.</p>
    <?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST" novalidate>
        <?php echo csrfField(); ?>
        <div class="form-group"><label>Kategori</label><select name="category"><?php foreach ($allowed as $opt): ?><option value="<?php echo e($opt); ?>" <?php if($category===$opt) echo 'selected'; ?>><?php echo e(ucfirst($opt)); ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Başlık</label><input name="title" value="<?php echo e($title); ?>" placeholder="Örn: Callisto eklensin"></div>
        <div class="form-group"><label>Açıklama</label><textarea name="description" rows="6" placeholder="Neden eklenmeli, hangi bilgiler olmalı?"><?php echo e($description); ?></textarea></div>
        <div class="form-group"><label>Kaynak URL</label><input name="source_url" value="<?php echo e($source_url); ?>" placeholder="https://..."></div>
        <button>Öneriyi Gönder</button>
    </form>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
