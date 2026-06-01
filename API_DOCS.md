# API Documentation - Sentiment Analysis

## Base URL
```
http://localhost/project/sentimen/api.php
```

## Authentication
API ini menggunakan session-based authentication. User harus login terlebih dahulu sebelum mengakses API.

## Response Format
Semua responses dalam format JSON dengan struktur:
```json
{
  "success": true/false,
  "message": "Status message",
  "data": { /* response data */ }
}
```

## Endpoints

### 1. Predict Sentiment
Melakukan prediksi sentimen untuk teks input.

**Method:** POST

**Content-Type:** application/json

**Request Body:**
```json
{
  "action": "predict",
  "text": "Teks yang ingin dianalisis"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Prediction successful",
  "data": {
    "text": "Teks yang ingin dianalisis",
    "sentiment": "positif",
    "score": 2.5,
    "confidence": 25.0,
    "numeric_label": 2
  }
}
```

**Example cURL:**
```bash
curl -X POST http://localhost/project/sentimen/api.php \
  -H "Content-Type: application/json" \
  -d '{"action":"predict","text":"Produk ini sangat bagus dan memuaskan"}'
```

**Example JavaScript:**
```javascript
fetch('http://localhost/project/sentimen/api.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    action: 'predict',
    text: 'Produk ini sangat bagus dan memuaskan'
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

### 2. Predict Detailed
Melakukan prediksi dengan detail kata positif dan negatif yang ditemukan.

**Method:** POST

**Content-Type:** application/json

**Request Body:**
```json
{
  "action": "predict_detailed",
  "text": "Teks yang ingin dianalisis"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Detailed prediction successful",
  "data": {
    "text": "Teks yang ingin dianalisis",
    "sentiment": "positif",
    "total_score": 2.5,
    "positive_score": 3.0,
    "negative_score": 0.5,
    "positive_words": {
      "bagus": 1.0,
      "cantik": 1.0,
      "senang": 1.0
    },
    "negative_words": {
      "jelek": 0.5
    },
    "confidence": 25.0,
    "numeric_label": 2
  }
}
```

### 3. Get Distribution
Mendapatkan distribusi sentimen dari dataset yang ada.

**Method:** POST

**Content-Type:** application/json

**Request Body:**
```json
{
  "action": "get_distribution"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Distribution retrieved",
  "data": {
    "positif": {
      "count": 150,
      "percentage": 42.86
    },
    "negatif": {
      "count": 180,
      "percentage": 51.43
    },
    "netral": {
      "count": 20,
      "percentage": 5.71
    },
    "total": 350
  }
}
```

### 4. Get Statistics
Mendapatkan statistik lexicon (jumlah kata positif dan negatif).

**Method:** POST

**Content-Type:** application/json

**Request Body:**
```json
{
  "action": "get_stats"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Stats retrieved",
  "data": {
    "positive_words_count": 245,
    "negative_words_count": 198,
    "total_words": 443
  }
}
```

## Sentiment Labels

- **positif** (2): Score > 0
- **negatif** (0): Score < 0
- **netral** (1): Score = 0

## Score Interpretation

- Positive Score: Jumlah bobot kata positif yang ditemukan
- Negative Score: Jumlah bobot kata negatif yang ditemukan
- Total Score: Positive Score - Negative Score
- Confidence: Min(|Total Score| × 10, 100)

## Error Handling

**Invalid Request:**
```json
{
  "success": false,
  "message": "Invalid request",
  "data": null
}
```

**Missing Parameters:**
```json
{
  "success": false,
  "message": "Invalid request",
  "data": null
}
```

**Server Error:**
```json
{
  "success": false,
  "message": "Error: [error details]",
  "data": null
}
```

## Rate Limiting
Belum diimplementasikan. Untuk production, tambahkan rate limiting.

## CORS
API ini tidak memiliki CORS headers. Untuk cross-origin requests, tambahkan:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

## Examples

### Python
```python
import requests
import json

url = 'http://localhost/project/sentimen/api.php'

data = {
    'action': 'predict',
    'text': 'Produk ini sangat bagus dan berkualitas tinggi'
}

headers = {'Content-Type': 'application/json'}

response = requests.post(url, json=data, headers=headers)
result = response.json()

print(f"Sentiment: {result['data']['sentiment']}")
print(f"Score: {result['data']['score']}")
print(f"Confidence: {result['data']['confidence']}%")
```

### JavaScript Fetch
```javascript
const text = 'Layanan pelanggan mereka sangat membantu dan ramah';

fetch('http://localhost/project/sentimen/api.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    action: 'predict_detailed',
    text: text
  })
})
.then(res => res.json())
.then(data => {
  if (data.success) {
    const result = data.data;
    console.log(`Text: ${result.text}`);
    console.log(`Sentiment: ${result.sentiment}`);
    console.log(`Score: ${result.total_score}`);
    console.log(`Positive Words:`, result.positive_words);
    console.log(`Negative Words:`, result.negative_words);
  }
});
```

### cURL Detailed
```bash
curl -X POST http://localhost/project/sentimen/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "action": "predict_detailed",
    "text": "Tempat ini sangat bagus dan menyenangkan untuk dikunjungi"
  }' \
  -s | jq .
```

## Notes

- Semua teks otomatis di-preprocess sebelum analisis
- Database menyimpan semua prediksi untuk tracking
- Lexicon dapat diupdate tanpa restart aplikasi
- API response time tergantung ukuran teks dan jumlah kata dalam lexicon

---

**Last Updated:** Juni 2026  
**Version:** 1.0
