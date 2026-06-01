-- Database untuk Sentiment Analysis
-- Tabel untuk menyimpan dataset review

CREATE TABLE `datasets` (
  `id_dataset` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `stars` int(11),
  `name` varchar(100) NOT NULL,
  `reviewUrl` text,
  `text` longtext NOT NULL,
  `sentiment` enum('positif', 'negatif', 'netral') DEFAULT NULL,
  `score` decimal(10, 2) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk menyimpan prediksi sentimen dari user
CREATE TABLE `sentiment_predictions` (
  `id_prediction` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_input` longtext NOT NULL,
  `sentiment_result` enum('positif', 'negatif', 'netral') NOT NULL,
  `score` decimal(10, 2) DEFAULT NULL,
  `confidence` decimal(5, 2) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel lexicon untuk kata-kata positif dan negatif
CREATE TABLE `lexicon_words` (
  `id_word` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `word` varchar(100) NOT NULL UNIQUE,
  `weight` decimal(10, 2) NOT NULL,
  `sentiment_type` enum('positif', 'negatif') NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `datasets` ADD FULLTEXT INDEX `ft_text` (`text`);
ALTER TABLE `sentiment_predictions` ADD FULLTEXT INDEX `ft_input` (`user_input`);
