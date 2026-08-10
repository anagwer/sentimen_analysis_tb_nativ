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

            <!-- Main Word Cloud Card -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-cloud-haze2 text-primary"></i> 
                                    Awan Kata (Word Cloud) Dataset 
                                    <span class="badge bg-primary ms-2"><?= count($wordCloudData); ?> Kata</span>
                                </h5>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="toggleCloudView('echarts')">
                                        <i class="bi bi-graph-up"></i> ECharts View
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleCloudView('pill')">
                                        <i class="bi bi-grid"></i> Tag Cloud View
                                    </button>
                                </div>
                            </div>
                            <hr>

                            <!-- Container for ECharts WordCloud -->
                            <div id="wordCloudChart" style="min-height: 480px; width: 100%;"></div>

                            <!-- Fallback/Alternative Tag Cloud View -->
                            <div id="tagCloudView" style="display: none; min-height: 400px; padding: 20px;" class="border rounded bg-light text-center">
                                <?php
                                if (!empty($wordCloudData)) {
                                    $maxVal = reset($allWordCounts);
                                    $minVal = end($allWordCounts);
                                    if ($maxVal == $minVal) $maxVal = $minVal + 1;

                                    foreach ($wordCloudData as $item) {
                                        $word = htmlspecialchars($item['name']);
                                        $cnt = $item['value'];
                                        // Font scale between 14px and 42px
                                        $size = 14 + (($cnt - $minVal) / ($maxVal - $minVal)) * 28;
                                        
                                        // Color logic
                                        $posC = $item['pos_count'];
                                        $negC = $item['neg_count'];
                                        $badgeClass = 'bg-secondary';
                                        if ($posC > $negC) {
                                            $badgeClass = 'bg-success';
                                        } elseif ($negC > $posC) {
                                            $badgeClass = 'bg-danger';
                                        }

                                        echo "<span class='badge $badgeClass m-1 p-2 shadow-sm' style='font-size: {$size}px; display: inline-block;' title='{$word}: {$cnt}x muncul'>
                                                {$word} <span class='badge bg-light text-dark rounded-pill ms-1' style='font-size:11px;'>{$cnt}</span>
                                              </span> ";
                                    }
                                } else {
                                    echo "<p class='text-muted'>Tidak ada data kata untuk ditampilkan.</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side-by-side Comparative Word Clouds (Positif vs Negatif) -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card border-top border-success border-4">
                        <div class="card-body">
                            <h5 class="card-title text-success">
                                <i class="bi bi-hand-thumbs-up"></i> Word Cloud Sentimen Positif
                            </h5>
                            <div id="posWordCloudChart" style="min-height: 380px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-top border-danger border-4">
                        <div class="card-body">
                            <h5 class="card-title text-danger">
                                <i class="bi bi-hand-thumbs-down"></i> Word Cloud Sentimen Negatif
                            </h5>
                            <div id="negWordCloudChart" style="min-height: 380px; width: 100%;"></div>
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
