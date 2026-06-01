<?php
/**
 * Sentiment Classifier Library
 * Mengklasifikasi sentimen menggunakan Lexicon-based approach
 */

require_once 'preprocessing.php';

class SentimentClassifier {
    private $positiveLexicon = [];
    private $negativeLexicon = [];
    private $conn;
    
    public function __construct($conn = null) {
        $this->conn = $conn;
        $this->loadLexicons();
    }
    
    /**
     * Load lexicon dari file
     */
    private function loadLexicons() {
        // Load positive lexicon
        $positiveFile = __DIR__ . '/assets/lexicon/positive.txt';
        if (file_exists($positiveFile)) {
            $lines = file($positiveFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $parts = explode('|', trim($line));
                if (count($parts) == 2) {
                    $word = strtolower(trim($parts[0]));
                    $weight = (float)trim($parts[1]);
                    $this->positiveLexicon[$word] = $weight;
                }
            }
        }
        
        // Load negative lexicon
        $negativeFile = __DIR__ . '/assets/lexicon/negative.txt';
        if (file_exists($negativeFile)) {
            $lines = file($negativeFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $parts = explode('|', trim($line));
                if (count($parts) == 2) {
                    $word = strtolower(trim($parts[0]));
                    $weight = (float)trim($parts[1]);
                    $this->negativeLexicon[$word] = $weight;
                }
            }
        }
    }
    
    /**
     * Hitung skor sentimen dari teks
     */
    public function calculateScore($text) {
        if (empty($text)) {
            return 0;
        }
        
        // Preprocess teks
        $processedText = preprocessText($text);
        
        // Tokenize
        $words = tokenizingText($processedText);
        
        $score = 0;
        foreach ($words as $word) {
            $word = strtolower($word);
            
            if (isset($this->positiveLexicon[$word])) {
                $score += $this->positiveLexicon[$word];
            } elseif (isset($this->negativeLexicon[$word])) {
                $score -= $this->negativeLexicon[$word];
            }
        }
        
        return $score;
    }
    
    /**
     * Klasifikasi sentimen berdasarkan skor
     * Return: positif (2), negatif (0), atau netral (1)
     */
    public function classifySentiment($text) {
        $score = $this->calculateScore($text);
        
        if ($score > 0) {
            return [
                'label' => 'positif',
                'score' => $score,
                'numeric' => 2
            ];
        } elseif ($score < 0) {
            return [
                'label' => 'negatif',
                'score' => $score,
                'numeric' => 0
            ];
        } else {
            return [
                'label' => 'netral',
                'score' => $score,
                'numeric' => 1
            ];
        }
    }
    
    /**
     * Prediksi sentimen dengan detail
     */
    public function predict($text) {
        $result = $this->classifySentiment($text);
        $score = $result['score'];
        
        // Hitung confidence (simplified)
        $confidence = min(abs($score) * 10, 100);
        
        return [
            'text' => $text,
            'sentiment' => $result['label'],
            'score' => round($score, 2),
            'confidence' => round($confidence, 2),
            'numeric_label' => $result['numeric']
        ];
    }
    
    /**
     * Prediksi dengan penjelasan detail
     */
    public function predictDetailed($text) {
        $processedText = preprocessText($text);
        $words = tokenizingText($processedText);
        
        $positiveWords = [];
        $negativeWords = [];
        $positiveScore = 0;
        $negativeScore = 0;
        
        foreach ($words as $word) {
            $word = strtolower($word);
            
            if (isset($this->positiveLexicon[$word])) {
                $positiveWords[$word] = $this->positiveLexicon[$word];
                $positiveScore += $this->positiveLexicon[$word];
            } elseif (isset($this->negativeLexicon[$word])) {
                $negativeWords[$word] = $this->negativeLexicon[$word];
                $negativeScore += $this->negativeLexicon[$word];
            }
        }
        
        $totalScore = $positiveScore - $negativeScore;
        
        if ($totalScore > 0) {
            $sentiment = 'positif';
            $numeric = 2;
        } elseif ($totalScore < 0) {
            $sentiment = 'negatif';
            $numeric = 0;
        } else {
            $sentiment = 'netral';
            $numeric = 1;
        }
        
        return [
            'text' => $text,
            'sentiment' => $sentiment,
            'total_score' => round($totalScore, 2),
            'positive_score' => round($positiveScore, 2),
            'negative_score' => round($negativeScore, 2),
            'positive_words' => $positiveWords,
            'negative_words' => $negativeWords,
            'confidence' => round(min(abs($totalScore) * 10, 100), 2),
            'numeric_label' => $numeric
        ];
    }
    
    /**
     * Dapatkan statistik lexicon
     */
    public function getLexiconStats() {
        return [
            'positive_words_count' => count($this->positiveLexicon),
            'negative_words_count' => count($this->negativeLexicon),
            'total_words' => count($this->positiveLexicon) + count($this->negativeLexicon)
        ];
    }
    
    /**
     * Get distribution dari dataset
     */
    public function getDatasetDistribution() {
        if ($this->conn === null) {
            return null;
        }
        
        $query = "SELECT 
                    sentiment, 
                    COUNT(*) as total 
                  FROM datasets 
                  WHERE sentiment IS NOT NULL 
                  GROUP BY sentiment";
        
        $result = $this->conn->query($query);
        
        $distribution = [
            'positif' => 0,
            'negatif' => 0,
            'netral' => 0
        ];
        
        while ($row = $result->fetch_assoc()) {
            $distribution[$row['sentiment']] = $row['total'];
        }
        
        $total = array_sum($distribution);
        
        return [
            'positif' => [
                'count' => $distribution['positif'],
                'percentage' => $total > 0 ? round(($distribution['positif'] / $total) * 100, 2) : 0
            ],
            'negatif' => [
                'count' => $distribution['negatif'],
                'percentage' => $total > 0 ? round(($distribution['negatif'] / $total) * 100, 2) : 0
            ],
            'netral' => [
                'count' => $distribution['netral'],
                'percentage' => $total > 0 ? round(($distribution['netral'] / $total) * 100, 2) : 0
            ],
            'total' => $total
        ];
    }
}

?>
