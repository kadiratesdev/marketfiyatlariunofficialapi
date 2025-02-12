# 🛒 Market Fiyat API

Türkiye'deki market fiyatlarını sorgulayabileceğiniz basit bir API wrapper. Veriler halka açık olarak sunulan marketfiyatlari.org.tr'den canlı olarak çekilmektedir. Bu API ile ürün araması yapabilir, konuma göre filtreleyebilir ve fiyat karşılaştırması yapabilirsiniz.

## 🚀 Özellikler

- ✨ Basit ve kullanımı kolay API
- 📍 Konum bazlı arama
- 📊 Sayfalama desteği
- 🔍 Gelişmiş ürün araması
- 🌍 UTF-8 karakter desteği
- ⚡ Hızlı yanıt süreleri

## 📦 Kurulum

1. Dosyaları sunucunuza yükleyin:
```bash
git clone https://github.com/yourusername/market-fiyat-api.git
cd market-fiyat-api
```

2. API'yi kullanmaya başlayın!

## 🔧 Kullanım

### Basit Arama

```http
GET /api.php?search=ekmek
```

### Konum Bazlı Arama

```http
GET /api.php?search=ekmek&lat=39.97041&lng=32.85647&distance=5000
```

### Sayfalama ile Arama

```http
GET /api.php?search=ekmek&page=1&size=50
```

## 📝 Parametreler

| Parametre | Tip     | Zorunlu | Açıklama                         |
|-----------|---------|---------|----------------------------------|
| search    | string  | Evet    | Aranacak ürün adı               |
| lat       | float   | Hayır   | Enlem değeri                    |
| lng       | float   | Hayır   | Boylam değeri                   |
| distance  | integer | Hayır   | Metre cinsinden arama mesafesi  |
| page      | integer | Hayır   | Sayfa numarası (varsayılan: 0)  |
| size      | integer | Hayır   | Sayfa başına sonuç (varsayılan: 24) |

## 📋 Örnek Yanıt

```json
{
  "products": [
    {
      "name": "Ekmek",
      "price": "7.00",
      "store": "Market A",
      "location": "Ankara/Çankaya"
    }
  ],
  "total": 1,
  "page": 0,
  "size": 24
}
```

## ⚠️ Hata Yanıtları

```json
{
  "error": "Arama terimi gerekli"
}
```

## 🔒 Güvenlik

- SSL sertifika doğrulaması kapalıdır
- CORS kontrolleri yapılmaktadır
- Temel hata yönetimi mevcuttur

---
⭐️ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!
