<?php
// Database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$dbname   = "sentiment_analysis";

$conn = new mysqli($hostname, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure required users table exists for login
if ($conn->query("SHOW TABLES LIKE 'users'")->num_rows === 0) {
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id_user int(11) NOT NULL AUTO_INCREMENT,
        username varchar(16) NOT NULL,
        password varchar(255) NOT NULL,
        nama varchar(70) DEFAULT NULL,
        email varchar(50) DEFAULT NULL,
        PRIMARY KEY (id_user),
        UNIQUE KEY (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $conn->query("INSERT IGNORE INTO users (id_user, username, password, nama, email) VALUES
        (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin@gmail.com')");
}
?>
