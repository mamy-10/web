CREATE DATABASE IF NOT EXISTS astroguide CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE astroguide;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS content_suggestions;
DROP TABLE IF EXISTS telescopes;
DROP TABLE IF EXISTS planet_types;
DROP TABLE IF EXISTS astronomy_terms;
DROP TABLE IF EXISTS galaxy_favorites;
DROP TABLE IF EXISTS exoplanet_favorites;
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS space_events;
DROP TABLE IF EXISTS missions;
DROP TABLE IF EXISTS galaxies;
DROP TABLE IF EXISTS exoplanets;
DROP TABLE IF EXISTS planets;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;


CREATE TABLE astronomy_terms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    term VARCHAR(120) NOT NULL,
    category VARCHAR(80) NOT NULL,
    short_definition VARCHAR(255) NOT NULL,
    detailed_definition TEXT NOT NULL,
    example VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE planet_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    summary VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    composition VARCHAR(255) NOT NULL,
    examples VARCHAR(255) NOT NULL,
    importance VARCHAR(500) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE telescopes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    platform ENUM('Yer tabanlı', 'Uzay tabanlı') NOT NULL,
    agency VARCHAR(150) NOT NULL,
    location_or_orbit VARCHAR(180) NOT NULL,
    wavelength VARCHAR(150) NOT NULL,
    launch_or_first_light_year INT NULL,
    main_goal VARCHAR(255) NOT NULL,
    discoveries VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('traveler', 'contributor', 'editor') NOT NULL DEFAULT 'traveler',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE planets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    gravity_multiplier DECIMAL(6,3) NOT NULL DEFAULT 1.000,
    radius_km DECIMAL(12,1) NULL,
    distance_from_sun_million_km DECIMAL(12,1) NULL,
    moons INT NULL,
    average_temperature_c INT NULL,
    live_distance_note VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE exoplanets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    system_name VARCHAR(120) NOT NULL,
    distance_light_years DECIMAL(10,2) NULL,
    discovery_year INT NULL,
    discovery_method VARCHAR(120) NULL,
    planet_type VARCHAR(100) NULL,
    habitability_note VARCHAR(255) NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE galaxies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    galaxy_type VARCHAR(100) NOT NULL,
    distance_light_years DECIMAL(14,1) NULL,
    constellation VARCHAR(120) NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE missions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    agency VARCHAR(100) NOT NULL,
    launch_year INT NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE space_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    category VARCHAR(80) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    planet_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorite (user_id, planet_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (planet_id) REFERENCES planets(id) ON DELETE CASCADE
);

CREATE TABLE exoplanet_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exoplanet_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_exoplanet_favorite (user_id, exoplanet_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exoplanet_id) REFERENCES exoplanets(id) ON DELETE CASCADE
);

CREATE TABLE galaxy_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    galaxy_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_galaxy_favorite (user_id, galaxy_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (galaxy_id) REFERENCES galaxies(id) ON DELETE CASCADE
);

CREATE TABLE content_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    suggested_by_name VARCHAR(100) NULL,
    category ENUM('gezegen', 'uydu', 'cüce gezegen', 'ötegezegen', 'galaksi', 'görev', 'gök olayı', 'diğer') NOT NULL DEFAULT 'diğer',
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    source_url VARCHAR(500) NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    admin_note VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);


INSERT INTO astronomy_terms (term, category, short_definition, detailed_definition, example) VALUES
('Astronomi', 'Temel Kavram', 'Gök cisimlerini ve evreni inceleyen bilim dalıdır.', 'Astronomi; yıldızlar, gezegenler, galaksiler, bulutsular, kara delikler ve evrenin büyük ölçekli yapısı gibi konuları gözlem ve fizik yasalarıyla açıklar. Modern astronomi; optik gözlemler, radyo gözlemleri, uzay teleskopları ve bilgisayar analizleriyle çalışır.', 'Bir teleskopla Jüpiter uydularını gözlemek astronomik bir gözlemdir.'),
('Astrofizik', 'Temel Kavram', 'Gök cisimlerinin fiziksel yapısını ve davranışını inceler.', 'Astrofizik, astronominin fizik ağırlıklı koludur. Yıldızların enerji üretimi, galaksilerin hareketi, kara deliklerin çekimi ve evrenin genişlemesi gibi olayları matematik ve fizik modelleriyle açıklar.', 'Bir yıldızın sıcaklığını tayfından hesaplamak astrofizik çalışmasıdır.'),
('Işık yılı', 'Ölçü Birimi', 'Işığın bir yılda aldığı mesafedir.', 'Işık yılı zaman değil, uzaklık birimidir. Evren çok büyük olduğu için kilometre yerine ışık yılı kullanmak daha pratiktir. Işık saniyede yaklaşık 300.000 km yol alır; bu nedenle bir ışık yılı yaklaşık 9,46 trilyon kilometredir.', 'Proxima Centauri yaklaşık 4,24 ışık yılı uzaklıktadır.'),
('Astronomik Birim', 'Ölçü Birimi', 'Dünya ile Güneş arasındaki ortalama uzaklıktır.', 'Astronomik Birim, Güneş Sistemi içindeki mesafeleri anlatmak için kullanılır. Kısaca AU veya AB olarak yazılır. 1 AB yaklaşık 149,6 milyon kilometredir.', 'Jüpiter Güneş’ten yaklaşık 5,2 AB uzaklıktadır.'),
('Yörünge', 'Hareket', 'Bir cismin başka bir cisim etrafında izlediği yoldur.', 'Gezegenler Güneş etrafında, uydular gezegenler etrafında ve yapay uydular Dünya etrafında yörüngede dolanabilir. Yörünge şekli genellikle elipse yakındır.', 'Ay, Dünya etrafında bir yörüngede dolanır.'),
('Kütle çekimi', 'Fizik', 'Kütlesi olan cisimlerin birbirini çekmesidir.', 'Kütle çekimi gezegenleri yörüngede tutar, yıldızların ve galaksilerin oluşumunda belirleyici rol oynar. Bir cismin kütlesi arttıkça çekim etkisi de artar.', 'Dünya’nın çekimi bizi yüzeyde tutar.'),
('Tayf', 'Gözlem Tekniği', 'Işığın dalga boylarına ayrılmış hâlidir.', 'Tayf analizi sayesinde bir yıldızın sıcaklığı, kimyasal bileşimi, hareketi ve manyetik alanı hakkında bilgi edinilebilir. Bu yöntem astronomide çok güçlüdür çünkü uzak cisimlere fiziksel olarak gitmeden onları incelemeyi sağlar.', 'Hidrojen çizgileri bir yıldız tayfında görülebilir.'),
('Kırmızıya kayma', 'Kozmoloji', 'Işığın daha uzun dalga boylarına kaymasıdır.', 'Bir galaksi bizden uzaklaşıyorsa ışığı kırmızıya doğru kayar. Uzak galaksilerin kırmızıya kayması evrenin genişlediğini gösteren en önemli kanıtlardan biridir.', 'Çok uzak galaksiler yüksek kırmızıya kayma değerlerine sahip olabilir.'),
('Bulutsu', 'Gök Cismi', 'Gaz ve tozdan oluşan büyük uzay bulutudur.', 'Bulutsular yıldız oluşum bölgeleri olabilir veya ömrünü tamamlayan yıldızların uzaya saçtığı maddelerden oluşabilir. Renkli teleskop görüntülerinin çoğu bulutsulardan gelir.', 'Orion Bulutsusu bir yıldız oluşum bölgesidir.'),
('Kara delik', 'Gök Cismi', 'Çekimi çok güçlü olan, ışığın bile kaçamadığı bölgedir.', 'Kara delikler çok büyük kütlenin çok küçük bir hacme sıkışmasıyla oluşur. Olay ufku denilen sınırın içinden ışık bile dışarı çıkamaz. Galaksilerin merkezlerinde süper kütleli kara delikler bulunabilir.', 'Samanyolu’nun merkezinde Sagittarius A* adlı kara delik vardır.'),
('Ötegezegen', 'Gök Cismi', 'Güneş Sistemi dışında başka yıldızların çevresinde dolanan gezegendir.', 'Ötegezegenler, gezegen sistemlerinin ne kadar çeşitli olduğunu gösterir. Bazıları yıldızına çok yakın sıcak gaz devleri, bazıları ise Dünya’ya benzer kayalık gezegen adaylarıdır.', 'TRAPPIST-1e bir ötegezegendir.'),
('Yaşanabilir bölge', 'Astrobiyoloji', 'Bir yıldız çevresinde sıvı suya uygun olabilecek uzaklık aralığıdır.', 'Yaşanabilir bölge, bir gezegende yaşam kesin vardır anlamına gelmez. Atmosfer, manyetik alan, yıldız etkinliği ve gezegenin yapısı da çok önemlidir.', 'Dünya Güneş’in yaşanabilir bölgesindedir.'),
('Gelgit kilitlenmesi', 'Hareket', 'Bir cismin hep aynı yüzünü diğer cisme göstermesidir.', 'Gelgit etkileri zamanla bir gök cisminin dönüşünü yavaşlatabilir. Ay’ın Dünya’ya hep aynı yüzünü göstermesi buna örnektir. Bazı ötegezegenlerde de bu durum beklenir.', 'Ay’ın görünmeyen yüzü Dünya’dan doğrudan görülemez.'),
('Kozmoloji', 'Kozmoloji', 'Evrenin kökenini, yapısını ve evrimini inceleyen alandır.', 'Kozmoloji; Büyük Patlama, karanlık madde, karanlık enerji, evrenin genişlemesi ve galaksi kümeleri gibi büyük ölçekli konularla ilgilenir.', 'Evrenin genişleme hızını ölçmek kozmolojinin konusudur.'),
('Manyetosfer', 'Gezegen Bilimi', 'Bir gezegenin manyetik alanının uzayda etkili olduğu bölgedir.', 'Manyetosfer, gezegeni Güneş rüzgârı gibi yüklü parçacıklardan kısmen koruyabilir. Dünya’nın manyetosferi kutup ışıklarının oluşumuyla da ilişkilidir.', 'Jüpiter çok güçlü bir manyetosfere sahiptir.');

INSERT INTO planet_types (name, summary, description, composition, examples, importance, display_order) VALUES
('Kayalık Gezegen', 'Katı yüzeye sahip, metal ve silikat ağırlıklı gezegen türüdür.', 'Kayalık gezegenler genellikle yıldızlarına daha yakın bölgelerde oluşur ve katı yüzeyleri vardır. Dağlar, kraterler, vadiler ve volkanik yapılar gösterebilirler. Yaşam araştırmalarında özellikle önemlidirler çünkü yüzey, atmosfer ve sıvı su olasılığı birlikte incelenebilir.', 'Silikat kayaçlar, metal çekirdek, ince veya orta yoğunlukta atmosfer', 'Merkür, Venüs, Dünya, Mars', 'Yaşam ihtimali ve yüzey jeolojisi açısından en doğrudan incelenebilir gezegen grubudur.', 1),
('Gaz Devi', 'Çoğunlukla hidrojen ve helyumdan oluşan çok büyük gezegenlerdir.', 'Gaz devlerinin belirgin katı yüzeyi yoktur. Kalın atmosferleri, güçlü fırtınaları, halkaları ve çok sayıda uyduları olabilir. Büyük kütleleri, çevrelerindeki küçük cisimlerin yörüngelerini etkiler.', 'Hidrojen, helyum, derin atmosfer katmanları, olası yoğun çekirdek', 'Jüpiter, Satürn', 'Uydu sistemleri ve gezegen oluşum modelleri için kritik bilgiler sağlar.', 2),
('Buz Devi', 'Su, amonyak ve metan gibi uçucu maddelerce zengin dev gezegenlerdir.', 'Buz devleri gaz devlerinden daha fazla ağır element ve uçucu bileşik içerir. Mavi-yeşil renkleri metanla ilişkilidir. Güneş Sistemi’nde Uranüs ve Neptün bu sınıfa girer.', 'Hidrojen, helyum, metan, su, amonyak ve buzlu/akışkan iç katmanlar', 'Uranüs, Neptün', 'Dış gezegenlerin atmosfer yapısını ve gezegen oluşum çeşitliliğini anlamak için önemlidir.', 3),
('Cüce Gezegen', 'Gezegen benzeri fakat yörüngesini temizlememiş gök cismidir.', 'Cüce gezegenler Güneş etrafında dolanır ve yaklaşık küresel şekil alacak kadar kütleye sahiptir; ancak yörüngelerindeki diğer cisimleri baskın şekilde temizleyemezler. Kuiper Kuşağı ve asteroit kuşağında örnekleri vardır.', 'Buz, kaya, organik bileşikler ve küçük metalik bileşenler', 'Plüton, Ceres, Eris, Haumea, Makemake', 'Güneş Sistemi’nin oluşumundan kalan eski malzemeleri anlamamıza yardım eder.', 4),
('Süper Dünya', 'Dünya’dan büyük, fakat gaz devlerinden küçük ötegezegen sınıfıdır.', 'Süper Dünya terimi gezegenin kütlesiyle ilgilidir; mutlaka Dünya gibi yaşanabilir olduğu anlamına gelmez. Bazıları kayalık olabilir, bazıları kalın atmosfere sahip olabilir.', 'Kayalık yapı, metal çekirdek veya kalın atmosfer olasılığı', 'Kepler-452b adayı, Gliese 667 Cc adayı', 'Dünya benzeri gezegenlerin çeşitliliğini anlamak için önemlidir.', 5),
('Sıcak Jüpiter', 'Yıldızına çok yakın dolanan büyük gaz devi ötegezegendir.', 'Sıcak Jüpiterler, yıldızlarının çevresinde çok kısa sürede tur atar ve atmosferleri aşırı ısınır. Bu tür gezegenler, gezegen göçü teorilerinin gelişmesinde önemli rol oynamıştır.', 'Hidrojen-helyum atmosferi, yüksek sıcaklık, şişmiş atmosfer', '51 Pegasi b, HD 189733 b', 'Ötegezegen keşif tarihinin ilk büyük örneklerinden olduğu için çok önemlidir.', 6),
('Okyanus Dünyası', 'Yüzeyinde veya buz kabuğu altında büyük miktarda su bulunabilecek cisimdir.', 'Okyanus dünyaları sadece gezegen olmak zorunda değildir; Europa ve Enceladus gibi uydular da bu sınıfta değerlendirilebilir. Sıvı su, kimyasal enerji ve organik maddeler yaşam araştırması açısından önemlidir.', 'Su buzu, tuzlu okyanus, kayalık çekirdek, olası hidrotermal etkinlik', 'Europa, Enceladus, Ganymede adayı', 'Astrobiyoloji açısından en heyecan verici hedeflerden biridir.', 7);

INSERT INTO telescopes (name, platform, agency, location_or_orbit, wavelength, launch_or_first_light_year, main_goal, discoveries, description) VALUES
('Hubble Uzay Teleskobu', 'Uzay tabanlı', 'NASA / ESA', 'Alçak Dünya yörüngesi', 'Görünür, morötesi, yakın kızılötesi', 1990, 'Derin uzay, galaksiler, yıldız oluşumu ve gezegen atmosferleri', 'Derin alan görüntüleri, evrenin genişleme hızı çalışmaları, galaksi evrimi', 'Hubble, atmosferin bozucu etkilerinden uzakta çalıştığı için çok keskin görüntüler elde eder. Modern astronominin en üretken gözlemevlerinden biridir.'),
('James Webb Uzay Teleskobu', 'Uzay tabanlı', 'NASA / ESA / CSA', 'Güneş-Dünya L2 noktası çevresi', 'Kızılötesi', 2021, 'Erken evren, yıldız doğumu, ötegezegen atmosferleri', 'Uzak galaksiler, yıldız oluşum bölgeleri, ötegezegen atmosfer analizleri', 'James Webb, özellikle kızılötesi dalga boylarında çalışır. Toz bulutlarının arkasını görebilir ve çok uzak galaksilerden gelen kırmızıya kaymış ışığı inceleyebilir.'),
('Chandra X-Işını Gözlemevi', 'Uzay tabanlı', 'NASA', 'Eliptik Dünya yörüngesi', 'X-ışını', 1999, 'Kara delikler, nötron yıldızları, süpernova kalıntıları ve sıcak gaz', 'Yüksek enerjili evren gözlemleri, galaksi kümelerindeki sıcak gaz haritaları', 'Chandra, evrenin en enerjik olaylarını X-ışını bölgesinde inceler. Kara delik çevreleri ve süpernova kalıntıları gibi aşırı ortamları anlamada kullanılır.'),
('Spitzer Uzay Teleskobu', 'Uzay tabanlı', 'NASA', 'Güneş merkezli yörünge', 'Kızılötesi', 2003, 'Soğuk cisimler, tozlu bölgeler ve ötegezegenler', 'TRAPPIST-1 sistemi gözlemleri, toz diskleri, yıldız oluşum bölgeleri', 'Spitzer, kızılötesi gözlemlerle soğuk ve tozlu bölgeleri incelemiştir. Görevi sona ermiş olsa da ötegezegen ve yıldız oluşumu çalışmalarında büyük katkı sağlamıştır.'),
('Gaia', 'Uzay tabanlı', 'ESA', 'Güneş-Dünya L2 noktası çevresi', 'Optik astrometri', 2013, 'Samanyolu yıldızlarının konum ve hareketlerini ölçmek', 'Milyarlarca yıldız için hassas konum, uzaklık ve hareket kataloğu', 'Gaia, Samanyolu’nun üç boyutlu haritasını çıkarmayı hedefleyen son derece hassas bir astrometri görevidir.'),
('Very Large Telescope', 'Yer tabanlı', 'ESO', 'Cerro Paranal, Şili', 'Görünür ve kızılötesi', 1998, 'Galaksiler, yıldızlar, ötegezegenler ve kara delik çevreleri', 'Sgr A* çevresindeki yıldız hareketleri, ötegezegen görüntüleme çalışmaları', 'VLT, birden fazla büyük teleskoptan oluşan güçlü bir yer tabanlı gözlemevidir. Uyarlanabilir optik teknikleriyle atmosfer etkilerini azaltabilir.'),
('ALMA', 'Yer tabanlı', 'ESO / NRAO / NAOJ', 'Atacama Çölü, Şili', 'Milimetre ve milimetre-altı', 2011, 'Soğuk gaz, toz diskleri, yıldız ve gezegen oluşumu', 'Gezegen oluşum disklerinin detaylı görüntüleri, uzak galaksilerde soğuk gaz ölçümleri', 'ALMA, çok sayıda antenin birlikte çalıştığı bir radyo interferometresidir. Soğuk moleküler gazı ve toz disklerini incelemede çok güçlüdür.'),
('Keck Gözlemevi', 'Yer tabanlı', 'Caltech / University of California', 'Mauna Kea, Hawaii', 'Görünür ve kızılötesi', 1993, 'Yüksek çözünürlüklü spektroskopi ve derin uzay gözlemleri', 'Ötegezegenler, galaksi uzaklıkları, Samanyolu merkezi çalışmaları', 'Keck teleskopları, büyük aynaları ve gelişmiş spektrograflarıyla yer tabanlı astronominin önemli araçlarındandır.'),
('Vera C. Rubin Gözlemevi', 'Yer tabanlı', 'NSF / DOE', 'Cerro Pachón, Şili', 'Geniş alan optik tarama', 2025, 'Gökyüzünü düzenli taramak ve değişen/parlayan cisimleri yakalamak', 'Yakın Dünya asteroitleri, süpernovalar, değişken yıldızlar ve karanlık madde çalışmaları', 'Rubin Gözlemevi, geniş alanlı ve düzenli gökyüzü taramalarıyla zaman alanı astronomisinde büyük veri üretecek şekilde tasarlanmıştır.'),
('FAST', 'Yer tabanlı', 'Çin Bilimler Akademisi', 'Guizhou, Çin', 'Radyo', 2016, 'Pulsarlar, nötr hidrojen ve radyo sinyalleri', 'Yeni pulsar keşifleri ve hassas radyo gözlemleri', 'FAST, çok büyük tek çanaklı bir radyo teleskoptur. Zayıf radyo sinyallerini algılama kapasitesiyle pulsar ve galaksi gazı çalışmalarında kullanılır.');

INSERT INTO users (name, email, password, role) VALUES
('Editör Kullanıcı', 'editor@astroguide.com', '$2y$10$y1ltv94Ch4F1rLQhUrGFheVjRJY0c7a2aTS31urAEmQaYE9xMf4TW', 'editor'),
('Gezgin Kullanıcı', 'gezgin@astroguide.com', '$2y$10$y1ltv94Ch4F1rLQhUrGFheVjRJY0c7a2aTS31urAEmQaYE9xMf4TW', 'traveler'),
('Destekçi Kullanıcı', 'destekci@astroguide.com', '$2y$10$y1ltv94Ch4F1rLQhUrGFheVjRJY0c7a2aTS31urAEmQaYE9xMf4TW', 'contributor');

INSERT INTO planets (name, type, description, image_url, gravity_multiplier, radius_km, distance_from_sun_million_km, moons, average_temperature_c, live_distance_note) VALUES
('Merkür', 'gezegen', 'Merkür, Güneş sisteminin Güneş’e en yakın ve en küçük gezegenidir. Atmosferi neredeyse yok denecek kadar ince olduğu için gündüzleri çok sıcak, geceleri ise çok soğuk olabilir. Yüzeyinde Ay’a benzeyen kraterler bulunur. Güneş’e yakınlığı nedeniyle gözlemlemesi zordur; genellikle gün doğumu öncesi veya gün batımı sonrası kısa süre görünür.', 'https://images-assets.nasa.gov/image/PIA15190/PIA15190~orig.jpg', 0.380, 2439.7, 57.9, 0, 167, 'Dünya’ya uzaklığı yörünge konumuna göre yaklaşık 77-222 milyon km arasında değişir.'),
('Venüs', 'gezegen', 'Venüs, Dünya’ya boyut olarak benzediği için bazen Dünya’nın ikizi olarak anılır; ancak yüzey koşulları son derece serttir. Yoğun karbondioksit atmosferi güçlü sera etkisi oluşturur ve yüzey sıcaklığı kurşunu eritebilecek seviyelere çıkar. Bulutlarında sülfürik asit damlacıkları bulunur. Kendi ekseni etrafında çok yavaş ve ters yönde döner.', 'https://images-assets.nasa.gov/image/PIA00271/PIA00271~orig.jpg', 0.910, 6051.8, 108.2, 0, 464, 'Dünya’ya uzaklığı yaklaşık 38-261 milyon km arasında değişir.'),
('Dünya', 'gezegen', 'Dünya, üzerinde yaşam olduğu kesin olarak bilinen tek gezegendir. Sıvı su, koruyucu atmosfer, manyetik alan ve uygun sıcaklık aralığı canlılığın devamı için kritik rol oynar. Ay’ın varlığı gelgitleri ve eksen eğikliğinin kararlılığını etkiler. Dünya aynı zamanda uzay araştırmaları için tüm gözlemlerin başlangıç noktasıdır.', 'https://upload.wikimedia.org/wikipedia/commons/9/97/The_Earth_seen_from_Apollo_17.jpg', 1.000, 6371.0, 149.6, 1, 15, 'Referans gezegen olduğu için Dünya-Dünya uzaklığı 0 km kabul edilir.'),
('Mars', 'gezegen', 'Mars, demir oksit bakımından zengin yüzeyi nedeniyle Kızıl Gezegen olarak bilinir. Geçmişte yüzeyinde sıvı su bulunmuş olabileceğine dair güçlü kanıtlar vardır. İnce atmosferi, kutup buzulları, dev kanyonları ve Olympus Mons gibi dev volkanlarıyla bilimsel açıdan çok önemlidir. Günümüzde robotik keşif araçları Mars’ta eski yaşam izlerini araştırmaktadır.', 'https://images-assets.nasa.gov/image/PIA04591/PIA04591~orig.jpg', 0.380, 3389.5, 227.9, 2, -65, 'Dünya’ya uzaklığı yaklaşık 54,6-401 milyon km arasında değişir.'),
('Jüpiter', 'gezegen', 'Jüpiter, Güneş sisteminin en büyük gezegenidir ve çoğunlukla hidrojen ile helyumdan oluşur. Büyük Kırmızı Leke adlı dev fırtınası yüzlerce yıldır gözlemlenmektedir. Güçlü manyetik alanı ve çok sayıda uydusu vardır. Europa, Ganymede, Io ve Callisto gibi büyük uyduları, astrobiyoloji ve gezegen bilimi açısından büyük önem taşır.', 'https://images-assets.nasa.gov/image/PIA22946/PIA22946~orig.jpg', 2.340, 69911.0, 778.5, 95, -110, 'Dünya’ya uzaklığı yaklaşık 588-968 milyon km arasında değişir.'),
('Satürn', 'gezegen', 'Satürn, geniş ve parlak halka sistemiyle tanınan gaz devidir. Halkaları büyük ölçüde buz ve kaya parçacıklarından oluşur. Yoğunluğu suyun yoğunluğundan bile düşüktür. Titan ve Enceladus gibi uyduları, organik kimya ve yer altı okyanusları açısından bilim insanlarının özellikle ilgisini çeker.', 'https://images-assets.nasa.gov/image/PIA21046/PIA21046~orig.jpg', 1.060, 58232.0, 1434.0, 146, -140, 'Dünya’ya uzaklığı yaklaşık 1,2-1,7 milyar km arasında değişir.'),
('Uranüs', 'gezegen', 'Uranüs, eksen eğikliği çok yüksek olan bir buz devidir; neredeyse yan yatmış şekilde döner. Atmosferinde hidrojen, helyum ve metan bulunur. Metan gazı kırmızı ışığı soğurduğu için gezegen mavi-yeşil görünür. Halkaları vardır fakat Satürn’ünkiler kadar parlak değildir.', 'https://images-assets.nasa.gov/image/PIA18182/PIA18182~orig.jpg', 0.890, 25362.0, 2871.0, 28, -195, 'Dünya’ya uzaklığı yaklaşık 2,6-3,2 milyar km arasında değişir.'),
('Neptün', 'gezegen', 'Neptün, Güneş sisteminin en uzak gezegenidir ve güçlü rüzgârlarıyla bilinir. Koyu mavi görünümünü atmosferindeki metan ve diğer bileşenler etkiler. Triton adlı büyük uydusu ters yönde döner ve geçmişte Kuiper Kuşağı’ndan yakalanmış olabileceği düşünülür.', 'https://images-assets.nasa.gov/image/PIA01492/PIA01492~orig.jpg', 1.140, 24622.0, 4495.1, 16, -200, 'Dünya’ya uzaklığı yaklaşık 4,3-4,7 milyar km arasında değişir.'),
('Ay', 'uydu', 'Ay, Dünya’nın doğal uydusudur. Gelgit olaylarında etkili olur ve insanlı uzay görevlerinin ilk hedeflerinden biri olmuştur. Yüzeyinde atmosfer olmadığı için meteor çarpma izleri uzun süre korunur. Ay çalışmaları, Güneş sisteminin erken dönemini anlamak için önemlidir.', 'https://images-assets.nasa.gov/image/PIA00405/PIA00405~orig.jpg', 0.160, 1737.4, 149.6, 0, -20, 'Dünya’ya ortalama uzaklığı yaklaşık 384.400 km’dir.'),
('Europa', 'uydu', 'Europa, Jüpiter’in buzla kaplı uydularından biridir. Kalın buz kabuğunun altında tuzlu ve sıvı bir okyanus bulunabileceği düşünülür. Bu nedenle Dünya dışı yaşam ihtimali açısından en önemli hedeflerden biri kabul edilir. Yüzeyindeki çatlak yapılar, buz kabuğunun hareketli olabileceğini gösterir.', 'https://images-assets.nasa.gov/image/PIA19048/PIA19048~orig.jpg', 0.134, 1560.8, 778.5, 0, -160, 'Jüpiter çevresinde dolandığı için Dünya’ya uzaklığı Jüpiter’in konumuna göre değişir.'),
('Titan', 'uydu', 'Titan, Satürn’ün en büyük uydusudur ve kalın atmosfere sahip nadir uydulardan biridir. Yüzeyinde metan ve etandan oluşan göller, nehirler ve yağış döngüsü bulunur. Organik moleküller açısından zengin olduğu için yaşamın kimyasal temellerini anlamada önemli bir laboratuvar gibidir.', 'https://images-assets.nasa.gov/image/PIA20016/PIA20016~orig.jpg', 0.138, 2574.7, 1434.0, 0, -179, 'Satürn çevresinde dolanır; Dünya’ya uzaklığı Satürn’ün konumuna göre değişir.'),
('Ganymede', 'uydu', 'Ganymede, Jüpiter’in en büyük uydusu ve Güneş sistemindeki en büyük doğal uydudur. Merkür’den bile büyüktür. Kendi manyetik alanına sahip olduğu bilinen tek uydudur. Buzlu kabuğunun altında okyanus bulunabileceği düşünülmektedir.', 'https://images-assets.nasa.gov/image/PIA01666/PIA01666~orig.jpg', 0.146, 2634.1, 778.5, 0   , -163, 'Jüpiter çevresinde dolandığı için uzaklığı Jüpiter’in Dünya’ya konumuyla birlikte değişir.'),
('Io', 'uydu', 'Io, Güneş sisteminin volkanik açıdan en aktif gök cismidir. Jüpiter’in güçlü kütle çekimi ve diğer uydularla etkileşimi iç kısmında gelgit ısınması oluşturur. Bu nedenle yüzeyinde sürekli değişen volkanik bölgeler ve kükürt bileşikleri görülür.', 'https://images-assets.nasa.gov/image/PIA02308/PIA02308~orig.jpg', 0.183, 1821.6, 778.5, 0, -143, 'Jüpiter sistemi içinde yer aldığı için Dünya’ya uzaklığı değişkendir.'),
('Enceladus', 'uydu', 'Enceladus, Satürn’ün küçük fakat bilimsel açıdan çok değerli bir uydusudur. Güney kutbundan uzaya su buharı ve buz parçacıkları püskürttüğü gözlemlenmiştir. Bu püskürmeler, yüzey altı okyanusuna dair güçlü kanıtlar sunar.', 'https://images-assets.nasa.gov/image/PIA17184/PIA17184~orig.jpg', 0.011, 252.1, 1434.0, 0, -201, 'Satürn çevresinde dolanır; uzaklığı Satürn’ün konumuna bağlıdır.'),
('Triton', 'uydu', 'Triton, Neptün’ün en büyük uydusudur ve gezegenin dönüş yönüne ters yönde dolanır. Bu durum onun sonradan yakalanmış bir Kuiper Kuşağı cismi olabileceğini düşündürür. Yüzeyinde azot buzu ve olası jeolojik etkinlik izleri vardır.', 'https://images-assets.nasa.gov/image/PIA00317/PIA00317~orig.jpg', 0.080, 1353.4, 1434.0, 0, -235, 'Neptün çevresinde dolanır; Dünya’ya uzaklığı Neptün’ün konumuna bağlıdır.'),
('Plüton', 'cüce gezegen', 'Plüton, bir dönem dokuzuncu gezegen olarak kabul edilmiş, daha sonra cüce gezegen sınıfına alınmıştır. New Horizons görevi sayesinde yüzeyinde kalp biçimli Tombaugh Regio bölgesi, buz ovaları ve dağlar keşfedilmiştir. Küçük boyutuna rağmen jeolojik olarak ilginç bir dünyadır.', 'https://images-assets.nasa.gov/image/PIA19952/PIA19952~orig.jpg', 0.063, 1188.3, 5900.0, 5, -229, 'Dünya’ya uzaklığı yaklaşık 4,3-7,5 milyar km arasında değişir.');

INSERT INTO exoplanets (name, system_name, distance_light_years, discovery_year, discovery_method, planet_type, habitability_note, description, image_url) VALUES
('Proxima Centauri b', 'Proxima Centauri', 4.24, 2016, 'Radyal hız', 'Kayalık ötegezegen adayı', 'Yıldızının yaşanabilir bölgesinde bulunur; fakat yıldız patlamaları önemli risk oluşturabilir.', 'Proxima Centauri b, Güneş’e en yakın yıldız sistemi olan Proxima Centauri çevresinde dolanan bir ötegezegendir. Dünya’ya görece yakın olması onu ötegezegen araştırmalarında çok önemli yapar. Yıldızına çok yakın dolandığı için gelgit kilitlenmesi olasılığı vardır.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Artist%E2%80%99s_impression_of_Proxima_Centauri_b_shown_hypothetically_as_an_arid_rocky_super-earth.jpg/1280px-Artist%E2%80%99s_impression_of_Proxima_Centauri_b_shown_hypothetically_as_an_arid_rocky_super-earth.jpg'),
('TRAPPIST-1e', 'TRAPPIST-1', 40.70, 2017, 'Geçiş yöntemi', 'Dünya benzeri kayalık gezegen', 'Yaşanabilir bölge adaylarından biridir.', 'TRAPPIST-1 sistemi, küçük ve soğuk bir kırmızı cüce yıldızın çevresinde dolanan birden fazla kayalık gezegene sahiptir. TRAPPIST-1e, boyut ve yoğunluk açısından Dünya’ya benzerliğiyle öne çıkar.', 'https://assets.science.nasa.gov/dynamicimage/assets/science/astro/exo-explore/2024/03/TRAPPIST-1e.png?w=600&h=600&fit=clip&crop=faces%2Cfocalpoint'),
('Kepler-22b', 'Kepler-22', 635.00, 2011, 'Geçiş yöntemi', 'Süper Dünya / mini Neptün adayı', 'Yaşanabilir bölgede yer aldığı açıklanan ilk önemli Kepler adaylarından biridir.', 'Kepler-22b, yıldızının yaşanabilir bölgesinde bulunan ilk dikkat çekici ötegezegenlerden biridir. Boyutu Dünya’dan büyüktür; yüzey koşulları ve atmosfer yapısı kesin bilinmediği için yaşanabilirliği hakkında temkinli yorum yapılır.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Kepler22b-artwork.jpg/1280px-Kepler22b-artwork.jpg'),
('K2-18b', 'K2-18', 124.00, 2015, 'Geçiş yöntemi', 'Alt Neptün / hycean aday', 'Atmosfer çalışmaları nedeniyle yaşam ihtimali tartışmalarında sık geçer.', 'K2-18b, atmosferinde bazı moleküler izlerin araştırılmasıyla gündeme gelen bir ötegezegendir. Dünya’dan büyük olduğu için doğrudan Dünya ikizi değildir; ancak atmosfer ve okyanus ihtimali araştırmaları açısından önemlidir.', 'https://upload.wikimedia.org/wikipedia/commons/8/8d/Esa-hubble-k2-18a_impression.jpg'),
('51 Pegasi b', '51 Pegasi', 50.45, 1995, 'Radyal hız', 'Sıcak Jüpiter', 'Yaşam için uygun değildir; fakat ötegezegen biliminin başlangıç örneklerindendir.', '51 Pegasi b, Güneş benzeri bir yıldız çevresinde keşfedilen ilk ötegezegenlerden biridir. Yıldızına çok yakın dönen sıcak bir gaz devidir. Bu keşif, gezegen sistemlerinin sandığımızdan çok daha çeşitli olduğunu göstermiştir.', 'https://upload.wikimedia.org/wikipedia/commons/f/f7/Artist_impression_of_the_exoplanet_51_Pegasi_b.jpg'),
('HD 189733 b', 'HD 189733', 64.50, 2005, 'Geçiş yöntemi', 'Sıcak Jüpiter', 'Aşırı sıcak ve fırtınalı atmosferi nedeniyle yaşama uygun değildir.', 'HD 189733 b, atmosferi ayrıntılı incelenen sıcak Jüpiter türü ötegezegenlerden biridir. Çok yüksek sıcaklık, hızlı rüzgârlar ve yıldızına yakın yörüngesiyle dikkat çeker.', 'https://upload.wikimedia.org/wikipedia/commons/8/80/Artist%E2%80%99s_impression_of_the_deep_blue_planet_HD_189733b.jpg');

INSERT INTO galaxies (name, galaxy_type, distance_light_years, constellation, description, image_url) VALUES
('Samanyolu', 'Çubuklu sarmal galaksi', 0, 'Merkez: Yay doğrultusu', 'Samanyolu, Güneş sistemimizin içinde bulunduğu galaksidir. Yüz milyarlarca yıldız, gaz, toz ve karanlık madde içerir. Güneş, galaksinin merkezinden yaklaşık 26 bin ışık yılı uzaklıkta yer alır.', 'https://www.universetoday.com/article_images/milky_way.jpg'),
('Andromeda', 'Sarmal galaksi', 2537000, 'Andromeda', 'Andromeda Galaksisi, Samanyolu’na en yakın büyük sarmal galaksidir. Gelecekte Samanyolu ile kütle çekimsel etkileşime girerek birleşmesi beklenir. Çıplak gözle karanlık gökyüzünde silik bir leke gibi görülebilir.', 'https://upload.wikimedia.org/wikipedia/commons/9/98/Andromeda_Galaxy_%28with_h-alpha%29.jpg'),
('Büyük Macellan Bulutu', 'Düzensiz uydu galaksi', 163000, 'Kılıçbalığı / Masa', 'Büyük Macellan Bulutu, Samanyolu’nun uydu galaksilerinden biridir. Güney yarımküreden gözlemlenebilir ve içinde yoğun yıldız oluşum bölgeleri bulunur.', 'https://upload.wikimedia.org/wikipedia/commons/9/94/Large.mc.arp.750pix.jpg'),
('Küçük Macellan Bulutu', 'Düzensiz uydu galaksi', 200000, 'Tukan', 'Küçük Macellan Bulutu, Samanyolu’nun yakın uydu galaksilerinden biridir. Düşük metal bolluğu ve yıldız oluşumu çalışmaları için önemlidir.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Small_Magellanic_Cloud_%28Digitized_Sky_Survey_2%29.jpg/330px-Small_Magellanic_Cloud_%28Digitized_Sky_Survey_2%29.jpg'),
('Üçgen Galaksisi', 'Sarmal galaksi', 2730000, 'Üçgen', 'M33 olarak da bilinen Üçgen Galaksisi, Yerel Grup içindeki büyük sarmal galaksilerden biridir. Samanyolu ve Andromeda ile birlikte yakın evrenin önemli üyelerindendir.', 'https://upload.wikimedia.org/wikipedia/commons/7/71/M33_lohrmobs.png'),
('Whirlpool Galaksisi', 'Sarmal galaksi', 31000000, 'Av Köpekleri', 'M51 olarak da bilinen Whirlpool Galaksisi, belirgin sarmal kolları ve komşu galaksiyle etkileşimi nedeniyle gökbilim görsellerinin en tanınmış örneklerinden biridir.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/db/Messier51_sRGB.jpg/1280px-Messier51_sRGB.jpg');

INSERT INTO missions (title, agency, launch_year, description, image_url) VALUES
('Sputnik 1', 'Sovyetler Birliği', 1957, 'Sputnik 1, Sovyetler Birliği tarafından 1957 yılında fırlatılan dünyanın ilk yapay uydusudur. Uzay çağının başlangıcı kabul edilen bu görev, Dünya yörüngesine başarıyla yerleşmiş ve gönderdiği radyo sinyalleriyle büyük yankı uyandırmıştır. Sputnik’in başarısı, ABD ile Sovyetler Birliği arasındaki uzay yarışını hızlandırmış ve modern uzay araştırmalarının temelini oluşturmuştur.', 'https://upload.wikimedia.org/wikipedia/commons/b/be/Sputnik_asm.jpg'),
('Apollo 11', 'NASA', 1969, 'Apollo 11, NASA tarafından gerçekleştirilen ve insanlığın Ay yüzeyine ilk kez ayak bastığı tarihi uzay görevidir. Neil Armstrong ve Buzz Aldrin Ay yüzeyine iniş yaparken Michael Collins komuta modülünde Ay yörüngesinde kalmıştır. Görev sırasında bilimsel deneyler gerçekleştirilmiş, Ay yüzeyinden kaya örnekleri toplanmış ve çok sayıda fotoğraf çekilmiştir. Armstrong’un söylediği “Bir insan için küçük, insanlık için büyük bir adım” sözü tarihe geçmiştir.', 'https://cdn.arstechnica.net/wp-content/uploads/2019/06/NASA_ApolloLanderOnMom-scaled.jpg'),
('Voyager 1', 'NASA', 1977, 'Voyager 1, NASA tarafından dış gezegenleri incelemek amacıyla fırlatılan keşif sondasıdır. Görev kapsamında Jüpiter ve Satürn hakkında çok önemli veriler toplamış, gezegenlerin atmosferleri, halkaları ve uyduları detaylı şekilde incelenmiştir. Günümüzde hâlâ çalışmaya devam eden Voyager 1, yıldızlararası uzaya ulaşan ilk insan yapımı araç olarak tarihe geçmiştir. Araç üzerinde Dünya kültürünü temsil eden altın plak da bulunmaktadır.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Voyager_spacecraft.jpg/1280px-Voyager_spacecraft.jpg'),
('Hubble Uzay Teleskobu', 'NASA / ESA', 1990, 'Hubble Uzay Teleskobu, Dünya yörüngesinde görev yapan ve astronomi tarihindeki en önemli gözlemevlerinden biri kabul edilen uzay teleskobudur. Atmosferin dışında bulunduğu için son derece net görüntüler elde edebilmektedir. Hubble sayesinde galaksiler, nebulalar, kara delikler ve uzak yıldız sistemleri hakkında çok önemli keşifler yapılmıştır. Teleskop bugüne kadar milyonlarca gözlem gerçekleştirerek bilim dünyasına büyük katkı sağlamıştır.', 'https://upload.wikimedia.org/wikipedia/commons/3/3f/HST-SM4.jpeg'),
('Cassini-Huygens', 'NASA / ESA / ASI', 1997, 'Cassini-Huygens görevi, Satürn, halkaları ve uydularını incelemek amacıyla geliştirilen büyük bir uzay araştırma projesidir. Cassini uzay aracı yıllarca Satürn yörüngesinde görev yaparken Huygens sondası Titan yüzeyine iniş yapmıştır. Görev sırasında Titan’ın atmosferi, Enceladus’un buz püskürmeleri ve Satürn halkalarının yapısı hakkında önemli bilgiler elde edilmiştir. Cassini görevi gezegen bilimine büyük katkılar sağlamıştır.', 'https://assets.science.nasa.gov/dynamicimage/assets/science/psd/solar/internal_resources/3814/Spacecraft_heating_up_in_Saturns_atmosphere.jpeg?w=1280&h=720&fit=clip&crop=faces%2Cfocalpoint'),
('New Horizons', 'NASA', 2006, 'New Horizons görevi, Plüton ve Kuiper Kuşağı cisimlerini incelemek amacıyla NASA tarafından başlatılmıştır. 2015 yılında Plüton’un yakınından geçen araç, yüzeyin detaylı görüntülerini Dünya’ya göndermiştir. Görev sayesinde Plüton’un buzlu ovaları, dağları ve karmaşık yüzey yapısı keşfedilmiştir. New Horizons daha sonra Kuiper Kuşağı’ndaki diğer cisimleri incelemek üzere görevine devam etmiştir.', 'https://science.nasa.gov/wp-content/uploads/2017/12/new-horizons-4q-poster-v4.png'),
('Perseverance', 'NASA', 2020, 'Perseverance, NASA tarafından Mars yüzeyinde geçmiş yaşam izlerini araştırmak amacıyla gönderilen gelişmiş bir keşif aracıdır. Jezero Krateri bölgesinde görev yapan araç, kaya ve toprak örnekleri toplamaktadır. Perseverance üzerinde gelişmiş kameralar, analiz cihazları ve Ingenuity adlı mini helikopter bulunmaktadır. Görev, gelecekteki insanlı Mars görevleri için de önemli bilgiler sağlamaktadır.', 'https://cdn.sanity.io/images/7p2whiua/production/eabd6f2fcaca421daca146f340493f9c1661f7e9-2048x1536.jpg'),
('James Webb Uzay Teleskobu', 'NASA / ESA / CSA', 2021, 'James Webb Uzay Teleskobu, şimdiye kadar geliştirilen en güçlü uzay teleskoplarından biridir. Kızılötesi gözlem teknolojisi sayesinde evrenin ilk galaksalarını, yıldız oluşum bölgelerini ve ötegezegen atmosferlerini inceleyebilmektedir. NASA, ESA ve CSA ortaklığıyla geliştirilen teleskop, Hubble’ın devamı niteliğinde görülmektedir. Webb teleskobu modern astronomi için devrim niteliğinde veriler sağlamaktadır.', 'https://upload.wikimedia.org/wikipedia/commons/2/2a/JWST_spacecraft_model_3.png'),
('Europa Clipper', 'NASA', 2024, 'Europa Clipper, NASA tarafından Jüpiter’in buzlu uydusu Europa’yı incelemek amacıyla geliştirilen görevdir. Araç, Europa’nın yüzey yapısını, buz kabuğunu ve yüzey altı okyanus ihtimalini araştıracaktır. Bilim insanları Europa’nın yaşam için uygun koşullar barındırabileceğini düşünmektedir. Görev sayesinde uydunun yaşanabilirlik potansiyeli hakkında detaylı bilgiler elde edilmesi hedeflenmektedir.', 'https://astrobiology.nasa.gov/uploads/filer_public_thumbnails/filer_public/a3/58/a358283d-339f-4f72-b91d-05e13af28d46/europa_hero.jpg__1240x510_q85_crop_subject_location-620%2C254_subsampling-2.jpg');

INSERT INTO space_events (title, event_date, category, description) VALUES
('Ay Tutulması', '2026-03-03', 'Tutulma', 'Dünya’nın gölgesinin Ay üzerine düşmesiyle oluşan gök olayıdır.'),
('Perseid Meteor Yağmuru', '2026-08-12', 'Meteor Yağmuru', 'Her yıl gözlemlenebilen popüler meteor yağmurlarından biridir.'),
('Orionid Meteor Yağmuru', '2026-10-21', 'Meteor Yağmuru', 'Halley Kuyruklu Yıldızı kaynaklı parçacıkların oluşturduğu meteor yağmurudur.'),
('Mars Karşı Konumu', '2027-02-19', 'Gezegen Gözlemi', 'Mars’ın Dünya’ya göre daha parlak ve gözleme uygun olduğu dönemlerden biridir.'),
('Jüpiter Gözlem Gecesi', '2026-11-05', 'Gezegen Gözlemi', 'Jüpiter’in parlak görüldüğü dönemlerde teleskopla bantları ve büyük uyduları gözlemlenebilir.'),('Geminid Meteor Yağmuru', '2026-12-14', 'Meteor Yağmuru', 'Yılın en yoğun meteor yağmurlarından biridir. Saatte yüzlerce meteor gözlemlenebilir.'),
('Tam Güneş Tutulması', '2027-08-02', 'Tutulma', 'Ay’ın Güneş’i tamamen örtmesiyle oluşan etkileyici gök olayıdır.'),
('Kısmi Güneş Tutulması', '2026-09-21', 'Tutulma', 'Ay’ın Güneş’in yalnızca bir kısmını kapattığı gök olayıdır.'),
('Satürn Gözlem Gecesi', '2026-07-18', 'Gezegen Gözlemi', 'Satürn’ün halkalarının teleskopla net şekilde gözlemlenebileceği dönemlerden biridir.'),
('Venüs En Parlak Konum', '2026-05-11', 'Gezegen Gözlemi', 'Venüs’ün gökyüzünde en parlak göründüğü dönemlerden biridir.'),
('Leonid Meteor Yağmuru', '2026-11-17', 'Meteor Yağmuru', 'Hızlı ve parlak meteorlarıyla bilinen popüler meteor yağmurlarından biridir.'),
('Merkür Geçişi', '2027-11-07', 'Gezegen Olayı', 'Merkür’ün Dünya’dan bakıldığında Güneş’in önünden geçtiği nadir olaydır.'),
('Süper Ay', '2026-04-27', 'Ay Olayı', 'Ay’ın Dünya’ya en yakın konumda olduğu ve normalden daha büyük göründüğü dönemdir.'),
('Mavi Ay', '2026-08-30', 'Ay Olayı', 'Aynı ay içinde gerçekleşen ikinci dolunay evresine verilen isimdir.'),
('Eta Aquarid Meteor Yağmuru', '2026-05-06', 'Meteor Yağmuru', 'Halley Kuyruklu Yıldızı kaynaklı meteor yağmurlarından biridir.');

INSERT INTO content_suggestions (user_id, suggested_by_name, category, title, description, source_url, status) VALUES
(3, 'Destekçi Kullanıcı', 'ötegezegen', 'TOI-700 d eklensin', 'Yaşanabilir bölge adaylarından biri olduğu için ötegezegenler sayfasına eklenebilir.', 'https://exoplanets.nasa.gov/', 'pending'),
(2, 'Gezgin Kullanıcı', 'uydu', 'Callisto bilgisi eklensin', 'Jüpiter’in büyük Galile uydularından biri olduğu için uydu bölümünde güzel durur.', '', 'pending');
