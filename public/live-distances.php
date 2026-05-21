<?php
require_once '../config/db.php';
include '../includes/header.php';
$items = $pdo->query("SELECT name, type, live_distance_note, distance_from_sun_million_km FROM planets ORDER BY FIELD(type,'gezegen','cüce gezegen','uydu'), name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Ortalama Uzaklık Bilgileri</h1>
<div class="alert info">Gezegenlerin Dünya’ya uzaklığı yörüngedeki konumlarına göre değişir.
Bu sayfada gök cisimleri için yaklaşık ve ortalama uzaklık bilgilerini inceleyebilirsiniz.</div>
<div class="table-card">
<table>
    <thead><tr><th>Gök Cismi</th><th>Tür</th><th>Güneş’e Ortalama Uzaklık</th><th>Dünya’ya Yaklaşık Uzaklık Notu</th></tr></thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo e($item['name']); ?></td>
            <td><?php echo e($item['type']); ?></td>
            <td><?php echo $item['distance_from_sun_million_km'] !== null ? e($item['distance_from_sun_million_km']).' milyon km' : '-'; ?></td>
            <td><?php echo e($item['live_distance_note'] ?: 'Veri yok'); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php include '../includes/footer.php'; ?>
