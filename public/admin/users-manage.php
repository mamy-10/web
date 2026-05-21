<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

$q = trim($_GET['q'] ?? '');
$role = $_GET['role'] ?? '';
$allowedRoles = ['traveler', 'contributor', 'editor'];

$sql = "SELECT id, name, email, role, created_at FROM users WHERE 1";
$params = [];

if ($q !== '') {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

if (in_array($role, $allowedRoles, true)) {
    $sql .= " AND role = ?";
    $params[] = $role;
} else {
    $role = '';
}

$sql .= " ORDER BY created_at DESC, id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$roleLabels = [
    'traveler' => 'Gezgin',
    'contributor' => 'Destekçi',
    'editor' => 'Editör',
];

include '../../includes/header.php';
?>
<h1>Kullanıcı Yönetimi</h1>
<form class="search-bar" method="GET">
    <input name="q" placeholder="Ad veya e-posta ara..." value="<?php echo e($q); ?>">
    <select name="role">
        <option value="">Tüm roller</option>
        <option value="traveler" <?php if($role==='traveler') echo 'selected'; ?>>Gezgin</option>
        <option value="contributor" <?php if($role==='contributor') echo 'selected'; ?>>Destekçi</option>
        <option value="editor" <?php if($role==='editor') echo 'selected'; ?>>Editör</option>
    </select>
    <button>Ara / Filtrele</button>
</form>

<div class="table-card">
<table>
    <tr><th>ID</th><th>Ad</th><th>E-posta</th><th>Rol</th><th>Kayıt Tarihi</th></tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?php echo e($user['id']); ?></td>
        <td><?php echo e($user['name']); ?></td>
        <td><?php echo e($user['email']); ?></td>
        <td><?php echo e($roleLabels[$user['role']] ?? $user['role']); ?></td>
        <td><?php echo e($user['created_at']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<?php if (!$users): ?><div class="alert info">Aramana uygun kullanıcı bulunamadı.</div><?php endif; ?>
<?php include '../../includes/footer.php'; ?>
