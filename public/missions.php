<?php
require_once '../config/db.php';
include '../includes/header.php';

$q = trim($_GET['q'] ?? '');
$agency = $_GET['agency'] ?? '';

$sql = "SELECT * FROM missions WHERE 1";
$params = [];

if ($q !== '') {
    $sql .= " AND (title LIKE ? OR agency LIKE ? OR description LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

if ($agency !== '') {
    $sql .= " AND agency = ?";
    $params[] = $agency;
}

$sql .= " ORDER BY launch_year ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$agencies = [
    'NASA',
    'SpaceX',
    'ESA',
    'Roscosmos',
    'Sovyetler Birliği',
    'Apollo Programı',
    'JAXA'
];
?>

<h1>Uzay Görevleri</h1>

<p class="muted">
    İnsanlığın uzay keşif tarihindeki önemli görevlerini inceleyin.
</p>

<form class="search-bar" method="GET">

    <input
        name="q"
        placeholder="Apollo, Voyager, NASA..."
        value="<?php echo e($q); ?>"
    >

    <select name="agency">

        <option value="">Tüm Kurumlar</option>

        <?php foreach ($agencies as $item): ?>

            <option
                value="<?php echo e($item); ?>"
                <?php if ($agency === $item) echo 'selected'; ?>
            >
                <?php echo e($item); ?>
            </option>

        <?php endforeach; ?>

    </select>

    <button>Ara / Filtrele</button>

</form>

<div class="grid">

    <?php foreach ($missions as $mission): ?>

        <?php
    $image =
    !empty($mission['image_url'])
    ? $mission['image_url']
    : 'https://images.unsplash.com/photo-1446776877081-d282a0f896e2?q=80&w=1200&auto=format&fit=crop';
?>

        <div class="card">

            <img
                class="card-img"
                src="<?php echo e($image); ?>"
                alt="<?php echo e($mission['title']); ?> görseli"
            >

            <div class="badge mini">
                🚀 <?php echo e($mission['launch_year']); ?>
            </div>

            <h3>
                <?php echo e($mission['title']); ?>
            </h3>

            <p class="muted">
                <?php echo e($mission['agency']); ?>
            </p>

            <p>
                <?php
                echo mb_strimwidth(
                    e($mission['description']),
                    0,
                    140,
                    '...'
                );
                ?>
            </p>

            <div class="info-grid compact">

                <div>
                    <strong>Yıl</strong>
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

            <div class="actions mt-2">

                <a
                    class="btn-small"
                    href="mission-detail.php?id=<?php echo e($mission['id']); ?>"
                >
                    Detay
                </a>

            </div>

        </div>

    <?php endforeach; ?>

</div>

<?php if (!$missions): ?>

    <div class="alert error">
        Aramana uygun görev bulunamadı.
    </div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>