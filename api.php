<?php
// market_api.php
header('Content-Type: application/json; charset=utf-8');

// GET parametrelerini al
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$latitude = isset($_GET['lat']) ? (string)$_GET['lat'] : "39.97040741870188";
$longitude = isset($_GET['lng']) ? (string)$_GET['lng'] : "32.856388362435595";
$distance = isset($_GET['distance']) ? (int)$_GET['distance'] : 1;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$size = isset($_GET['size']) ? (int)$_GET['size'] : 24;

// Eğer GET isteği yoksa API kullanım bilgilerini göster
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || empty($_GET)) {
    $usage = [
        'status' => 'info',
        'message' => 'Market Fiyat API Kullanım Kılavuzu',
        'version' => '1.0'
    ];
    
    echo json_encode($usage, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Zorunlu parametre kontrolü
if (empty($searchTerm)) {
    echo json_encode(['error' => 'Arama terimi gerekli']);
    exit;
}

// API URL'si
$url = 'https://api.marketfiyati.org.tr/api/v2/search';

// POST verisi
$postData = [
    'keywords' => $searchTerm,
    'pages' => $page,
    'size' => $size,
    'depots' => ["bim-4809", "bim-4083", "sok-2580", "a101-C771", "bim-H248", "a101-I589", "sok-8220", "migros-7019", "migros-5286", "bim-7540", "a101-J414"],
    'latitude' => $latitude,
    'longitude' => $longitude,
    'distance' => $distance
];

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
		'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Content-Type: application/json',
        'Accept: application/json',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-site'
    ],
    CURLOPT_POSTFIELDS => json_encode($postData),
    CURLOPT_ENCODING => 'gzip, deflate',
    CURLOPT_TIMEOUT => 20
]);

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
    echo json_encode([
        'error' => 'API Hatası',
        'status' => $httpCode,
        'response' => $response
    ]);
    exit;
}

// CURL oturumunu kapat
curl_close($ch);

// API yanıtını ilet
echo $response;
