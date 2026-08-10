<?php include('session.php'); ?>
<?php include('head.php'); ?>
<?php require_once 'sentiment_classifier.php'; ?>
<?php require_once 'preprocessing.php'; ?>

<?php
// Filter params
$selected_sentiment = isset($_GET['sentiment']) ? $_GET['sentiment'] : 'all';
$word_limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
if ($word_limit <= 0) $word_limit = 100;

// Fetch datasets
$whereClause = "";
if (in_array($selected_sentiment, ['positif', 'negatif', 'netral'])) {
    $whereClause = "WHERE sentiment = '$selected_sentiment'";
}

$query = "SELECT text, sentiment FROM datasets $whereClause";
$result = $conn->query($query);

// Aggregators
$allWordCounts = [];
$posWordCounts = [];
$negWordCounts = [];
$neuWordCounts = [];
$bigramCounts = [];

$totalTokens = 0;
$totalReviews = 0;

$classifier = new SentimentClassifier($conn);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalReviews++;
        $text = $row['text'];
        $sentiment = strtolower($row['sentiment']);

        // Preprocess
        $cleaned = cleaningText($text);
        $folded = casefoldingText($cleaned);
        $tokens = tokenizingText($folded);
        $filteredTokens = filteringText($tokens);

        $processedWords = [];
        foreach ($filteredTokens as $t) {
            // Apply simple stemming
            $stemmed = simpleStemming($t);
            if (mb_strlen($stemmed) < 2) continue; // skip single letters
            
            $processedWords[] = $stemmed;
            $totalTokens++;

            // Count for main list
            if (!isset($allWordCounts[$stemmed])) {
                $allWordCounts[$stemmed] = 0;
            }
            $allWordCounts[$stemmed]++;

            // Count per sentiment
            if ($sentiment == 'positif') {
                if (!isset($posWordCounts[$stemmed])) $posWordCounts[$stemmed] = 0;
                $posWordCounts[$stemmed]++;
            } elseif ($sentiment == 'negatif') {
                if (!isset($negWordCounts[$stemmed])) $negWordCounts[$stemmed] = 0;
                $negWordCounts[$stemmed]++;
            } elseif ($sentiment == 'netral') {
                if (!isset($neuWordCounts[$stemmed])) $neuWordCounts[$stemmed] = 0;
                $neuWordCounts[$stemmed]++;
            }
        }

        // Count Bigrams (2 consecutive words)
        $count = count($processedWords);
        for ($i = 0; $i < $count - 1; $i++) {
            $bigram = $processedWords[$i] . " " . $processedWords[$i + 1];
            if (!isset($bigramCounts[$bigram])) {
                $bigramCounts[$bigram] = 0;
            }
            $bigramCounts[$bigram]++;
        }
    }
}

// Sort word counts descending
arsort($allWordCounts);
arsort($posWordCounts);
arsort($negWordCounts);
arsort($neuWordCounts);
arsort($bigramCounts);

// Prepare data array for charts
$topWords = array_slice($allWordCounts, 0, $word_limit, true);
$top20Words = array_slice($allWordCounts, 0, 20, true);
$topPosWords = array_slice($posWordCounts, 0, 40, true);
$topNegWords = array_slice($negWordCounts, 0, 40, true);
$topBigrams = array_slice($bigramCounts, 0, 10, true);

// Format for JS WordCloud
$wordCloudData = [];
foreach ($topWords as $word => $count) {
    // Determine color scheme based on sentiment frequency or sentiment lexicon
    $posScore = isset($posWordCounts[$word]) ? $posWordCounts[$word] : 0;
    $negScore = isset($negWordCounts[$word]) ? $negWordCounts[$word] : 0;
    
    $wordCloudData[] = [
        'name' => $word,
        'value' => $count,
        'pos_count' => $posScore,
        'neg_count' => $negScore
    ];
}

$posWordCloudData = [];
foreach ($topPosWords as $word => $count) {
    $posWordCloudData[] = ['name' => $word, 'value' => $count];
}

$negWordCloudData = [];
foreach ($topNegWords as $word => $count) {
    $negWordCloudData[] = ['name' => $word, 'value' => $count];
}

// Unique vocabulary count
$uniqueVocab = count($allWordCounts);
$topPosWordName = !empty($posWordCounts) ? array_key_first($posWordCounts) : '-';
$topNegWordName = !empty($negWordCounts) ? array_key_first($negWordCounts) : '-';
?>

<body>
    <!-- Sidebar -->
    <?php include('side_bar.php'); ?>

    <main id="main" class="main">
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="pagetitle">
                        <h1>Visualisasi Dataset & Word Cloud</h1>
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="visualisasi.php">Visualisasi</a></li>
                                <li class="breadcrumb-item active">Word Cloud & Text</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card mb-4">
                <div class="card-body pt-3">
                    <form method="GET" action="wordcloud.php" class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label for="sentimentFilter" class="form-label font-weight-bold">Filter Sentimen</label>
                            <select id="sentimentFilter" name="sentiment" class="form-select" onchange="this.form.submit()">
                                <option value="all" <?= $selected_sentiment == 'all' ? 'selected' : ''; ?>>Semua Sentimen</option>
                                <option value="positif" <?= $selected_sentiment == 'positif' ? 'selected' : ''; ?>>Sentimen Positif</option>
                                <option value="negatif" <?= $selected_sentiment == 'negatif' ? 'selected' : ''; ?>>Sentimen Negatif</option>
                                <option value="netral" <?= $selected_sentiment == 'netral' ? 'selected' : ''; ?>>Sentimen Netral</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="limitFilter" class="form-label font-weight-bold">Batas Kata Word Cloud</label>
                            <select id="limitFilter" name="limit" class="form-select" onchange="this.form.submit()">
                                <option value="50" <?= $word_limit == 50 ? 'selected' : ''; ?>>Top 50 Kata</option>
                                <option value="100" <?= $word_limit == 100 ? 'selected' : ''; ?>>Top 100 Kata</option>
                                <option value="150" <?= $word_limit == 150 ? 'selected' : ''; ?>>Top 150 Kata</option>
                                <option value="200" <?= $word_limit == 200 ? 'selected' : ''; ?>>Top 200 Kata</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <a href="wordcloud.php" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Token Kata</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary text-white">
                                    <i class="bi bi-file-earmark-word"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= number_format($totalTokens); ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Kata terproses</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Kosa Kata Unik</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info text-white">
                                    <i class="bi bi-spellcheck"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= number_format($uniqueVocab); ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Unique vocabulary</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Kata Positif Utama</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success text-white">
                                    <i class="bi bi-hand-thumbs-up-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="text-success"><?= htmlspecialchars($topPosWordName); ?></h6>
                                    <span class="text-muted small pt-2 ps-1">
                                        <?= isset($posWordCounts[$topPosWordName]) ? $posWordCounts[$topPosWordName] . 'x muncul' : '-'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Kata Negatif Utama</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger text-white">
                                    <i class="bi bi-hand-thumbs-down-fill"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 class="text-danger"><?= htmlspecialchars($topNegWordName); ?></h6>
                                    <span class="text-muted small pt-2 ps-1">
                                        <?= isset($negWordCounts[$topNegWordName]) ? $negWordCounts[$topNegWordName] . 'x muncul' : '-'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            

            <!-- Visualisasi Gambar Word Cloud (Hasil Python Notebook) -->
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-file-image text-primary"></i> Benchmark Visual Word Cloud (Hasil Eksperimen Python Notebook)</h5>
                            <p class="text-muted small">Perbandingan visual gambar Word Cloud yang diproses melalui library Python <code>wordcloud</code> dan <code>matplotlib</code> dari notebook Google Colab.</p>

                            <div class="row g-4 mt-1">
                                <div class="col-md-4">
                                    <div class="card h-100 border text-center shadow-sm">
                                        <div class="card-header bg-success text-white py-2 font-weight-bold">
                                            Word Cloud Positif
                                        </div>
                                        <div class="card-body p-2">
                                            <img src="assets/img/wordcloud untuk sentimen positif.png" class="img-fluid rounded" alt="Wordcloud Positif Python" style="max-height: 220px; object-fit: contain;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card h-100 border text-center shadow-sm">
                                        <div class="card-header bg-danger text-white py-2 font-weight-bold">
                                            Word Cloud Negatif
                                        </div>
                                        <div class="card-body p-2">
                                            <img src="assets/img/wordcloud untuk sentimen negatif.png" class="img-fluid rounded" alt="Wordcloud Negatif Python" style="max-height: 220px; object-fit: contain;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card h-100 border text-center shadow-sm">
                                        <div class="card-header bg-secondary text-white py-2 font-weight-bold">
                                            Word Cloud Netral
                                        </div>
                                        <div class="card-body p-2">
                                            <img src="assets/img/wordcloud untuk sentimen netral.png" class="img-fluid rounded" alt="Wordcloud Netral Python" style="max-height: 220px; object-fit: contain;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Evaluasi Model & Confusion Matrix (Naive Bayes + SMOTE) -->
            <div class="row mt-4 mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0"><i class="bi bi-cpu-fill text-primary"></i> Perbandingan & Evaluasi Model (Naive Bayes + SMOTE)</h5>
                                <span class="badge bg-success fs-6"><i class="bi bi-check-circle-fill"></i> Akurasi Model: 77.25%</span>
                            </div>
                            <p class="text-muted">
                                Evaluasi performa pengujian data menggunakan algoritma <strong>Multinomial Naive Bayes</strong> yang dikombinasikan dengan teknik pembobotan <strong>TF-IDF</strong> dan penyeimbangan kelas <strong>SMOTE (Synthetic Minority Over-sampling Technique)</strong> pada perbandingan data latih (80%) dan data uji (20%).
                            </p>

                            <div class="row g-4 mt-1">
                                <!-- Confusion Matrix Image Card -->
                                <div class="col-lg-5">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light font-weight-bold">
                                            <i class="bi bi-grid-3x3-gap-fill text-info"></i> Confusion Matrix Heatmap
                                        </div>
                                        <div class="card-body text-center p-3">
                                            <img src="assets/img/confussion-matrix.png" class="img-fluid rounded border shadow-sm mb-2" alt="Confusion Matrix Naive Bayes SMOTE" style="max-height: 320px; cursor: pointer;" onclick="openImageModal(this.src, 'Confusion Matrix - Naive Bayes (SMOTE)')">
                                            <div class="alert alert-info py-2 px-3 mt-2 text-start small mb-0">
                                                <strong>Catatan Matrix:</strong><br>
                                                - Prediksi Negatif (0): 384 Aktual Negatif tepat terprediksi.<br>
                                                - Prediksi Positif (2): 153 Aktual Positif tepat terprediksi.<br>
                                                - Total Data Testing: 699 Dokumen Review.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evaluation Metrics & Table -->
                                <div class="col-lg-7">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light font-weight-bold">
                                            <i class="bi bi-journal-check text-primary"></i> Classification Report (Laporan Evaluasi Performa)
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle text-center mb-3">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>Kelas Sentimen</th>
                                                            <th>Precision</th>
                                                            <th>Recall</th>
                                                            <th>F1-Score</th>
                                                            <th>Support</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-start"><strong>0 (Negatif)</strong></td>
                                                            <td><span class="badge bg-success">88.5%</span></td>
                                                            <td><span class="badge bg-success">84.6%</span></td>
                                                            <td><strong class="text-success">86.5%</strong></td>
                                                            <td>454</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start"><strong>1 (Netral)</strong></td>
                                                            <td><span class="badge bg-warning text-dark">10.0%</span></td>
                                                            <td><span class="badge bg-warning text-dark">6.1%</span></td>
                                                            <td><strong class="text-warning text-dark">7.6%</strong></td>
                                                            <td>49</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start"><strong>2 (Positif)</strong></td>
                                                            <td><span class="badge bg-info text-dark">65.1%</span></td>
                                                            <td><span class="badge bg-info text-dark">78.1%</span></td>
                                                            <td><strong class="text-info text-dark">71.0%</strong></td>
                                                            <td>196</td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr>
                                                            <th class="text-start">Akurasi Keseluruhan</th>
                                                            <th colspan="3"><span class="badge bg-primary fs-6">77.25% (0.773)</span></th>
                                                            <th>699</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-start">Macro Average</th>
                                                            <td>54.5%</td>
                                                            <td>56.3%</td>
                                                            <td>55.0%</td>
                                                            <td>699</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-start">Weighted Average</th>
                                                            <td>76.4%</td>
                                                            <td>77.3%</td>
                                                            <td>76.6%</td>
                                                            <td>699</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            <div class="bg-light p-3 rounded border">
                                                <h6 class="font-weight-bold text-dark mb-2"><i class="bi bi-chat-left-text-fill text-primary me-1"></i> Rangkuman Hasil Evaluasi Python:</h6>
                                                <ul class="small mb-0 ps-3">
                                                    <li><strong>Dominasi Sentimen Negatif:</strong> Dari total 3.492 ulasan, sentimen negatif mendominasi (65% / 2.268 ulasan), disusul positif (28% / 980 ulasan) dan netral (7% / 244 ulasan).</li>
                                                    <li><strong>Keunggulan Naive Bayes + SMOTE:</strong> Model berkinerja sangat tinggi dalam mengidentifikasi ulasan <em>Negatif</em> (F1-Score 86.5%) dan ulasan <em>Positif</em> (F1-Score 71.0%).</li>
                                                    <li><strong>Catatan Kelas Netral:</strong> Jumlah sampel netral yang relatif kecil membuat F1-Score kelas netral rendah, namun SMOTE berhasil mencegah bias ekstrem terhadap kelas mayoritas.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Charts: Top 20 Words & Top Bigrams -->
            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Top 20 Kata Paling Sering Muncul</h5>
                            <div id="topWordsBarChart" style="min-height: 400px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Top 10 Frasa 2 Kata (Bigram)</h5>
                            <div id="topBigramChart" style="min-height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Words Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Daftar Frekuensi Kata Dataset</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle datatable" id="wordsTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kata / Token</th>
                                            <th>Frekuensi Total</th>
                                            <th>Kemunculan Positif</th>
                                            <th>Kemunculan Negatif</th>
                                            <th>Kemunculan Netral</th>
                                            <th>Dominasi Sentimen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        foreach ($allWordCounts as $word => $count) {
                                            if ($no > 300) break; // Limit table rows for high performance
                                            $posC = isset($posWordCounts[$word]) ? $posWordCounts[$word] : 0;
                                            $negC = isset($negWordCounts[$word]) ? $negWordCounts[$word] : 0;
                                            $neuC = isset($neuWordCounts[$word]) ? $neuWordCounts[$word] : 0;

                                            $dominance = "<span class='badge bg-secondary'>Netral / Imbang</span>";
                                            if ($posC > $negC && $posC > $neuC) {
                                                $dominance = "<span class='badge bg-success'>Positif</span>";
                                            } elseif ($negC > $posC && $negC > $neuC) {
                                                $dominance = "<span class='badge bg-danger'>Negatif</span>";
                                            }
                                        ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><strong><?= htmlspecialchars($word); ?></strong></td>
                                                <td><span class="badge bg-primary rounded-pill"><?= $count; ?></span></td>
                                                <td class="text-success font-weight-bold"><?= $posC; ?></td>
                                                <td class="text-danger font-weight-bold"><?= $negC; ?></td>
                                                <td class="text-secondary"><?= $neuC; ?></td>
                                                <td><?= $dominance; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main><!-- End #main -->

    <?php include('footer.php'); ?>
    <?php include('script.php'); ?>

    <!-- ECharts & ECharts WordCloud extension -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts-wordcloud@2.1.0/dist/echarts-wordcloud.min.js"></script>

    <script>
        // Data from PHP
        var mainCloudData = <?= json_encode($wordCloudData); ?>;
        var posCloudData = <?= json_encode($posWordCloudData); ?>;
        var negCloudData = <?= json_encode($negWordCloudData); ?>;

        var top20WordsData = <?= json_encode(array_reverse($top20Words, true)); ?>;
        var topBigramsData = <?= json_encode(array_reverse($topBigrams, true)); ?>;

        // Toggle View Function
        function toggleCloudView(type) {
            if (type === 'echarts') {
                document.getElementById('wordCloudChart').style.display = 'block';
                document.getElementById('tagCloudView').style.display = 'none';
            } else {
                document.getElementById('wordCloudChart').style.display = 'none';
                document.getElementById('tagCloudView').style.display = 'block';
            }
        }

        // Initialize Main Word Cloud
        function initWordCloud(elementId, wordData, defaultColor) {
            var chartDom = document.getElementById(elementId);
            if (!chartDom) return null;
            
            var chart = echarts.init(chartDom);
            
            var option = {
                tooltip: {
                    show: true,
                    formatter: function(params) {
                        return '<b>' + params.name + '</b>: ' + params.value + ' kali muncul';
                    }
                },
                series: [{
                    type: 'wordCloud',
                    shape: 'circle',
                    keepAspect: false,
                    left: 'center',
                    top: 'center',
                    width: '95%',
                    height: '95%',
                    right: null,
                    bottom: null,
                    sizeRange: [14, 60],
                    rotationRange: [-45, 90],
                    rotationStep: 45,
                    gridSize: 8,
                    drawOutOfBound: false,
                    layoutAnimation: true,
                    textStyle: {
                        fontFamily: 'Nunito, sans-serif',
                        fontWeight: 'bold',
                        color: function (params) {
                            if (defaultColor === 'positive') {
                                var greens = ['#198754', '#20c997', '#0f5132', '#146c43', '#00a86b'];
                                return greens[Math.floor(Math.random() * greens.length)];
                            } else if (defaultColor === 'negative') {
                                var reds = ['#dc3545', '#b02a37', '#842029', '#e35d6a', '#ff4d4d'];
                                return reds[Math.floor(Math.random() * reds.length)];
                            } else {
                                var colors = ['#4154f1', '#2be6a5', '#012970', '#899bbd', '#ff771d', '#198754', '#dc3545'];
                                return colors[Math.floor(Math.random() * colors.length)];
                            }
                        }
                    },
                    emphasis: {
                        focus: 'self',
                        textStyle: {
                            shadowBlur: 10,
                            shadowColor: '#333'
                        }
                    },
                    data: wordData
                }]
            };

            try {
                chart.setOption(option);
            } catch(err) {
                console.warn("WordCloud ECharts extension error, falling back to tag cloud view:", err);
                toggleCloudView('pill');
            }

            return chart;
        }

        // Initialize All Word Clouds
        var mainChart = initWordCloud('wordCloudChart', mainCloudData, 'mixed');
        var posChart = initWordCloud('posWordCloudChart', posCloudData, 'positive');
        var negChart = initWordCloud('negWordCloudChart', negCloudData, 'negative');

        // Top 20 Words Bar Chart
        var topWordsDom = document.getElementById('topWordsBarChart');
        if (topWordsDom) {
            var topWordsChart = echarts.init(topWordsDom);
            var categories = Object.keys(top20WordsData);
            var values = Object.values(top20WordsData);

            var topWordsOption = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value',
                    boundaryGap: [0, 0.01]
                },
                yAxis: {
                    type: 'category',
                    data: categories
                },
                series: [{
                    name: 'Frekuensi',
                    type: 'bar',
                    data: values,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [
                            { offset: 0, color: '#4154f1' },
                            { offset: 1, color: '#717ff5' }
                        ]),
                        borderRadius: [0, 5, 5, 0]
                    }
                }]
            };
            topWordsChart.setOption(topWordsOption);
        }

        // Top 10 Bigrams Bar Chart
        var bigramDom = document.getElementById('topBigramChart');
        if (bigramDom) {
            var bigramChart = echarts.init(bigramDom);
            var bgCategories = Object.keys(topBigramsData);
            var bgValues = Object.values(topBigramsData);

            var bigramOption = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value'
                },
                yAxis: {
                    type: 'category',
                    data: bgCategories
                },
                series: [{
                    name: 'Frekuensi Frasa',
                    type: 'bar',
                    data: bgValues,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [
                            { offset: 0, color: '#ff771d' },
                            { offset: 1, color: '#ffb300' }
                        ]),
                        borderRadius: [0, 5, 5, 0]
                    }
                }]
            };
            bigramChart.setOption(bigramOption);
        }

        // Window resize event handler
        window.addEventListener('resize', function() {
            if (mainChart) mainChart.resize();
            if (posChart) posChart.resize();
            if (negChart) negChart.resize();
            if (topWordsChart) topWordsChart.resize();
            if (bigramChart) bigramChart.resize();
        });
    </script>
</body>
</html>
