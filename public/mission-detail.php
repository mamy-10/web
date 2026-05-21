<?php
require_once '../config/db.php';
include '../includes/header.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

$stmt = $pdo->prepare("SELECT * FROM missions WHERE id = ?");
$stmt->execute([$id]);

$mission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mission) {
    http_response_code(404);
    die("Görev bulunamadı.");
}

$image =
    !empty($mission['image_url'])
    ? $mission['image_url']
    : 'https://images.unsplash.com/photo-1446776877081-d282a0f896e2?q=80&w=1200&auto=format&fit=crop';
?>

<div class="grid two">

    <div>

        <img
            class="apod-img"
            src="<?php echo e($image); ?>"
            alt="<?php echo e($mission['title']); ?> görseli"
        >

    </div>

    <div class="card">

        <h1>
            <?php echo e($mission['title']); ?>
        </h1>

        <p class="muted">
            <?php echo e($mission['agency']); ?>
        </p>

        <p>
            <?php echo nl2br(e($mission['description'])); ?>
        </p>

        <div class="info-grid">

            <div>
                <strong>Fırlatma Yılı</strong>
                <span>
                    <?php echo e($mission['launch_year']); ?>
                </span>
            </div>

            <div>
                <strong>Kurum</strong>
                <span>
                    <?php echo e($mission['agency']); ?>
                </span>
            </div>

        </div>

        <div class="actions mt-3">

            <a
                class="btn-small outline"
                href="missions.php"
            >
                ← Görevlere Dön
            </a>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>
