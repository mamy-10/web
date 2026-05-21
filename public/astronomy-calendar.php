<?php
include '../includes/header.php';
date_default_timezone_set('Europe/Istanbul');
$filter = $_GET['type'] ?? '';
$events = [
    ['date'=>'2026-01-02', 'end'=>'2026-01-03', 'type'=>'Meteor Yağmuru', 'title'=>'Quadrantids Meteor Yağmuru', 'visibility'=>'Kuzey Yarımküre için iyi', 'description'=>'Yılın ilk güçlü meteor yağmurlarından biridir. En iyi gözlem gece yarısından sonra, karanlık ve açık gökyüzünde yapılır.', 'source'=>'NASA Watch the Skies / Royal Observatory'],
    ['date'=>'2026-01-10', 'end'=>'', 'type'=>'Gezegen', 'title'=>'Jüpiter karşı konumda', 'visibility'=>'Parlak gezegen gözlemi', 'description'=>'Karşı konum dönemlerinde gezegenler genellikle daha parlak ve gözlem için daha uygundur.', 'source'=>'NASA Watch the Skies'],
    ['date'=>'2026-02-17', 'end'=>'', 'type'=>'Tutulma', 'title'=>'Halkalı Güneş Tutulması', 'visibility'=>'Antarktika çevresinden görülebilir', 'description'=>'Türkiye’den izlenebilir bir tutulma değildir; ancak astronomi takviminde önemli bir olaydır.', 'source'=>'NASA Watch the Skies'],
    ['date'=>'2026-03-20', 'end'=>'', 'type'=>'Mevsim', 'title'=>'Mart Ekinoksu', 'visibility'=>'Küresel olay', 'description'=>'Güneş’in gök ekvatorunu geçtiği andır. Kuzey Yarımküre’de astronomik ilkbaharın başlangıcı kabul edilir.', 'source'=>'NASA Night Sky Network'],
    ['date'=>'2026-04-21', 'end'=>'2026-04-22', 'type'=>'Meteor Yağmuru', 'title'=>'Lyrids Meteor Yağmuru', 'visibility'=>'Her iki yarımküreden izlenebilir', 'description'=>'Nisan ayının klasik meteor yağmurudur. Işık kirliliğinden uzak bölgelerde daha iyi görünür.', 'source'=>'NASA Watch the Skies / Royal Observatory'],
    ['date'=>'2026-05-05', 'end'=>'2026-05-06', 'type'=>'Meteor Yağmuru', 'title'=>'Eta Aquariids Meteor Yağmuru', 'visibility'=>'Güney Yarımküre daha avantajlı', 'description'=>'Halley Kuyruklu Yıldızı’nın bıraktığı parçacıklarla ilişkilidir. Şafak öncesi saatler daha uygundur.', 'source'=>'NASA Watch the Skies / Space.com'],
    ['date'=>'2026-06-21', 'end'=>'', 'type'=>'Mevsim', 'title'=>'Haziran Gündönümü', 'visibility'=>'Küresel olay', 'description'=>'Kuzey Yarımküre’de astronomik yazın başlangıcıdır; yılın en uzun gündüzlerinden biri yaşanır.', 'source'=>'NASA Watch the Skies / NWS'],
    ['date'=>'2026-07-30', 'end'=>'2026-07-31', 'type'=>'Meteor Yağmuru', 'title'=>'Southern Delta Aquariids ve Alpha Capricornids', 'visibility'=>'Güney gökyüzü daha avantajlı', 'description'=>'Yaz sonuna yaklaşırken etkin olan meteor yağmurlarıdır; Alpha Capricornids yavaş ve parlak ateş toplarıyla bilinir.', 'source'=>'NASA Watch the Skies / Royal Observatory'],
    ['date'=>'2026-08-12', 'end'=>'', 'type'=>'Tutulma', 'title'=>'Tam Güneş Tutulması', 'visibility'=>'Grönland, İzlanda ve İspanya hattı', 'description'=>'2026’nın en dikkat çekici gök olaylarından biridir. Türkiye’den tam tutulma olarak görülmez.', 'source'=>'NASA Watch the Skies'],
    ['date'=>'2026-08-12', 'end'=>'2026-08-13', 'type'=>'Meteor Yağmuru', 'title'=>'Perseids Meteor Yağmuru', 'visibility'=>'Kuzey Yarımküre için çok iyi', 'description'=>'Yılın en popüler meteor yağmurlarındandır. Çıplak gözle, geniş ve karanlık bir alanda izlenmelidir.', 'source'=>'Timeanddate / Royal Observatory'],
    ['date'=>'2026-09-23', 'end'=>'', 'type'=>'Mevsim', 'title'=>'Eylül Ekinoksu', 'visibility'=>'Küresel olay', 'description'=>'Kuzey Yarımküre’de astronomik sonbaharın başlangıcıdır. Gece ve gündüz süreleri birbirine yaklaşır.', 'source'=>'NASA Night Sky Network'],
    ['date'=>'2026-10-08', 'end'=>'2026-10-09', 'type'=>'Meteor Yağmuru', 'title'=>'Draconids Meteor Yağmuru', 'visibility'=>'Kuzey Yarımküre için iyi', 'description'=>'Akşam saatlerinde de gözlenebilmesiyle diğer birçok meteor yağmurundan ayrılır.', 'source'=>'Timeanddate'],
    ['date'=>'2026-10-21', 'end'=>'2026-10-22', 'type'=>'Meteor Yağmuru', 'title'=>'Orionids Meteor Yağmuru', 'visibility'=>'Her iki yarımküreden izlenebilir', 'description'=>'Halley Kuyruklu Yıldızı ile ilişkili ikinci önemli meteor yağmurudur.', 'source'=>'Timeanddate'],
    ['date'=>'2026-11-17', 'end'=>'2026-11-18', 'type'=>'Meteor Yağmuru', 'title'=>'Leonids Meteor Yağmuru', 'visibility'=>'Her iki yarımküreden izlenebilir', 'description'=>'Tarihsel olarak güçlü meteor fırtınalarıyla bilinir; normal yıllarda daha sakin seyreder.', 'source'=>'Timeanddate'],
    ['date'=>'2026-12-13', 'end'=>'2026-12-14', 'type'=>'Meteor Yağmuru', 'title'=>'Geminids Meteor Yağmuru', 'visibility'=>'Her iki yarımküreden çok iyi', 'description'=>'Genellikle yılın en güçlü meteor yağmurlarından biri kabul edilir; akşamdan itibaren gözlem şansı verir.', 'source'=>'American Meteor Society / Timeanddate'],
    ['date'=>'2026-12-21', 'end'=>'', 'type'=>'Mevsim', 'title'=>'Aralık Gündönümü', 'visibility'=>'Küresel olay', 'description'=>'Kuzey Yarımküre’de astronomik kışın başlangıcıdır; yılın en uzun gecelerinden biri yaşanır.', 'source'=>'NWS / NASA'],
];
$types = array_values(array_unique(array_column($events, 'type')));
if ($filter !== '') { $events = array_values(array_filter($events, fn($e) => $e['type'] === $filter)); }
?>
<section class="hero compact-hero">
    <div class="hero-card">
        <span class="badge">Astronomi Takvimi</span>
        <h1>2026 özel gök olayları</h1>
        <p>Meteor yağmurları, tutulmalar, ekinokslar, gündönümleri ve önemli gezegen olayları tek takvimde.</p>
        <div class="actions"><a class="btn secondary" href="tonight-sky.php">Bu Gece Gökyüzü</a></div>
    </div>
    <div class="hero-card">
        <h2>Filtrele</h2>
        <form method="get" class="search-bar calendar-filter">
            <select name="type"><option value="">Tüm olaylar</option><?php foreach($types as $type): ?><option value="<?php echo e($type); ?>" <?php echo $filter===$type?'selected':''; ?>><?php echo e($type); ?></option><?php endforeach; ?></select>
            <button type="submit">Uygula</button>
        </form>
        <p class="muted">Tarihler gözlem konumuna göre birkaç saat farkla yerel güne kayabilir. Kaynak etiketi her kartta belirtilmiştir.</p>
    </div>
</section>

<section class="timeline">
    <?php foreach($events as $event): ?>
    <article class="timeline-item">
        <div class="timeline-date">
            <strong><?php echo e((new DateTime($event['date']))->format('d M')); ?></strong>
            <?php if($event['end']): ?><span><?php echo e((new DateTime($event['end']))->format('d M')); ?> arası</span><?php else: ?><span>tek gün</span><?php endif; ?>
        </div>
        <div class="card timeline-card">
            <span class="badge mini"><?php echo e($event['type']); ?></span>
            <h3><?php echo e($event['title']); ?></h3>
            <p><strong>Gözlem durumu:</strong> <?php echo e($event['visibility']); ?></p>
            <p><?php echo e($event['description']); ?></p>
            <p class="muted"><strong>Kaynak:</strong> <?php echo e($event['source']); ?></p>
        </div>
    </article>
    <?php endforeach; ?>
</section>

<section class="table-card">
    <h2>Gözlem önerisi</h2>
    <p class="muted">Meteor yağmurları için teleskop gerekmez; en iyi yöntem karanlık bir alanda çıplak gözle geniş gökyüzünü izlemektir. Tutulmalar için ise Güneş’e doğrudan bakılmamalı; yalnızca sertifikalı güneş filtresi kullanılmalıdır.</p>
</section>
<?php include '../includes/footer.php'; ?>
