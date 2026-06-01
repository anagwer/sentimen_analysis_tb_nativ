<?php
/**
 * Database Setup Script untuk Sentiment Analysis
 * Jalankan file ini sekali untuk membuat database dan table
 * URL: http://localhost/project/sentimen/setup.php
 */

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "db_spk_waspas";

// Connect to MySQL without selecting database
$conn = new mysqli($hostname, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Setup Database Sentiment Analysis</h2>";
echo "<hr>";

// Create database if not exists
$sqlDb = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sqlDb) === TRUE) {
    echo "✓ Database '$dbname' berhasil dibuat atau sudah ada<br>";
} else {
    echo "✗ Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db($dbname);

// Create tables
$tables = [
    // Datasets table
    "CREATE TABLE IF NOT EXISTS datasets (
        id_dataset INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        url LONGTEXT NOT NULL,
        stars INT(11),
        name VARCHAR(100) NOT NULL,
        reviewUrl LONGTEXT,
        text LONGTEXT NOT NULL,
        sentiment ENUM('positif', 'negatif', 'netral') DEFAULT NULL,
        score DECIMAL(10, 2) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    // Sentiment predictions table
    "CREATE TABLE IF NOT EXISTS sentiment_predictions (
        id_prediction INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        user_input LONGTEXT NOT NULL,
        sentiment_result ENUM('positif', 'negatif', 'netral') NOT NULL,
        score DECIMAL(10, 2) DEFAULT NULL,
        confidence DECIMAL(5, 2) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    // Lexicon words table
    "CREATE TABLE IF NOT EXISTS lexicon_words (
        id_word INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        word VARCHAR(100) NOT NULL UNIQUE,
        weight DECIMAL(10, 2) NOT NULL,
        sentiment_type ENUM('positif', 'negatif') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        // Extract table name
        preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $sql, $matches);
        echo "✓ Table '{$matches[1]}' berhasil dibuat atau sudah ada<br>";
    } else {
        echo "✗ Error creating table: " . $conn->error . "<br>";
    }
}

// Add indexes
$indexes = [
    "ALTER TABLE datasets ADD FULLTEXT INDEX ft_text (text)",
    "ALTER TABLE sentiment_predictions ADD FULLTEXT INDEX ft_input (user_input)"
];

foreach ($indexes as $sql) {
    $result = $conn->query($sql);
    // Ignore duplicate key error
}

echo "<hr>";
echo "<h3>Setup Selesai!</h3>";
echo "<p>Database dan tabel telah berhasil dibuat.</p>";
echo "<p><a href='index.php' class='btn btn-primary'>Kembali ke Dashboard</a></p>";

// Check if lexicon files exist
echo "<hr>";
echo "<h3>Status Lexicon Files:</h3>";

$lexiconFiles = [
    'assets/lexicon/positive.txt' => 'Positive Lexicon',
    'assets/lexicon/negative.txt' => 'Negative Lexicon'
];

foreach ($lexiconFiles as $file => $name) {
    if (file_exists($file)) {
        $lineCount = count(file($file));
        echo "✓ $name: $lineCount kata<br>";
    } else {
        echo "✗ $name: File tidak ditemukan<br>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Setup Database Sentiment Analysis</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            padding: 30px;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Content akan di-generate oleh PHP di atas -->
</div>
</body>
</html>
