<?php
// market_api.php
header('Content-Type: application/json; charset=utf-8');

// GET parametrelerini al
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$latitude = isset($_GET['lat']) ? (string)$_GET['lat'] : "";
$longitude = isset($_GET['lng']) ? (string)$_GET['lng'] : "";
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

// Nearest API'ye istek at
$nearestUrl = 'https://api.marketfiyati.org.tr/api/v2/nearest';
$nearestData = [
    'latitude' => $latitude,
    'longitude' => $longitude,
    'distance' => $distance
];
$ch = curl_init($nearestUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_HTTPHEADER => [
		'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
		'Content-Type: application/json',
		'Accept: application/json',
		'Accept-Encoding: gzip, deflate',
		'Origin: https://marketfiyati.org.tr',
		'Referer: https://marketfiyati.org.tr/',
		'Cache-Control: no-cache',
		'Pragma: no-cache',
		'Connection: keep-alive'
	],
    CURLOPT_POSTFIELDS => json_encode($nearestData)
]);
$nearestResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Nearest API hata kontrolü
if ($httpCode !== 200) {
    echo json_encode([ 'error' => 'Nearest API Hatası', 'status' => $httpCode, 'response' => $nearestResponse ]);
    exit;
}

$nearestMarkets = json_decode($nearestResponse, true);

// Depots ID'leri al
$depots = array_map(fn($market) => $market['id'], $nearestMarkets);


// Search API'ye istek at
$searchUrl = 'https://api.marketfiyati.org.tr/api/v2/search';
$searchData = [
    'keywords' => $searchTerm,
    'pages' => $page,
    'size' => $size,
    'depots' => $depots,
    'latitude' => $latitude,
    'longitude' => $longitude,
    'distance' => $distance
];
$ch = curl_init($searchUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_HTTPHEADER => [
		'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
		'Content-Type: application/json',
		'Accept: application/json',
		'Accept-Encoding: gzip, deflate',
		'Origin: https://marketfiyati.org.tr',
		'Referer: https://marketfiyati.org.tr/',
		'Cache-Control: no-cache',
		'Pragma: no-cache',
		'Connection: keep-alive'
	],
    CURLOPT_POSTFIELDS => json_encode($searchData)
]);
$searchResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Search API hata kontrolü
if ($httpCode !== 200) {
    echo json_encode([ 'error' => 'Search API Hatası', 'status' => $httpCode, 'response' => $searchResponse ]);
    exit;
}

echo $searchResponse;
