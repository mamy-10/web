<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$error = "";
$success = "";
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrfToken();
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $role = $_POST['role'] ?? 'traveler';
    if (!in_array($role, ['traveler', 'contributor'], true)) { $role = 'traveler'; }

    if (mb_strlen($name) < 3) {
        $error = "Ad soyad en az 3 karakter olmalı.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Geçerli bir e-posta adresi girmelisin.";
    } elseif (strlen($password) < 6) {
        $error = "Şifre en az 6 karakter olmalı.";
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "Bu e-posta zaten kayıtlı.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hash, $role]);
            $success = "Kayıt başarılı. Şimdi giriş yapabilirsin.";
            $name = $email = "";
        }
    }
}
include '../includes/header.php';
?>
<div class="form-card">
    <h1>Kayıt Ol</h1>
    <?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?php echo e($success); ?></div><?php endif; ?>
    <form method="POST" novalidate>
        <?php echo csrfField(); ?>
        <div class="form-group">
            <label>Ad Soyad</label>
            <input name="name" value="<?php echo e($name); ?>" required minlength="3">
        </div>
        <div class="form-group">
            <label>E-posta</label>
            <input name="email" type="email" value="<?php echo e($email); ?>" required>
        </div>
        <div class="form-group">
            <label>Şifre</label>
            <input name="password" type="password" required minlength="6">
            <small class="muted">En az 6 karakter kullan.</small>
        </div>
        <div class="form-group">
            <label>Hesap Türü</label>
            <select name="role">
                <option value="traveler">Standart Gezgin</option>
                <option value="contributor">Destekçi / İçerik Önerici</option>
            </select>
            <small class="muted">Destekçi hesaplar içerik önerilerini editöre göndermek için kullanılabilir.</small>
        </div>
        <button>Kayıt Ol</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
