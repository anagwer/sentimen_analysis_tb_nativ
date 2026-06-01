<?php
/**
 * Text Preprocessing Library untuk Sentiment Analysis
 * Mengkonversi dari Python preprocessing ke PHP
 */

/**
 * 1. Membersihkan teks dari mention, hashtag, URL, angka, tanda baca
 */
function cleaningText($text) {
    if (!is_string($text) || empty($text)) {
        return "";
    }
    
    // Hapus mention (@username)
    $text = preg_replace('/@[A-Za-z0-9_]+/', '', $text);
    
    // Hapus hashtag (#hashtag)
    $text = preg_replace('/#[A-Za-z0-9_]+/', '', $text);
    
    // Hapus RT (Retweet)
    $text = preg_replace('/RT[\s]/', '', $text);
    
    // Hapus URL/link
    $text = preg_replace('/http\S+/', '', $text);
    
    // Hapus angka
    $text = preg_replace('/[0-9]+/', '', $text);
    
    // Hapus tanda baca khusus (tapi pertahankan spasi)
    $text = preg_replace('/[^\w\s]/u', '', $text);
    
    // Ganti newline dengan spasi
    $text = str_replace(["\n", "\r"], ' ', $text);
    
    // Hapus spasi berlebih
    $text = preg_replace('/\s+/', ' ', $text);
    
    // Trim spasi di awal dan akhir
    return trim($text);
}

/**
 * 2. Case folding - konversi semua huruf menjadi lowercase
 */
function casefoldingText($text) {
    if (!is_string($text)) {
        return "";
    }
    return strtolower($text);
}

/**
 * 3. Tokenisasi - pisahkan teks menjadi kata-kata individual
 */
function tokenizingText($text) {
    if (!is_string($text) || empty($text)) {
        return [];
    }
    
    // Split by whitespace
    $tokens = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
    return array_filter($tokens);
}

/**
 * 4. Filtering stopwords - hapus kata-kata yang tidak penting
 */
function filteringText($tokens) {
    if (!is_array($tokens)) {
        return [];
    }
    
    // Stopwords Bahasa Indonesia yang umum
    $stopwords = [
        'a', 'about', 'above', 'after', 'again', 'against', 'all', 'am', 'an', 'and', 'any', 'are', 
        'as', 'at', 'be', 'because', 'been', 'before', 'being', 'below', 'between', 'both', 'but', 
        'by', 'can', 'could', 'did', 'do', 'does', 'doing', 'down', 'during', 'each', 'few', 'for', 
        'from', 'further', 'had', 'has', 'have', 'having', 'he', 'her', 'here', 'hers', 'herself', 
        'him', 'himself', 'his', 'how', 'i', 'if', 'in', 'into', 'is', 'it', 'its', 'itself', 'just', 
        'me', 'might', 'more', 'most', 'must', 'my', 'myself', 'no', 'nor', 'not', 'of', 'off', 'on', 
        'only', 'or', 'other', 'our', 'ours', 'ourselves', 'out', 'over', 'own', 'same', 'so', 'some', 
        'such', 'than', 'that', 'the', 'their', 'theirs', 'them', 'themselves', 'then', 'there', 
        'these', 'they', 'this', 'those', 'to', 'too', 'under', 'until', 'up', 'very', 'was', 'we', 
        'were', 'what', 'when', 'where', 'which', 'while', 'who', 'whom', 'why', 'will', 'with', 
        'you', 'your', 'yours', 'yourself', 'yourselves',
        
        // Stopwords Bahasa Indonesia
        'dan', 'di', 'ke', 'dari', 'yang', 'untuk', 'dengan', 'adalah', 'atau', 'pada', 'ini', 'telah',
        'akan', 'ada', 'dapat', 'juga', 'lebih', 'besar', 'kecil', 'sama', 'tidak', 'sudah', 'harus',
        'kalau', 'jika', 'supaya', 'agar', 'saat', 'ketika', 'setelah', 'sebelum', 'maka', 'meski',
        'walaupun', 'ialah', 'yaitu', 'karena', 'benar', 'salah', 'betul', 'ya', 'iya', 'tidak', 'nggak',
        'gak', 'nya', 'na', 'sih', 'lah', 'tuh', 'kok', 'kah', 'ku', 'ga', 'gaa', 'loh', 'woii', 'woi'
    ];
    
    $filtered = [];
    foreach ($tokens as $token) {
        $lower_token = strtolower($token);
        if (!in_array($lower_token, $stopwords) && !empty($lower_token)) {
            $filtered[] = $token;
        }
    }
    
    return $filtered;
}

/**
 * 5. Gabungkan kembali token menjadi kalimat
 */
function toSentence($tokens) {
    if (!is_array($tokens)) {
        return "";
    }
    return implode(' ', $tokens);
}

/**
 * 6. Stemming - ubah kata ke bentuk dasarnya (simplified version)
 * Menggunakan aturan sederhana untuk suffix/prefix Bahasa Indonesia
 */
function stemmingText($text) {
    if (!is_string($text) || empty($text)) {
        return "";
    }
    
    $words = tokenizingText($text);
    $stemmed = [];
    
    foreach ($words as $word) {
        $stemmed[] = simpleStemming($word);
    }
    
    return implode(' ', $stemmed);
}

/**
 * Helper untuk stemming sederhana
 */
function simpleStemming($word) {
    $word = strtolower($word);
    
    // Hapus suffix umum Bahasa Indonesia
    $patterns = [
        '/kan$/' => '',      // verb suffix
        '/kan$/' => '',      // transitive marker
        '/an$/' => '',       // noun suffix
        '/i$/' => '',        // possessive suffix
        '/lah$/' => '',      // emphasis marker
        '/kah$/' => '',      // question marker
        '/tah$/' => '',      // contraction
        '/nya$/' => '',      // possessive pronoun
        '/pun$/' => '',      // emphasis
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $word = preg_replace($pattern, $replacement, $word, 1);
    }
    
    // Hapus prefix umum Bahasa Indonesia
    $prefixes = [
        'me' => '',
        'mem' => '',
        'men' => '',
        'meng' => '',
        'meny' => '',
        'di' => '',
        'ter' => '',
        'ke' => '',
        'ber' => '',
        'bel' => '',
        'be' => '',
    ];
    
    foreach ($prefixes as $prefix => $replacement) {
        if (strpos($word, $prefix) === 0 && strlen($word) > strlen($prefix) + 2) {
            $word = substr($word, strlen($prefix));
            break;
        }
    }
    
    return $word;
}

/**
 * 7. Normalisasi slangwords / kata tidak baku
 */
function fixSlangwords($text, $slangDict = []) {
    if (!is_string($text) || empty($text)) {
        return "";
    }
    
    if (empty($slangDict)) {
        // Buat kamus slang default
        $slangDict = getDefaultSlangDictionary();
    }
    
    $words = tokenizingText($text);
    $fixed = [];
    
    foreach ($words as $word) {
        $lower = strtolower($word);
        if (isset($slangDict[$lower])) {
            $fixed[] = $slangDict[$lower];
        } else {
            $fixed[] = $word;
        }
    }
    
    return implode(' ', $fixed);
}

/**
 * Kamus slangwords default untuk Bahasa Indonesia
 */
function getDefaultSlangDictionary() {
    return [
        'gak' => 'tidak',
        'ga' => 'tidak',
        'gaa' => 'tidak',
        'nggak' => 'tidak',
        'ngga' => 'tidak',
        'g' => 'tidak',
        'gapapa' => 'tidak apa-apa',
        'gpp' => 'tidak apa-apa',
        'tp' => 'tapi',
        'tpi' => 'tapi',
        'utk' => 'untuk',
        'yg' => 'yang',
        'lg' => 'lagi',
        'sm' => 'sama',
        'dg' => 'dengan',
        'dgn' => 'dengan',
        'krn' => 'karena',
        'pls' => 'tolong',
        'plz' => 'tolong',
        'tks' => 'terima kasih',
        'mksh' => 'makasih',
        'maaf' => 'maaf',
        'ok' => 'baik',
        'okee' => 'baik',
        'pernah' => 'pernah',
        'udah' => 'sudah',
        'dah' => 'sudah',
        'uda' => 'sudah',
        'belum' => 'belum',
        'belom' => 'belum',
        'mau' => 'mau',
        'pengen' => 'ingin',
        'pgn' => 'ingin',
        'bnyk' => 'banyak',
        'byk' => 'banyak',
        'banget' => 'sangat',
        'banged' => 'sangat',
        'bgt' => 'sangat',
        'bgd' => 'sangat',
        'suka' => 'suka',
        'suka2' => 'sembarangan',
        'malas' => 'malas',
        'males' => 'malas',
        'capek' => 'capek',
        'cape' => 'capek',
        'asik' => 'asik',
        'asyik' => 'asik',
        'seru' => 'seru',
        'bosen' => 'bosan',
        'boring' => 'bosan',
        'enak' => 'enak',
        'enaknya' => 'enaknya',
        'gile' => 'gila',
        'gila2' => 'gila-gilaan',
        'keren' => 'keren',
        'cool' => 'keren',
        'jelek' => 'jelek',
        'jlek' => 'jelek',
        'buruk' => 'buruk',
        'bagus' => 'bagus',
        'baguss' => 'bagus',
        'mantap' => 'mantap',
        'mantabs' => 'mantap',
        'lumayan' => 'lumayan',
        'lumayanlah' => 'lumayan',
        'cuek' => 'cuek',
        'santai' => 'santai',
        'santay' => 'santai',
        'chill' => 'santai',
        'nyaman' => 'nyaman',
        'nyamn' => 'nyaman',
        'sedih' => 'sedih',
        'sedih2' => 'sangat sedih',
        'senang' => 'senang',
        'sange' => 'sengaja',
        'sengaja' => 'sengaja',
        'demen' => 'suka',
        'doyan' => 'suka',
        'hobi' => 'hobi',
        'fanatik' => 'fanatik',
        'fans' => 'penggemar',
        'cinta' => 'cinta',
        'cinte' => 'cinta',
        'sayang' => 'sayang',
        'sabar' => 'sabar',
        'sabarnya' => 'sabarnya',
        'tabah' => 'tabah',
        'kuat' => 'kuat',
        'lemah' => 'lemah',
        'jago' => 'ahli',
        'jagoan' => 'ahli',
        'pintar' => 'pintar',
        'pinter' => 'pintar',
        'bodoh' => 'bodoh',
        'tolol' => 'tolol',
        'cerdas' => 'cerdas',
        'pandai' => 'pandai',
        'dungu' => 'dungu',
        'bego' => 'bodoh',
        'goblok' => 'bodoh',
        'jenius' => 'jenius',
        'hebat' => 'hebat',
        'luar biasa' => 'luar biasa',
        'biasa' => 'biasa',
        'standar' => 'standar',
        'normal' => 'normal',
        'aneh' => 'aneh',
        'unik' => 'unik',
        'langka' => 'langka',
        'asing' => 'asing',
        'malu' => 'malu',
        'maluin' => 'memalukan',
        'hiya' => 'malu',
        'kesal' => 'kesal',
        'marah' => 'marah',
        'berang' => 'marah',
        'dendam' => 'dendam',
        'dengki' => 'dengki',
        'iri' => 'iri',
        'irihati' => 'iri hati',
        'benci' => 'benci',
        'bela' => 'bela',
        'bersahabat' => 'bersahabat',
        'baikan' => 'berdamai',
        'rukun' => 'rukun',
        'harmonis' => 'harmonis',
        'harmoni' => 'harmoni',
        'musik' => 'musik',
        'lagu' => 'lagu',
        'nyanyian' => 'nyanyian',
        'nyanyi' => 'menyanyi',
        'tembang' => 'lagu',
        'melodi' => 'melodi',
        'ritme' => 'ritme',
        'irama' => 'irama',
        'indah' => 'indah',
        'cantik' => 'cantik',
        'cantiq' => 'cantik',
        'ganteng' => 'ganteng',
        'tampan' => 'tampan',
        'manis' => 'manis',
        'maniss' => 'manis',
        'imut' => 'imut',
        'lucu' => 'lucu',
        'lucuu' => 'lucu',
        'jelek' => 'jelek',
        'buruk' => 'buruk',
        'cacat' => 'cacat',
        'lurus' => 'lurus',
        'bengkok' => 'bengkok',
        'bengkak' => 'bengkak',
        'bengel' => 'bengel',
        'pincang' => 'pincang',
        'tuli' => 'tuli',
        'bisu' => 'bisu',
        'buta' => 'buta',
        'lumpuh' => 'lumpuh',
        'sakit' => 'sakit',
        'sakitt' => 'sakit',
        'sakit2' => 'sakit-sakitan',
        'sehat' => 'sehat',
        'sehat2' => 'sehat',
        'fit' => 'sehat',
        'bugar' => 'bugar',
        'lemah' => 'lemah',
        'lemas' => 'lemas',
        'gerah' => 'gerah',
        'pusing' => 'pusing',
        'puyeng' => 'pusing',
        'mumet' => 'pusing',
        'migrain' => 'migren',
        'migrem' => 'migren',
        'demam' => 'demam',
        'panas' => 'panas',
        'dingin' => 'dingin',
        'pilek' => 'pilek',
        'batuk' => 'batuk',
        'bersin' => 'bersin',
        'atesmen' => 'asma',
        'asma' => 'asma',
        'napas' => 'napas',
        'nafas' => 'napas',
        'sesak' => 'sesak',
        'tersedak' => 'tersedak',
        'tersendak' => 'tersedak',
        'telan' => 'telan',
        'menelan' => 'menelan',
    ];
}

/**
 * Fungsi main untuk preprocessing teks - melakukan semua tahap sekaligus
 */
function preprocessText($text) {
    // 1. Cleaning
    $text = cleaningText($text);
    
    // 2. Case folding
    $text = casefoldingText($text);
    
    // 3. Normalisasi slangwords
    $slangDict = getDefaultSlangDictionary();
    $text = fixSlangwords($text, $slangDict);
    
    // 4. Tokenizing
    $tokens = tokenizingText($text);
    
    // 5. Filtering stopwords
    $tokens = filteringText($tokens);
    
    // 6. Gabung kembali
    $text = toSentence($tokens);
    
    // 7. Stemming
    $text = stemmingText($text);
    
    return $text;
}

?>
