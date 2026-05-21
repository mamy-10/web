<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrfToken();
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = "Geçerli e-posta ve şifre girmelisin.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = $user["role"];
            redirectTo('index.php');
        } else {
            $error = "E-posta veya şifre hatalı.";
        }
    }
}
include '../includes/header.php';
?>
<div class="form-card">
    <h1>Giriş Yap</h1>
    <?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST" novalidate>
        <?php echo csrfField(); ?>
        <div class="form-group">
            <label>E-posta</label>
            <input name="email" type="email" value="<?php echo e($email); ?>" required>
        </div>
        <div class="form-group">
            <label>Şifre</label>
            <input name="password" type="password" required>
        </div>
        <button>Giriş Yap</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
