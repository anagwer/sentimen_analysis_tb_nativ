# Website Analisis Sentimen

Website analisis sentimen yang dibangun dengan mengubah template SPK WASPAS menjadi aplikasi sentiment analysis menggunakan metode Lexicon-Based dengan PHP dan MySQL.

## 🎯 Fitur Utama

- **Dashboard**: Menampilkan statistik dataset, total prediksi sentimen, dan statistik lexicon.
- **Dataset Management**: Upload dan analisis dataset CSV dengan format terstruktur serta otomatisasi klasifikasi sentimen.
- **Visualisasi Sentimen**: Chart distribusi sentimen (Pie Chart, Bar Chart, Doughnut Chart) dan ringkasan persentase.
- **Word Cloud & Text Analytics**: Visualisasi Awan Kata (*Word Cloud*) interaktif, komparasi kata sentimen positif vs negatif, grafik 20 kata terbanyak, analisis frasa 2 kata (*Bigram*), dan tabel frekuensi kata.
- **Prediksi Real-time**: Analisis sentimen untuk teks baru secara langsung dengan skor detail dan tingkat keyakinan (*confidence*).
- **Riwayat Prediksi**: Tracking lengkap semua prediksi yang telah dilakukan beserta ekspor dan statistik.
- **Keamanan & Autentikasi Login**: Dilengkapi fitur **Simbol Mata (Show/Hide Password)** dan modal **Lupa Password** untuk menyetel ulang password akun secara mandiri.

## 📋 Struktur File

```
sentimen_analysis_tb_nativ/
├── index.php                    # Dashboard utama
├── dataset.php                  # Halaman manage dataset
├── visualisasi.php             # Halaman visualisasi chart distribusi sentimen
├── wordcloud.php               # Halaman visualisasi Word Cloud & Text Analytics
├── prediksi.php                # Halaman prediksi sentimen real-time
├── riwayat.php                 # Halaman riwayat prediksi
├── login.php                   # Halaman login dengan fitur toggle eye & lupa password
├── setup.php                   # Setup database (jalankan sekali)
├── dbcon.php                   # Database connection
├── session.php                 # Session management & user validation
├── head.php                    # Header/meta tags & CSS vendors
├── footer.php                  # Footer template
├── side_bar.php                # Sidebar navigation
├── script.php                  # Scripts template
├── preprocessing.php           # Text preprocessing library (cleaning, filtering, stemming)
├── sentiment_classifier.php    # Sentiment classification logic & lexicon loader
├── sample_data.csv             # Contoh data sampel CSV
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   ├── lexicon/
│   │   ├── positive.txt       # Kamus kata positif
│   │   └── negative.txt       # Kamus kata negatif
│   ├── vendor/                # Library eksternal (Bootstrap, ECharts, ApexCharts, dll)
│   └── img/
└── database/
    ├── db_spk_waspas.sql      # Database original
    └── sentiment_analysis.sql # Database sentiment analysis
```

## 🚀 Instalasi & Setup

### 1. Copy Files
Salin folder proyek ke direktori web server XAMPP: `c:\xampp\htdocs\project\sentimen_analysis_tb_nativ\`

### 2. Setup Database
Buka browser dan akses:
```
http://localhost/project/sentimen_analysis_tb_nativ/setup.php
```

File ini akan otomatis membuat:
- Tabel `datasets` - menyimpan data review
- Tabel `sentiment_predictions` - menyimpan hasil prediksi user
- Tabel `lexicon_words` - menyimpan kamus sentimen

### 3. Login Sistem
Buka halaman login: `http://localhost/project/sentimen_analysis_tb_nativ/login.php`
- Gunakan ikon **Simbol Mata** untuk melihat password yang diketik.
- Gunakan tombol **Lupa Password?** jika lupa password akun terdaftar untuk menyetel ulang password secara langsung.

---

## 📊 Cara Menggunakan

### Dashboard (`index.php`)
Menampilkan:
- Total dataset yang sudah dianalisis
- Jumlah sentimen positif, negatif, dan netral
- Total prediksi yang telah dilakukan oleh pengguna
- Statistik lexicon (jumlah kata positif/negatif)

### Dataset (`dataset.php`)
1. Upload file CSV dengan format:
   ```csv
   title, url, stars, name, reviewUrl, text
   Taman Kyai Langgeng, https://..., 5, John Doe, https://..., "Review text here"
   ```
2. Sistem otomatis:
   - Membaca file CSV
   - Melakukan preprocessing pada setiap review
   - Mengklasifikasi sentimen (positif/negatif/netral)
   - Menyimpan ke database
3. Menampilkan tabel dataset dengan hasil sentiment analysis.

### Visualisasi (`visualisasi.php`)
Menampilkan:
- **Pie Chart**: Distribusi sentimen dalam persen
- **Bar Chart**: Jumlah review per sentimen
- **Doughnut Chart**: Detail sentimen dengan visualisasi ring
- **Tabel Ringkasan**: Rekapitulasi jumlah dan persentase sentimen

### Word Cloud & Text Analytics (`wordcloud.php`)
Menampilkan:
- **Kartu Ringkasan Kata**: Total token kata terproses, jumlah kosa kata unik, kata positif utama, dan kata negatif utama.
- **Filter Sentimen & Batas Kata**: Pilihan filter berdasarkan kategori sentimen (*Positif, Negatif, Netral*) dan batas kata (*50 - 200 kata*).
- **Word Cloud Interaktif**: Visualisasi awan kata berbasis ECharts dan Tag Cloud View.
- **Side-by-Side Word Cloud**: Perbandingan kata-kata sentimen positif vs kata-kata sentimen negatif secara visual.
- **Top 20 Kata & Top 10 Bigram**: Grafik batang horizontal kata terbanyak dan frasa 2 kata (*Bigram*) yang paling sering muncul.
- **Tabel Detail Frekuensi Kata**: Daftar kata terproses lengkap dengan status kemunculan per sentimen dan tag dominasi.

### Prediksi Sentimen (`prediksi.php`)
1. Masukkan teks/review yang ingin dianalisis
2. Klik "Analisis Sentimen"
3. Sistem menampilkan:
   - Hasil sentimen (Positif/Negatif/Netral)
   - Skor sentimen
   - Confidence level (%)
   - Detail kata positif dan negatif yang ditemukan

### Riwayat (`riwayat.php`)
Menampilkan:
- Daftar lengkap semua prediksi yang telah dilakukan
- Waktu, teks input, hasil, dan confidence
- Statistik (total prediksi, rata-rata confidence, sentimen terbanyak)
- Chart distribusi sentimen dari user predictions

---

## 🔧 Teknologi & Library

### Backend
- **PHP 7.4+ / PHP 8.x** - Server-side scripting
- **MySQL/MariaDB** - Database
- **Sentiment Classifier** - Custom class untuk classification berbasis Lexicon

### Frontend
- **Bootstrap 5** - CSS Framework
- **Bootstrap Icons** - Icon library
- **ECharts 5 & ECharts WordCloud** - Data & text visualization
- **DataTables** - Table management & interactive pagination

---

## 📝 Proses Analisis Sentimen

### 1. Text Preprocessing
Dilakukan di `preprocessing.php`:
- **Cleaning**: Hapus @mention, #hashtag, URL, angka, dan tanda baca.
- **Case Folding**: Konversi teks ke huruf kecil.
- **Tokenizing**: Pisahkan teks menjadi kata-kata individual.
- **Filtering / Stop Words Removal**: Hapus kata-kata umum yang tidak berpengaruh (dan, di, ke, yang, dll).
- **Stemming**: Ubah kata ke bentuk dasar menggunakan aturan stemming Bahasa Indonesia.

### 2. Sentiment Classification
Dilakukan di `sentiment_classifier.php`:
- Bandingkan kata hasil preprocessing dengan lexicon.
- Hitung skor: Tambah bobot untuk kata positif, kurangi bobot untuk kata negatif.
- Klasifikasi:
  - **Positif** jika skor > 0
  - **Negatif** jika skor < 0
  - **Netral** jika skor = 0

---

## 🔐 Keamanan & Login

1. **Password Toggle (Simbol Mata)**:
   - Tombol toggle mata interaktif pada input password di halaman login dan modal reset.
2. **Lupa Password**:
   - Pengguna dapat menyetel ulang password secara mandiri melalui modal dengan memasukkan Username atau Email terdaftar.

---

## 📞 Support

Untuk pertanyaan atau bug reports, silakan hubungi developer.

---

**Versi**: 1.1  
**Last Updated**: Agustus 2026  
**Database**: MySQL 5.7+ / MariaDB 10.4+
