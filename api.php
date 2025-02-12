<?php
// market_api.php
header('Content-Type: application/json; charset=utf-8');

// GET parametrelerini al
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$latitude = isset($_GET['lat']) ? (float)$_GET['lat'] : null;
$longitude = isset($_GET['lng']) ? (float)$_GET['lng'] : null;
$distance = isset($_GET['distance']) ? (int)$_GET['distance'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$size = isset($_GET['size']) ? (int)$_GET['size'] : 24;

// Eğer GET isteği yoksa API kullanım bilgilerini göster
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || empty($_GET)) {
    $usage = [
        'status' => 'info',
        'message' => 'Market Fiyat API Kullanım Kılavuzu',
        'version' => '1.0',
        'endpoints' => [
            'Ana Endpoint' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']
        ],
        'parameters' => [
            'search' => [
                'type' => 'string',
                'required' => true,
                'description' => 'Aranacak ürün adı',
                'example' => 'ekmek'
            ],
            'lat' => [
                'type' => 'float',
                'required' => false,
                'description' => 'Enlem değeri',
                'example' => '39.97041'
            ],
            'lng' => [
                'type' => 'float',
                'required' => false,
                'description' => 'Boylam değeri',
                'example' => '32.85647'
            ],
            'distance' => [
                'type' => 'integer',
                'required' => false,
                'description' => 'Metre cinsinden arama mesafesi',
                'example' => '5000'
            ],
            'page' => [
                'type' => 'integer',
                'required' => false,
                'default' => '0',
                'description' => 'Sayfa numarası'
            ],
            'size' => [
                'type' => 'integer',
                'required' => false,
                'default' => '24',
                'description' => 'Sayfa başına sonuç sayısı'
            ]
        ],
        'example_requests' => [
            'Basit Arama' => '?search=ekmek',
            'Konuma Göre Arama' => '?search=ekmek&lat=39.97041&lng=32.85647&distance=5000',
            'Sayfalı Arama' => '?search=ekmek&page=1&size=50'
        ],
        'usage_note' => 'Tüm GET istekleri için UTF-8 karakter kodlaması kullanılmaktadır.'
    ];
    
    echo json_encode($usage, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Zorunlu parametre kontrolü
if (empty($searchTerm)) {
    echo json_encode(['error' => 'Arama terimi gerekli']);
    exit;
}

// CURL isteği için verileri hazırla
$url = 'https://api.marketfiyati.org.tr/api/v2/search';
$postData = [
    'keywords' => $searchTerm,
    'pages' => $page,
    'size' => $size,
    'depots' => []
];

// Opsiyonel konum parametrelerini ekle
if ($latitude !== null && $longitude !== null) {
    $postData['latitude'] = $latitude;
    $postData['longitude'] = $longitude;
}

if ($distance !== null) {
    $postData['distance'] = $distance;
}

// CURL oturumunu başlat
$ch = curl_init($url);

// CURL seçeneklerini ayarla
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
        'Origin: https://marketfiyati.org.tr',
        'Referer: https://marketfiyati.org.tr/',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'sec-ch-ua: "Google Chrome";v="91", "Chromium";v="91"',
        'sec-ch-ua-mobile: ?0',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-site'
    ],
    CURLOPT_POSTFIELDS => json_encode($postData),
    CURLOPT_ENCODING => 'gzip, deflate',
    CURLOPT_TIMEOUT => 30
]);

// İsteği gönder ve yanıtı al
$response = curl_exec($ch);

// CURL hata kontrolü
if (curl_errno($ch)) {
    echo json_encode([
        'error' => 'CURL Hatası',
        'message' => curl_error($ch),
        'code' => curl_errno($ch)
    ]);
    exit;
}

// HTTP durum kodunu kontrol et
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpCode !== 200) {
    $debug = [
        'error' => 'API Hatası',
        'status' => $httpCode,
        'response' => $response,
        'curl_info' => curl_getinfo($ch)
    ];
    echo json_encode($debug);
    exit;
}

// CURL oturumunu kapat
curl_close($ch);

// API yanıtını doğrudan ilet
echo $response;
?>
