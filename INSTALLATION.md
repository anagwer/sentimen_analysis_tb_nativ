# Installation Guide - Sentiment Analysis Website

## Prerequisites
- XAMPP installed (atau MySQL + Apache + PHP)
- PHP 7.4 atau lebih tinggi
- MySQL 5.7+ atau MariaDB 10.4+
- Web browser modern

## Step-by-Step Installation

### 1. Prepare Files
File sudah tersedia di:
```
/Applications/XAMPP/xamppfiles/htdocs/project/sentimen/
```

Struktur direktori:
```
sentimen/
├── index.php
├── dataset.php
├── visualisasi.php
├── prediksi.php
├── riwayat.php
├── setup.php
├── preprocessing.php
├── sentiment_classifier.php
├── api.php
├── dbcon.php
├── session.php
├── head.php
├── footer.php
├── side_bar.php
├── script.php
├── sample_data.csv
├── assets/
│   ├── css/
│   ├── js/
│   ├── lexicon/
│   │   ├── positive.txt
│   │   └── negative.txt
│   └── vendor/
├── database/
│   ├── db_spk_waspas.sql
│   └── sentiment_analysis.sql
├── README.md
├── API_DOCS.md
└── INSTALLATION.md
```

### 2. Database Setup

#### Option A: Using Setup Script (Recommended)
1. Open browser
2. Navigate to: `http://localhost/project/sentimen/setup.php`
3. Script akan otomatis membuat database dan tables
4. Tunggu hingga muncul pesan "Setup Selesai!"

#### Option B: Manual Setup
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create database dengan nama `db_spk_waspas` (jika belum ada)
3. Import file `database/sentiment_analysis.sql`:
   - Klik database `db_spk_waspas`
   - Klik tab "Import"
   - Upload file `sentiment_analysis.sql`
   - Klik "Go"

### 3. Verify Installation

Check apakah semua file sudah ada:
```bash
ls -la /Applications/XAMPP/xamppfiles/htdocs/project/sentimen/
```

Verify database tables:
1. Open phpMyAdmin
2. Select database `db_spk_waspas`
3. Verify tables ada:
   - `datasets`
   - `sentiment_predictions`
   - `lexicon_words` (optional)

### 4. Verify Lexicon Files
```bash
ls -la /Applications/XAMPP/xamppfiles/htdocs/project/sentimen/assets/lexicon/
```

Should show:
- `positive.txt`
- `negative.txt`

Count words:
```bash
wc -l /Applications/XAMPP/xamppfiles/htdocs/project/sentimen/assets/lexicon/*.txt
```

### 5. Start Using Application

1. **Login**
   - Navigate to: `http://localhost/project/sentimen/index.php`
   - Login dengan akun yang ada di database `db_spk_waspas`

2. **Upload Sample Data** (Optional)
   - Go to: `http://localhost/project/sentimen/dataset.php`
   - Upload: `sample_data.csv`
   - Review akan otomatis dianalisis

3. **Test Prediction**
   - Go to: `http://localhost/project/sentimen/prediksi.php`
   - Masukkan teks untuk dianalisis
   - Click "Analisis Sentimen"

4. **View Visualizations**
   - Go to: `http://localhost/project/sentimen/visualisasi.php`
   - Lihat distribusi sentimen dalam bentuk chart

## Troubleshooting

### Problem: "Connection failed: SQLSTATE[HY000] [2002]"
**Solution:**
- Pastikan MySQL/MariaDB sudah berjalan
- Check username & password di `dbcon.php`
- Verify database `db_spk_waspas` ada di MySQL

### Problem: "Table doesn't exist"
**Solution:**
- Run setup.php: `http://localhost/project/sentimen/setup.php`
- Atau manually import `sentiment_analysis.sql`

### Problem: "Lexicon files not found"
**Solution:**
- Verify file path di `sentiment_classifier.php`
- Check file permissions (should be readable)
- Verify `assets/lexicon/` directory exists

### Problem: "Session not found" atau redirect ke login
**Solution:**
- Verify session file: `session.php`
- Check username & password di database `users` table
- Browser cookies should be enabled

### Problem: CSV upload gagal
**Solution:**
- Verify file format (comma-separated, UTF-8 encoding)
- Check CSV headers: `title,url,stars,name,reviewUrl,text`
- Check file size (kurang dari max file upload)
- Verify database write permission

### Problem: Chart tidak tampil
**Solution:**
- Clear browser cache (Ctrl+Shift+Delete)
- Verify ECharts library loaded
- Check browser console for errors (F12)
- Verify dataset ada di database

## Configuration

### Modify Database Connection
Edit `dbcon.php`:
```php
$conn = new mysqli('localhost', 'root', '', 'db_spk_waspas');
```

Change:
- `localhost` → database server
- `root` → database username
- `` → database password
- `db_spk_waspas` → database name

### Modify Max File Upload Size
Edit `php.ini`:
```ini
upload_max_filesize = 20M
post_max_size = 20M
```

Then restart Apache.

### Add Custom Stopwords
Edit `preprocessing.php` function `filteringText()`:
```php
$stopwords = [
    // existing stopwords...
    'kata_baru_1', 'kata_baru_2'
];
```

### Customize Lexicon Weights
Edit `assets/lexicon/positive.txt` atau `negative.txt`:
```txt
kata|bobot
sangat_bagus|2.0
bagus|1.0
```

## File Permissions

Ensure proper permissions:
```bash
chmod 755 /Applications/XAMPP/xamppfiles/htdocs/project/sentimen/
chmod 644 /Applications/XAMPP/xamppfiles/htdocs/project/sentimen/*.php
chmod 644 /Applications/XAMPP/xamppfiles/htdocs/project/sentimen/assets/lexicon/*.txt
```

## Performance Tips

### Database Optimization
1. Add indexes untuk sering di-query fields:
```sql
ALTER TABLE datasets ADD INDEX idx_sentiment (sentiment);
ALTER TABLE sentiment_predictions ADD INDEX idx_created (created_at);
```

2. Archive old predictions:
```sql
DELETE FROM sentiment_predictions WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

### Caching
Add caching untuk lexicon:
```php
$lexicon = apcu_fetch('sentiment_lexicon');
if (!$lexicon) {
    $lexicon = loadLexicon();
    apcu_store('sentiment_lexicon', $lexicon, 3600);
}
```

### Batch Processing
Untuk banyak file CSV:
```bash
# Process multiple CSV files
for file in *.csv; do
    curl -F "csvFile=@$file" http://localhost/project/sentimen/dataset.php
done
```

## Security Considerations

### 1. Remove Setup Script
Setelah installation, delete atau rename `setup.php`:
```bash
rm /Applications/XAMPP/xamppfiles/htdocs/project/sentimen/setup.php
```

### 2. Implement HTTPS
Configure Apache untuk SSL/TLS.

### 3. Add Rate Limiting
Add rate limiting untuk API endpoint di `api.php`.

### 4. Input Validation
All user inputs sudah di-sanitize, tapi verify di production.

### 5. SQL Injection Prevention
Update query dari:
```php
"SELECT * FROM table WHERE id = " . $_GET['id']
```

Ke:
```php
$stmt = $conn->prepare("SELECT * FROM table WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
```

## Backup & Recovery

### Backup Database
```bash
mysqldump -u root -p db_spk_waspas > backup_$(date +%Y%m%d).sql
```

### Restore Database
```bash
mysql -u root -p db_spk_waspas < backup_20260601.sql
```

### Backup Files
```bash
tar -czf sentimen_backup_$(date +%Y%m%d).tar.gz /Applications/XAMPP/xamppfiles/htdocs/project/sentimen/
```

## Upgrading

### Backup first
```bash
cp -r /Applications/XAMPP/xamppfiles/htdocs/project/sentimen/ sentimen_backup/
mysqldump -u root -p db_spk_waspas > sentimen_backup.sql
```

### Update files
Copy new files, keeping:
- `dbcon.php`
- `session.php`
- `assets/lexicon/` (if customized)
- `database/` (backup)

### Run migrations
Update database schema jika ada perubahan.

## Testing

### Unit Tests
Buat test file untuk preprocessing:
```php
<?php
require_once 'preprocessing.php';

echo cleaningText("Halo @user, cek link http://example.com! #hashtag");
// Output: Halo cek link
?>
```

### Integration Tests
Test full flow:
1. Upload CSV
2. Predict text
3. Check database

### Performance Tests
Measure response time:
```bash
time curl -X POST http://localhost/project/sentimen/api.php \
  -H "Content-Type: application/json" \
  -d '{"action":"predict","text":"test text"}'
```

## Support & Documentation

- README.md - Overview & features
- API_DOCS.md - API endpoints & examples
- INSTALLATION.md - This file
- Code comments - Implementation details

---

**Last Updated:** Juni 2026  
**Version:** 1.0  
**Status:** Production Ready
