<?php
require_once '../../config/db.php';
require_once '../../includes/auth.php';
requireEditor();

$allowedContentTypes = ['planet', 'exoplanet', 'galaxy'];
$contentType = $_POST['content_type'] ?? ($_GET['type'] ?? 'planet');
if (!in_array($contentType, $allowedContentTypes, true)) {
    $contentType = 'planet';
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = '';

$planet = [
    'name'=>'', 'type'=>'gezegen', 'description'=>'', 'image_url'=>'', 'gravity_multiplier'=>'1',
    'radius_km'=>'', 'distance_from_sun_million_km'=>'', 'moons'=>'', 'average_temperature_c'=>'', 'live_distance_note'=>''
];
$exoplanet = [
    'name'=>'', 'system_name'=>'', 'distance_light_years'=>'', 'discovery_year'=>'',
    'discovery_method'=>'', 'planet_type'=>'', 'habitability_note'=>'', 'description'=>'', 'image_url'=>''
];
$galaxy = [
    'name'=>'', 'galaxy_type'=>'', 'distance_light_years'=>'', 'constellation'=>'', 'description'=>'', 'image_url'=>''
];

if ($id) {
    if ($contentType === 'planet') {
        $stmt = $pdo->prepare("SELECT * FROM planets WHERE id = ?");
        $stmt->execute([$id]);
        $planet = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$planet) { die('Gök cismi bulunamadı.'); }
    } elseif ($contentType === 'exoplanet') {
        $stmt = $pdo->prepare("SELECT * FROM exoplanets WHERE id = ?");
        $stmt->execute([$id]);
        $exoplanet = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$exoplanet) { die('Ötegezegen bulunamadı.'); }
    } else {
        $stmt = $pdo->prepare("SELECT * FROM galaxies WHERE id = ?");
        $stmt->execute([$id]);
        $galaxy = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$galaxy) { die('Galaksi bulunamadı.'); }
    }
}

function nullableValue($value) {
    return $value !== '' ? $value : null;
}

function redirectAfterSave($contentType) {
    if ($contentType === 'exoplanet') {
        redirectTo('admin/exoplanets-manage.php');
    }
    if ($contentType === 'galaxy') {
        redirectTo('admin/galaxy-manage.php');
    }
    redirectTo('admin/planets-manage.php');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrfToken();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($contentType === 'planet') {
        $planet = [
            'name' => trim($_POST['planet_name'] ?? ''),
            'type' => $_POST['planet_type'] ?? 'gezegen',
            'description' => trim($_POST['planet_description'] ?? ''),
            'image_url' => trim($_POST['planet_image_url'] ?? ''),
            'gravity_multiplier' => $_POST['gravity_multiplier'] ?? '',
            'radius_km' => nullableValue($_POST['radius_km'] ?? ''),
            'distance_from_sun_million_km' => nullableValue($_POST['distance_from_sun_million_km'] ?? ''),
            'moons' => nullableValue($_POST['moons'] ?? ''),
            'average_temperature_c' => nullableValue($_POST['average_temperature_c'] ?? ''),
            'live_distance_note' => trim($_POST['live_distance_note'] ?? ''),
        ];
        $allowedTypes = ['gezegen', 'cüce gezegen', 'uydu'];

        if (mb_strlen($planet['name']) < 2) {
            $error = 'Gök cismi adı en az 2 karakter olmalı.';
        } elseif (!in_array($planet['type'], $allowedTypes, true)) {
            $error = 'Geçerli bir gök cismi türü seçmelisin.';
        } elseif (mb_strlen($planet['description']) < 20) {
            $error = 'Açıklama en az 20 karakter olmalı.';
        } elseif (!filter_var($planet['image_url'], FILTER_VALIDATE_URL)) {
            $error = 'Görsel URL geçerli bir bağlantı olmalı.';
        } elseif (!is_numeric($planet['gravity_multiplier']) || $planet['gravity_multiplier'] < 0 || $planet['gravity_multiplier'] > 30) {
            $error = 'Yerçekimi katsayısı 0 ile 30 arasında sayısal bir değer olmalı.';
        } elseif ($planet['radius_km'] !== null && (!is_numeric($planet['radius_km']) || $planet['radius_km'] < 0)) {
            $error = 'Yarıçap negatif olamaz.';
        } elseif ($planet['distance_from_sun_million_km'] !== null && (!is_numeric($planet['distance_from_sun_million_km']) || $planet['distance_from_sun_million_km'] < 0)) {
            $error = 'Güneş uzaklığı negatif olamaz.';
        } elseif ($planet['moons'] !== null && (!filter_var($planet['moons'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) && $planet['moons'] !== '0')) {
            $error = 'Uydu sayısı 0 veya daha büyük tam sayı olmalı.';
        } else {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE planets SET name=?, type=?, description=?, image_url=?, gravity_multiplier=?, radius_km=?, distance_from_sun_million_km=?, moons=?, average_temperature_c=?, live_distance_note=? WHERE id=?");
                $stmt->execute([$planet['name'], $planet['type'], $planet['description'], $planet['image_url'], $planet['gravity_multiplier'], $planet['radius_km'], $planet['distance_from_sun_million_km'], $planet['moons'], $planet['average_temperature_c'], $planet['live_distance_note'], $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO planets (name, type, description, image_url, gravity_multiplier, radius_km, distance_from_sun_million_km, moons, average_temperature_c, live_distance_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$planet['name'], $planet['type'], $planet['description'], $planet['image_url'], $planet['gravity_multiplier'], $planet['radius_km'], $planet['distance_from_sun_million_km'], $planet['moons'], $planet['average_temperature_c'], $planet['live_distance_note']]);
            }
            redirectAfterSave($contentType);
        }
    } elseif ($contentType === 'exoplanet') {
        $exoplanet = [
            'name' => trim($_POST['exoplanet_name'] ?? ''),
            'system_name' => trim($_POST['system_name'] ?? ''),
            'distance_light_years' => nullableValue($_POST['exoplanet_distance_light_years'] ?? ''),
            'discovery_year' => nullableValue($_POST['discovery_year'] ?? ''),
            'discovery_method' => trim($_POST['discovery_method'] ?? ''),
            'planet_type' => trim($_POST['exoplanet_planet_type'] ?? ''),
            'habitability_note' => trim($_POST['habitability_note'] ?? ''),
            'description' => trim($_POST['exoplanet_description'] ?? ''),
            'image_url' => trim($_POST['exoplanet_image_url'] ?? ''),
        ];

        if (mb_strlen($exoplanet['name']) < 2) {
            $error = 'Ötegezegen adı en az 2 karakter olmalı.';
        } elseif (mb_strlen($exoplanet['system_name']) < 2) {
            $error = 'Sistem adı en az 2 karakter olmalı.';
        } elseif (mb_strlen($exoplanet['description']) < 20) {
            $error = 'Açıklama en az 20 karakter olmalı.';
        } elseif (!filter_var($exoplanet['image_url'], FILTER_VALIDATE_URL)) {
            $error = 'Görsel URL geçerli bir bağlantı olmalı.';
        } elseif ($exoplanet['distance_light_years'] !== null && (!is_numeric($exoplanet['distance_light_years']) || $exoplanet['distance_light_years'] < 0)) {
            $error = 'Uzaklık negatif olamaz.';
        } elseif ($exoplanet['discovery_year'] !== null && (!filter_var($exoplanet['discovery_year'], FILTER_VALIDATE_INT) || $exoplanet['discovery_year'] < 0)) {
            $error = 'Keşif yılı geçerli bir yıl olmalı.';
        } else {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE exoplanets SET name=?, system_name=?, distance_light_years=?, discovery_year=?, discovery_method=?, planet_type=?, habitability_note=?, description=?, image_url=? WHERE id=?");
                $stmt->execute([$exoplanet['name'], $exoplanet['system_name'], $exoplanet['distance_light_years'], $exoplanet['discovery_year'], $exoplanet['discovery_method'], $exoplanet['planet_type'], $exoplanet['habitability_note'], $exoplanet['description'], $exoplanet['image_url'], $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO exoplanets (name, system_name, distance_light_years, discovery_year, discovery_method, planet_type, habitability_note, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$exoplanet['name'], $exoplanet['system_name'], $exoplanet['distance_light_years'], $exoplanet['discovery_year'], $exoplanet['discovery_method'], $exoplanet['planet_type'], $exoplanet['habitability_note'], $exoplanet['description'], $exoplanet['image_url']]);
            }
            redirectAfterSave($contentType);
        }
    } else {
        $galaxy = [
            'name' => trim($_POST['galaxy_name'] ?? ''),
            'galaxy_type' => trim($_POST['galaxy_type'] ?? ''),
            'distance_light_years' => nullableValue($_POST['galaxy_distance_light_years'] ?? ''),
            'constellation' => trim($_POST['constellation'] ?? ''),
            'description' => trim($_POST['galaxy_description'] ?? ''),
            'image_url' => trim($_POST['galaxy_image_url'] ?? ''),
        ];

        if (mb_strlen($galaxy['name']) < 2) {
            $error = 'Galaksi adı en az 2 karakter olmalı.';
        } elseif (mb_strlen($galaxy['galaxy_type']) < 2) {
            $error = 'Galaksi türü en az 2 karakter olmalı.';
        } elseif (mb_strlen($galaxy['description']) < 20) {
            $error = 'Açıklama en az 20 karakter olmalı.';
        } elseif (!filter_var($galaxy['image_url'], FILTER_VALIDATE_URL)) {
            $error = 'Görsel URL geçerli bir bağlantı olmalı.';
        } elseif ($galaxy['distance_light_years'] !== null && (!is_numeric($galaxy['distance_light_years']) || $galaxy['distance_light_years'] < 0)) {
            $error = 'Uzaklık negatif olamaz.';
        } else {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE galaxies SET name=?, galaxy_type=?, distance_light_years=?, constellation=?, description=?, image_url=? WHERE id=?");
                $stmt->execute([$galaxy['name'], $galaxy['galaxy_type'], $galaxy['distance_light_years'], $galaxy['constellation'], $galaxy['description'], $galaxy['image_url'], $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO galaxies (name, galaxy_type, distance_light_years, constellation, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$galaxy['name'], $galaxy['galaxy_type'], $galaxy['distance_light_years'], $galaxy['constellation'], $galaxy['description'], $galaxy['image_url']]);
            }
            redirectAfterSave($contentType);
        }
    }
}

$titleMap = [
    'planet' => $id ? 'Gök Cismi Düzenle' : 'Gök Cismi Ekle',
    'exoplanet' => $id ? 'Ötegezegen Düzenle' : 'Ötegezegen Ekle',
    'galaxy' => $id ? 'Galaksi Düzenle' : 'Galaksi Ekle',
];

include '../../includes/header.php';
?>
<div class="form-card wide">
    <h1><?php echo e($titleMap[$contentType]); ?></h1>
    <?php if ($error): ?><div class="alert error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="POST" novalidate>
        <?php echo csrfField(); ?>
        <input type="hidden" name="id" value="<?php echo e($id ?? ''); ?>">
        <div class="form-group">
            <label>İçerik Türü</label>
            <select name="content_type" id="contentType" <?php if ($id) echo 'disabled'; ?>>
                <option value="planet" <?php if($contentType==='planet') echo 'selected'; ?>>Gök Cismi</option>
                <option value="exoplanet" <?php if($contentType==='exoplanet') echo 'selected'; ?>>Ötegezegen</option>
                <option value="galaxy" <?php if($contentType==='galaxy') echo 'selected'; ?>>Galaksi</option>
            </select>
            <?php if ($id): ?><input type="hidden" name="content_type" value="<?php echo e($contentType); ?>"><?php endif; ?>
        </div>

        <div class="content-fields" data-content-fields="planet">
            <div class="form-group"><label>Ad</label><input name="planet_name" value="<?php echo e($planet['name']); ?>"></div>
            <div class="form-group"><label>Tür</label><select name="planet_type">
                <option value="gezegen" <?php if($planet['type']==='gezegen') echo 'selected'; ?>>Gezegen</option>
                <option value="cüce gezegen" <?php if($planet['type']==='cüce gezegen') echo 'selected'; ?>>Cüce Gezegen</option>
                <option value="uydu" <?php if($planet['type']==='uydu') echo 'selected'; ?>>Uydu</option>
            </select></div>
            <div class="form-group"><label>Açıklama</label><textarea name="planet_description" rows="6"><?php echo e($planet['description']); ?></textarea></div>
            <div class="form-group"><label>Görsel URL</label><input name="planet_image_url" type="url" value="<?php echo e($planet['image_url']); ?>"></div>
            <div class="form-group"><label>Yerçekimi Katsayısı</label><input name="gravity_multiplier" type="number" step="0.01" min="0" max="30" value="<?php echo e($planet['gravity_multiplier']); ?>"></div>
            <div class="grid two form-grid">
                <div class="form-group"><label>Yarıçap (km)</label><input name="radius_km" type="number" step="0.1" min="0" value="<?php echo e($planet['radius_km'] ?? ''); ?>"></div>
                <div class="form-group"><label>Güneş'e Uzaklık (milyon km)</label><input name="distance_from_sun_million_km" type="number" step="0.1" min="0" value="<?php echo e($planet['distance_from_sun_million_km'] ?? ''); ?>"></div>
                <div class="form-group"><label>Uydu Sayısı</label><input name="moons" type="number" step="1" min="0" value="<?php echo e($planet['moons'] ?? ''); ?>"></div>
                <div class="form-group"><label>Ortalama Sıcaklık (°C)</label><input name="average_temperature_c" type="number" step="1" value="<?php echo e($planet['average_temperature_c'] ?? ''); ?>"></div>
            </div>
            <div class="form-group"><label>Dünya'ya Uzaklık Notu</label><textarea name="live_distance_note" rows="3"><?php echo e($planet['live_distance_note'] ?? ''); ?></textarea></div>
        </div>

        <div class="content-fields" data-content-fields="exoplanet">
            <div class="grid two form-grid">
                <div class="form-group"><label>Ad</label><input name="exoplanet_name" value="<?php echo e($exoplanet['name']); ?>"></div>
                <div class="form-group"><label>Sistem Adı</label><input name="system_name" value="<?php echo e($exoplanet['system_name']); ?>"></div>
                <div class="form-group"><label>Uzaklık (ışık yılı)</label><input name="exoplanet_distance_light_years" type="number" step="0.01" min="0" value="<?php echo e($exoplanet['distance_light_years'] ?? ''); ?>"></div>
                <div class="form-group"><label>Keşif Yılı</label><input name="discovery_year" type="number" step="1" min="0" value="<?php echo e($exoplanet['discovery_year'] ?? ''); ?>"></div>
                <div class="form-group"><label>Keşif Yöntemi</label><input name="discovery_method" value="<?php echo e($exoplanet['discovery_method'] ?? ''); ?>"></div>
                <div class="form-group"><label>Gezegen Türü</label><input name="exoplanet_planet_type" value="<?php echo e($exoplanet['planet_type'] ?? ''); ?>"></div>
            </div>
            <div class="form-group"><label>Yaşanabilirlik Notu</label><textarea name="habitability_note" rows="3"><?php echo e($exoplanet['habitability_note'] ?? ''); ?></textarea></div>
            <div class="form-group"><label>Açıklama</label><textarea name="exoplanet_description" rows="6"><?php echo e($exoplanet['description']); ?></textarea></div>
            <div class="form-group"><label>Görsel URL</label><input name="exoplanet_image_url" type="url" value="<?php echo e($exoplanet['image_url']); ?>"></div>
        </div>

        <div class="content-fields" data-content-fields="galaxy">
            <div class="grid two form-grid">
                <div class="form-group"><label>Ad</label><input name="galaxy_name" value="<?php echo e($galaxy['name']); ?>"></div>
                <div class="form-group"><label>Galaksi Türü</label><input name="galaxy_type" value="<?php echo e($galaxy['galaxy_type']); ?>"></div>
                <div class="form-group"><label>Uzaklık (ışık yılı)</label><input name="galaxy_distance_light_years" type="number" step="0.1" min="0" value="<?php echo e($galaxy['distance_light_years'] ?? ''); ?>"></div>
                <div class="form-group"><label>Takımyıldız</label><input name="constellation" value="<?php echo e($galaxy['constellation'] ?? ''); ?>"></div>
            </div>
            <div class="form-group"><label>Açıklama</label><textarea name="galaxy_description" rows="6"><?php echo e($galaxy['description']); ?></textarea></div>
            <div class="form-group"><label>Görsel URL</label><input name="galaxy_image_url" type="url" value="<?php echo e($galaxy['image_url']); ?>"></div>
        </div>

        <button>Kaydet</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('contentType');
    const groups = document.querySelectorAll('[data-content-fields]');

    function syncFields() {
        groups.forEach(function (group) {
            group.hidden = group.dataset.contentFields !== select.value;
        });
    }

    select.addEventListener('change', syncFields);
    syncFields();
});
</script>
<?php include '../../includes/footer.php'; ?>
