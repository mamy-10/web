<?php
require_once '../config/config.php';
include '../includes/header.php';

function translateText($text)
{
    $url =
        "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=tr&dt=t&q="
        . urlencode($text);

    $response = @file_get_contents($url);

    if (!$response) {
        return $text;
    }

    $result = json_decode($response, true);

    if (!isset($result[0])) {
        return $text;
    }

    $translated = '';

    foreach ($result[0] as $part) {

        if (isset($part[0])) {
            $translated .= $part[0];
        }

    }

    return $translated;
}

$url = "https://api.nasa.gov/planetary/apod?api_key=" . NASA_API_KEY;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

curl_close($ch);

$data = json_decode($response, true);
?>

<h1>Günün Astronomi Görseli</h1>

<p class="muted">
    Astronomy Picture of the Day servisinden alınan günlük uzay görseli.
</p>

<?php if ($data && !isset($data['error'])): ?>

    <div class="card">

        <h2>
            <?php echo htmlspecialchars(translateText($data['title'])); ?>
        </h2>

        <p class="muted">
            <?php echo htmlspecialchars($data['date']); ?>
        </p>

        <?php if (($data['media_type'] ?? '') === 'image'): ?>

            <img
                class="apod-img"
                src="<?php echo htmlspecialchars($data['url']); ?>"
                alt="<?php echo htmlspecialchars($data['title']); ?>"
            >

        <?php else: ?>

            <a
                class="btn-small"
                href="<?php echo htmlspecialchars($data['url']); ?>"
                target="_blank"
            >
                İçeriği Aç
            </a>

        <?php endif; ?>

        <p style="margin-top:20px; line-height:1.9;">

            <?php
            echo nl2br(
                htmlspecialchars(
                    translateText($data['explanation'])
                )
            );
            ?>

        </p>

    </div>

<?php else: ?>

    <div class="alert error">
        NASA verisi alınamadı.
    </div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>
