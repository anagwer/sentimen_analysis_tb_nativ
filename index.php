<?php 
include('session.php'); 
include('head.php');
require_once 'sentiment_classifier.php';
require_once 'dbcon.php';

// Initialize sentiment classifier
$classifier = new SentimentClassifier($conn);
$distribution = $classifier->getDatasetDistribution();
$stats = $classifier->getLexiconStats();

// Get total predictions
$totalDatasets = $distribution['total'] ?? 0;
$totalPredictions = 0;
if ($conn) {
    $query = "SELECT COUNT(*) as total FROM sentiment_predictions";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $totalPredictions = $row['total'];
    }
}
?>

<body>
    <!-- Sidebar -->
    <?php include('side_bar.php'); ?>

    <main id="main" class="main">
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="mb-4">Dashboard Analisis Sentimen</h4>
                </div>
            </div>

            <div class="row">
                <!-- Total Dataset -->
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Dataset</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-database"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $totalDatasets; ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Review</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sentimen Positif -->
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Sentimen Positif</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-hand-thumbs-up"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $distribution['positif']['count']; ?></h6>
                                    <span class="text-muted small pt-2 ps-1"><?= $distribution['positif']['percentage']; ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sentimen Negatif -->
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Sentimen Negatif</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-hand-thumbs-down"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $distribution['negatif']['count']; ?></h6>
                                    <span class="text-muted small pt-2 ps-1"><?= $distribution['negatif']['percentage']; ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sentimen Netral -->
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Sentimen Netral</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-dash-circle"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $distribution['netral']['count']; ?></h6>
                                    <span class="text-muted small pt-2 ps-1"><?= $distribution['netral']['percentage']; ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row of Cards -->
            <div class="row mt-3">
                <!-- Total Prediksi -->
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Prediksi</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-search"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $totalPredictions; ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Prediksi User</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lexicon Stats -->
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <h5 class="card-title">Kata Positif</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $stats['positive_words_count']; ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Lexicon</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Negative Words -->
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <h5 class="card-title">Kata Negatif</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $stats['negative_words_count']; ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Lexicon</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Words -->
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Kata</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-database-check"></i>                                </div>
                                <div class="ps-3">
                                    <h6><?= $stats['total_words']; ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Lexicon</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tentang Analisis Sentimen</h5>
                            <p>
                                Aplikasi ini menggunakan metode <strong>Lexicon-Based Sentiment Analysis</strong> untuk mengklasifikasikan sentimen dari teks.
                                Sistem ini dapat mengenali apakah sebuah ulasan atau komentar memiliki sentimen <strong>positif</strong>, <strong>negatif</strong>, atau <strong>netral</strong>.
                            </p>
                            <p>
                                <strong>Fitur Utama:</strong><br>
                                - Upload dan analisis dataset review dalam bentuk tabel<br>
                                - Visualisasi distribusi sentimen dengan chart<br>
                                - Prediksi sentimen untuk teks baru secara real-time<br>
                                - Riwayat lengkap semua prediksi yang telah dilakukan
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main><!-- End #main -->

    <?php include('footer.php'); ?>
    <?php include('script.php'); ?>
</body>

</html>
