<?php
require_once '../config/db.php';
include '../includes/header.php';

$stmt = $pdo->query("SELECT * FROM space_events ORDER BY event_date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Gök Olayları</h1>
<div class="grid">
<?php foreach ($events as $event): ?>
    <div class="card">
        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
        <p class="muted"><?php echo htmlspecialchars($event['event_date']); ?> | <?php echo htmlspecialchars($event['category']); ?></p>
        <p><?php echo htmlspecialchars($event['description']); ?></p>
    </div>
<?php endforeach; ?>
</div>
<?php include '../includes/footer.php'; ?>
