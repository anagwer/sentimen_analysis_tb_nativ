<?php include('session.php'); ?>
<?php include('head.php'); ?>
<?php require_once 'sentiment_classifier.php'; ?>

<?php
$prediction_result = null;
$detailed_result = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['predict'])) {
    $userInput = $_POST['user_input'];
    
    if (!empty($userInput)) {
        $classifier = new SentimentClassifier($conn);
        $prediction_result = $classifier->predict($userInput);
        $detailed_result = $classifier->predictDetailed($userInput);
        
        // Save to database
        if ($conn) {
            $text = $conn->real_escape_string($userInput);
            $sentiment = $prediction_result['sentiment'];
            $score = $prediction_result['score'];
            $confidence = $prediction_result['confidence'];
            
            $query = "INSERT INTO sentiment_predictions (user_input, sentiment_result, score, confidence) 
                     VALUES ('$text', '$sentiment', $score, $confidence)";
            $conn->query($query);
        }
    }
}
?>

<body>
    <!-- Sidebar -->
    <?php include('side_bar.php'); ?>

    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="pagetitle">
                        <h1>Prediksi Sentimen</h1>
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Prediksi Sentimen</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Prediction Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Masukkan Teks untuk Analisis Sentimen</h5>
                            <form method="POST" class="form-floating">
                                <div class="form-floating mb-3">
                                    <textarea 
                                        class="form-control" 
                                        placeholder="Masukkan teks/review yang ingin dianalisis..." 
                                        id="user_input" 
                                        name="user_input" 
                                        style="height: 200px"
                                        required><?php echo isset($_POST['user_input']) ? htmlspecialchars($_POST['user_input']) : ''; ?></textarea>
                                    <label for="user_input">Review Text</label>
                                </div>
                                <button type="submit" name="predict" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Analisis Sentimen
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tips Penggunaan</h5>
                            <p><strong>Sentimen Positif:</strong><br>
                            Gunakan untuk teks yang mengandung kata-kata seperti "bagus", "enak", "suka", "mantap", dll.</p>
                            
                            <p><strong>Sentimen Negatif:</strong><br>
                            Gunakan untuk teks yang mengandung kata-kata seperti "buruk", "jelek", "benci", "kecewa", dll.</p>
                            
                            <p><strong>Sentimen Netral:</strong><br>
                            Teks yang tidak menunjukkan sentimen positif atau negatif yang jelas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <?php if ($prediction_result): ?>
            <div class="row mt-4">
                <!-- Main Result -->
                <div class="col-lg-8">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h5 class="card-title">Hasil Analisis</h5>
                            
                            <!-- Sentiment Badge -->
                            <div class="mb-4">
                                <p><strong>Teks:</strong></p>
                                <p class="text-muted"><?= htmlspecialchars($prediction_result['text']); ?></p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Sentiment:</strong></p>
                                    <?php 
                                    $sentiment = $prediction_result['sentiment'];
                                    if ($sentiment == 'positif') {
                                        echo '<span class="badge bg-success" style="font-size: 16px; padding: 10px 20px;">✓ POSITIF</span>';
                                    } elseif ($sentiment == 'negatif') {
                                        echo '<span class="badge bg-danger" style="font-size: 16px; padding: 10px 20px;">✗ NEGATIF</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary" style="font-size: 16px; padding: 10px 20px;">— NETRAL</span>';
                                    }
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Skor:</strong></p>
                                    <h6><?= $prediction_result['score']; ?></h6>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Confidence:</strong></p>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: <?= $prediction_result['confidence']; ?>%;" 
                                             aria-valuenow="<?= $prediction_result['confidence']; ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= $prediction_result['confidence']; ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confidence -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Informasi Confidence</h5>
                            <p>Confidence score menunjukkan seberapa yakin sistem dalam melakukan prediksi.</p>
                            <div class="d-grid">
                                <div class="alert alert-info">
                                    <strong><?= $prediction_result['confidence']; ?>%</strong> confidence
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analysis -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Analisis Detail</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Positive Score: <span class="badge bg-success"><?= $detailed_result['positive_score']; ?></span></h6>
                                    <?php if (count($detailed_result['positive_words']) > 0): ?>
                                        <p><strong>Kata Positif yang Ditemukan:</strong></p>
                                        <div class="mb-3">
                                            <?php foreach ($detailed_result['positive_words'] as $word => $weight): ?>
                                                <span class="badge bg-success me-2 mb-2"><?= $word; ?> (+<?= $weight; ?>)</span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted"><em>Tidak ada kata positif yang ditemukan</em></p>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6">
                                    <h6>Negative Score: <span class="badge bg-danger"><?= $detailed_result['negative_score']; ?></span></h6>
                                    <?php if (count($detailed_result['negative_words']) > 0): ?>
                                        <p><strong>Kata Negatif yang Ditemukan:</strong></p>
                                        <div class="mb-3">
                                            <?php foreach ($detailed_result['negative_words'] as $word => $weight): ?>
                                                <span class="badge bg-danger me-2 mb-2"><?= $word; ?> (-<?= $weight; ?>)</span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted"><em>Tidak ada kata negatif yang ditemukan</em></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr>
                            <p><strong>Total Skor:</strong> <span class="h5"><?= $detailed_result['total_score']; ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </section>
    </main><!-- End #main -->

    <?php include('footer.php'); ?>
    <?php include('script.php'); ?>
</body>

</html>
