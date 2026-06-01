# Website Analisis Sentimen

Website analisis sentimen yang dibangun dengan mengubah template SPK WASPAS menjadi aplikasi sentiment analysis menggunakan metode Lexicon-Based dengan PHP.

## 🎯 Fitur Utama

- **Dashboard**: Menampilkan statistik dataset dan prediksi sentimen
- **Dataset Management**: Upload dan analisis dataset CSV dengan format terstruktur
- **Visualisasi**: Chart distribusi sentimen (pie, bar, doughnut chart)
- **Prediksi Real-time**: Analisis sentimen untuk teks baru secara langsung
- **Riwayat**: Tracking lengkap semua prediksi yang telah dilakukan

## 📋 Struktur File

```
sentimen/
├── index.php                    # Dashboard utama
├── dataset.php                  # Halaman manage dataset
├── visualisasi.php             # Halaman visualisasi chart
├── prediksi.php                # Halaman prediksi sentimen
├── riwayat.php                 # Halaman riwayat prediksi
├── setup.php                   # Setup database (jalankan sekali)
├── dbcon.php                   # Database connection
├── session.php                 # Session management
├── head.php                    # Header/meta tags
├── footer.php                  # Footer template
├── side_bar.php                # Sidebar navigation
├── script.php                  # Scripts template
├── preprocessing.php           # Text preprocessing library
├── sentiment_classifier.php    # Sentiment classification logic
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   ├── lexicon/
│   │   ├── positive.txt       # Kamus kata positif
│   │   └── negative.txt       # Kamus kata negatif
│   ├── vendor/                # Library eksternal
│   └── img/
└── database/
    ├── db_spk_waspas.sql      # Database original
    └── sentiment_analysis.sql # Database sentiment analysis
```

## 🚀 Instalasi & Setup

### 1. Copy Files
Semua file sudah tersedia di direktori `/Applications/XAMPP/xamppfiles/htdocs/project/sentimen/`

### 2. Setup Database
Buka browser dan akses:
```
http://localhost/project/sentimen/setup.php
```

File ini akan otomatis membuat:
- Tabel `datasets` - menyimpan data review
- Tabel `sentiment_predictions` - menyimpan hasil prediksi user
- Tabel `lexicon_words` - menyimpan kamus sentimen (optional)

### 3. Login
Gunakan akun yang sudah ada di database `db_spk_waspas`:
- Username: (sesuai data user di database)
- Password: (sesuai password di database)

## 📊 Cara Menggunakan

### Dashboard (index.php)
Menampilkan:
- Total dataset yang sudah dianalisis
- Jumlah sentimen positif, negatif, dan netral
- Total prediksi yang telah dilakukan
- Statistik lexicon (jumlah kata positif/negatif)

### Dataset (dataset.php)
1. Upload file CSV dengan format:
   ```
   title, url, stars, name, reviewUrl, text
   Taman Kyai Langgeng, https://..., 5, John Doe, https://..., "Review text here"
   ```
2. Sistem otomatis:
   - Membaca file CSV
   - Melakukan preprocessing pada setiap review
   - Mengklasifikasi sentimen (positif/negatif/netral)
   - Menyimpan ke database

3. Menampilkan tabel dataset dengan sentiment analysis hasil

### Visualisasi (visualisasi.php)
Menampilkan:
- Pie Chart - distribusi sentimen dalam persen
- Bar Chart - jumlah review per sentimen
- Doughnut Chart - detail sentimen dengan visualisasi ring
- Tabel summary statistik

### Prediksi Sentimen (prediksi.php)
1. Masukkan teks/review yang ingin dianalisis
2. Klik "Analisis Sentimen"
3. Sistem menampilkan:
   - Hasil sentimen (Positif/Negatif/Netral)
   - Skor sentimen
   - Confidence level
   - Detail kata positif dan negatif yang ditemukan

### Riwayat (riwayat.php)
Menampilkan:
- Daftar lengkap semua prediksi yang telah dilakukan
- Waktu, teks input, hasil, dan confidence
- Statistik (total prediksi, rata-rata confidence, sentimen terbanyak)
- Chart distribusi sentimen dari user predictions

## 🔧 Teknologi & Library

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL/MariaDB** - Database
- **Sentiment Classifier** - Custom class untuk classification

### Frontend
- **Bootstrap 5** - CSS Framework
- **ECharts 5** - Data visualization
- **ApexCharts** - Alternative charting
- **DataTables** - Table management
- **Quill Editor** - Text editor (optional)

## 📝 Proses Analisis Sentimen

### 1. Text Preprocessing
Dilakukan di `preprocessing.php`:
- **Cleaning**: Hapus @mention, #hashtag, URL, angka, tanda baca
- **Case Folding**: Konversi ke huruf kecil
- **Tokenizing**: Pisahkan teks menjadi kata-kata
- **Stop Words Removal**: Hapus kata-kata umum (a, the, dan, di, dll)
- **Slang Words Normalization**: Ganti kata tidak baku (ga→tidak, suka→suka, dll)
- **Stemming**: Ubah kata ke bentuk dasar

### 2. Sentiment Classification
Dilakukan di `sentiment_classifier.php`:
- Bandingkan kata hasil preprocessing dengan lexicon
- Hitung skor: +1 untuk kata positif, -1 untuk kata negatif
- Klasifikasi:
  - **Positif** jika skor > 0
  - **Negatif** jika skor < 0
  - **Netral** jika skor = 0

### 3. Confidence Calculation
- Confidence = (|skor| × 10) capped di 100%
- Semakin tinggi skor, semakin tinggi confidence

## 📚 Format Data

### CSV Input Format
```csv
title,url,stars,name,reviewUrl,text
"Taman Kyai Langgeng - Tempat Wisata Magelang","https://www.google.com/maps/search/?api=1&query=Taman+Kyai+Langgeng",5,"Yusuf Noufal Rahman","https://www.google.com/maps/reviews/data=!4m8!...","Sudah tiga kali ke tempat ini dengan rentang waktu..."
```

### Database Schema

**datasets table:**
- id_dataset (INT, Primary Key)
- title (VARCHAR 255)
- url (LONGTEXT)
- stars (INT)
- name (VARCHAR 100)
- reviewUrl (LONGTEXT)
- text (LONGTEXT)
- sentiment (ENUM: positif, negatif, netral)
- score (DECIMAL)
- created_at (TIMESTAMP)

**sentiment_predictions table:**
- id_prediction (INT, Primary Key)
- user_input (LONGTEXT)
- sentiment_result (ENUM: positif, negatif, netral)
- score (DECIMAL)
- confidence (DECIMAL)
- created_at (TIMESTAMP)

## 🎨 Customization

### Menambah Kata ke Lexicon
Edit file `assets/lexicon/positive.txt` atau `assets/lexicon/negative.txt`:
```
kata|bobot
bagus|1.0
sangat bagus|2.0
```

Format: `kata|bobot` (separated by pipe)

### Mengubah Threshold Sentimen
Ubah di `sentiment_classifier.php` di method `classifySentiment()`:
```php
if ($score > 0.5) {
    return 'positif';
} elseif ($score < -0.5) {
    return 'negatif';
}
```

### Menambah Stopwords
Edit di `preprocessing.php` di function `filteringText()`:
```php
$stopwords = [
    // ... existing stopwords ...
    'kata_baru', 'stopword_baru'
];
```

## ⚠️ Important Notes

1. **Database Charset**: Pastikan menggunakan UTF-8 untuk support bahasa Indonesia
2. **File Permissions**: Lexicon files harus readable
3. **CSV Encoding**: Gunakan UTF-8 when saving CSV files
4. **Session**: Login diperlukan untuk mengakses aplikasi
5. **File Upload**: Max file size bisa disesuaikan di `php.ini`

## 🔐 Security Recommendations

1. Jangan expose `setup.php` di production - rename atau delete setelah setup
2. Gunakan prepared statements untuk database queries
3. Validate & sanitize semua user inputs
4. Implementasi rate limiting untuk prediksi
5. Add CSRF protection pada form submissions

## 📞 Support

Untuk pertanyaan atau bug reports, silakan hubungi developer.

---

**Versi**: 1.0  
**Last Updated**: Juni 2026  
**Database**: MySQL 5.7+ / MariaDB 10.4+
