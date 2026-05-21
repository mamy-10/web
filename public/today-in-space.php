<?php
require_once '../includes/header.php';

date_default_timezone_set('Europe/Istanbul');

$month = date('m');
$day = date('d');
$monthSlug = strtolower(date('M'));
$daySlug = (string)((int)$day);

function fetchUrl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AstroGuide/1.0');
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($response === false || $statusCode >= 400) {
        return false;
    }

    return $response;
}

function translateToTurkish($text) {
    $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=tr&dt=t&q=' . urlencode($text);
    $response = @file_get_contents($url);

    if ($response === false) {
        return $text;
    }

    $data = json_decode($response, true);
    return $data[0][0][0] ?? $text;
}

function cleanText($text) {
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

function fetchAstroHistoryEvents($monthSlug, $daySlug) {
    $url = "https://thisdayinastrohistory.com/$monthSlug-$daySlug/";
    $html = fetchUrl($url);

    if ($html === false) {
        return [];
    }

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);

    $events = [];
    $nodes = $xpath->query('//p|//li|//h2|//h3|//h4');

    foreach ($nodes as $node) {
        $text = cleanText($node->textContent);

        if (!preg_match('/^(\d{3,4})\s*[–-]\s*(.+)$/u', $text, $matches)) {
            continue;
        }

        $events[] = [
            'year' => $matches[1],
            'title' => 'Uzay tarihi kaydı',
            'text' => translateToTurkish($matches[2]),
        ];
    }

    return $events;
}

$spaceEvents = fetchAstroHistoryEvents($monthSlug, $daySlug);

$seen = [];
$spaceEvents = array_values(array_filter($spaceEvents, function ($event) use (&$seen) {
    $key = $event['year'] . '|' . mb_strtolower($event['text']);

    if (isset($seen[$key])) {
        return false;
    }

    $seen[$key] = true;
    return true;
}));

usort($spaceEvents, function ($firstEvent, $secondEvent) {
    return (int)$firstEvent['year'] <=> (int)$secondEvent['year'];
});
?>

<section class="hero compact-hero">
    <div class="hero-card">
        <span class="badge">Bugün Uzay Tarihinde</span>
        <h1><?php echo date('d.m.Y'); ?></h1>
        <p>Bu tarihte yaşanmış uzay, astronomi ve keşif olayları.</p>
    </div>
    <div class="hero-card">
        <h2>Kaynak</h2>
        <p class="muted">Bu bölümde This Day in Astro History kaynağındaki günlük astronomi tarihi kayıtları kullanılmıştır.</p>
    </div>
</section>

<div class="grid">
    <?php if (!empty($spaceEvents)): ?>
        <?php foreach ($spaceEvents as $event): ?>
            <div class="card">
                <div class="badge mini"><?php echo e($event['year']); ?></div>
                <h3><?php echo e($event['title']); ?></h3>
                <p><?php echo e($event['text']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <h3>Bugün için kayıtlı uzay olayı bulunamadı.</h3>
            <p class="muted">Bu tarih için kullanılan kaynaklarda uygun bir uzay veya astronomi kaydı yakalanamadı.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
