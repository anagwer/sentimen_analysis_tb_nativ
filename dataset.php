<?php include('session.php'); ?>
<?php include('head.php'); ?>
<?php require_once 'sentiment_classifier.php'; ?>

<body>
    <!-- Sidebar -->
    <?php include('side_bar.php'); ?>

    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="pagetitle">
                        <h1>Dataset</h1>
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Dataset</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Upload CSV -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Upload Dataset CSV</h5>
                            <form method="POST" enctype="multipart/form-data" class="row g-3" id="uploadForm">
                                <div class="col-md-6">
                                    <label for="csvFile" class="form-label">Pilih File CSV</label>
                                    <input class="form-control" type="file" id="csvFile" name="csvFile" accept=".csv" required>
                                    <small class="form-text text-muted">Format: title, url, stars, name, reviewUrl, text</small>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" name="upload_csv" class="btn btn-primary" id="uploadBtn">
                                        <i class="bi bi-upload"></i> Upload CSV
                                    </button>
                                </div>
                            </form>

                            <?php
                            $error = '';
                            $success = '';
                            
                            if (isset($_POST['upload_csv'])) {
                                if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] != 0) {
                                    $error = "Error: File upload gagal. Pastikan file dipilih dengan benar.";
                                } else {
                                    $csvFile = $_FILES['csvFile']['tmp_name'];
                                    $fileName = $_FILES['csvFile']['name'];
                                    
                                    // Validasi file extension
                                    if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'csv') {
                                        $error = "Error: File harus berformat CSV.";
                                    } elseif ($_FILES['csvFile']['size'] > 10 * 1024 * 1024) { // 10MB limit
                                        $error = "Error: File terlalu besar (max 10MB).";
                                    } else {
                                        try {
                                            $classifier = new SentimentClassifier($conn);
                                            
                                            if (($handle = fopen($csvFile, 'r')) !== FALSE) {
                                                $rowNum = 0;
                                                $importedCount = 0;
                                                $skippedCount = 0;
                                                
                                                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                                                    // Skip header
                                                    if ($rowNum == 0) {
                                                        $rowNum++;
                                                        continue;
                                                    }
                                                    
                                                    if (count($data) >= 6) {
                                                        // Trim whitespace from all fields
                                                        $title = trim($conn->real_escape_string($data[0]));
                                                        $url = trim($conn->real_escape_string($data[1]));
                                                        $stars = (int)$data[2];
                                                        $name = trim($conn->real_escape_string($data[3]));
                                                        $reviewUrl = trim($conn->real_escape_string($data[4]));
                                                        $text = trim($conn->real_escape_string($data[5]));
                                                        
                                                        // Validate data
                                                        if (empty($title) || empty($text)) {
                                                            $skippedCount++;
                                                            continue;
                                                        }
                                                        
                                                        // Classify sentiment
                                                        $result = $classifier->classifySentiment($text);
                                                        $sentiment = $result['label'];
                                                        $score = $result['score'];
                                                        
                                                        // Insert ke database
                                                        $query = "INSERT INTO datasets (title, url, stars, name, reviewUrl, text, sentiment, score) 
                                                                 VALUES ('$title', '$url', $stars, '$name', '$reviewUrl', '$text', '$sentiment', $score)";
                                                        
                                                        if ($conn->query($query)) {
                                                            $importedCount++;
                                                        }
                                                    } else {
                                                        $skippedCount++;
                                                    }
                                                    $rowNum++;
                                                }
                                                fclose($handle);
                                                
                                                if ($importedCount > 0) {
                                                    $success = "<strong>Sukses!</strong> $importedCount data berhasil diimport dan dianalisis.";
                                                    if ($skippedCount > 0) {
                                                        $success .= " ($skippedCount baris dilewatkan karena data tidak lengkap)";
                                                    }
                                                } else {
                                                    $error = "Error: Tidak ada data valid untuk diimport.";
                                                }
                                            } else {
                                                $error = "Error: Tidak dapat membuka file CSV.";
                                            }
                                        } catch (Exception $e) {
                                            $error = "Error: " . $e->getMessage();
                                        }
                                    }
                                }
                            }
                            
                            // Display error or success message
                            if (!empty($error)) {
                                echo "<div class='alert alert-danger mt-3' role='alert'>$error</div>";
                            }
                            if (!empty($success)) {
                                echo "<div class='alert alert-success mt-3' role='alert'>$success</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dataset Table -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tabel Dataset</h5>
                            <div class="table-responsive">
                                <table class="table table-hover datatable" id="datasetTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Judul</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Review</th>
                                            <th scope="col">Rating</th>
                                            <th scope="col">Sentimen</th>
                                            <th scope="col">Skor</th>
                                            <th scope="col">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM datasets ORDER BY created_at DESC LIMIT 100";
                                        $result = $conn->query($query);
                                        
                                        if ($result->num_rows > 0) {
                                            $no = 1;
                                            while ($row = $result->fetch_assoc()) {
                                                $sentimentBadge = '';
                                                if ($row['sentiment'] == 'positif') {
                                                    $sentimentBadge = '<span class="badge bg-success">Positif</span>';
                                                } elseif ($row['sentiment'] == 'negatif') {
                                                    $sentimentBadge = '<span class="badge bg-danger">Negatif</span>';
                                                } else {
                                                    $sentimentBadge = '<span class="badge bg-secondary">Netral</span>';
                                                }
                                                
                                                $fullTitle = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
                                                $fullReview = htmlspecialchars($row['text'], ENT_QUOTES, 'UTF-8');
                                                
                                                echo "
                                                <tr>
                                                    <th scope='row'>" . $no++ . "</th>
                                                    <td><small>" . substr($row['title'], 0, 40) . "...</small></td>
                                                    <td>" . $row['name'] . "</td>
                                                    <td><small>" . substr($row['text'], 0, 50) . "...</small></td>
                                                    <td><span class='badge bg-primary'>" . $row['stars'] . " ⭐</span></td>
                                                    <td>" . $sentimentBadge . "</td>
                                                    <td><small>" . number_format($row['score'], 2) . "</small></td>
                                                    <td>
                                                        <button class='btn btn-sm btn-info detail-btn' 
                                                                data-title='" . $fullTitle . "' 
                                                                data-review='" . $fullReview . "' 
                                                                data-name='" . $row['name'] . "' 
                                                                data-rating='" . $row['stars'] . "' 
                                                                data-sentiment='" . $row['sentiment'] . "' 
                                                                data-score='" . number_format($row['score'], 2) . "' 
                                                                data-url='" . htmlspecialchars($row['url'], ENT_QUOTES, 'UTF-8') . "'
                                                                data-bs-toggle='modal' 
                                                                data-bs-target='#detailModal'>
                                                            <i class='bi bi-eye'></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                ";
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>Tidak ada data. Upload CSV terlebih dahulu.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main><!-- End #main -->

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Dataset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Judul:</label>
                            <p id="modalTitle" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Reviewer:</label>
                            <p id="modalName" class="form-control-plaintext"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Rating:</label>
                            <p id="modalRating" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sentimen:</label>
                            <p id="modalSentiment" class="form-control-plaintext"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Skor:</label>
                            <p id="modalScore" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Review:</label>
                            <div class="alert alert-light" style="max-height: 300px; overflow-y: auto;">
                                <p id="modalReview" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">URL:</label>
                            <p id="modalUrl" class="form-control-plaintext text-break"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script untuk Detail Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const detailButtons = document.querySelectorAll('.detail-btn');
            
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const title = this.getAttribute('data-title');
                    const review = this.getAttribute('data-review');
                    const name = this.getAttribute('data-name');
                    const rating = this.getAttribute('data-rating');
                    const sentiment = this.getAttribute('data-sentiment');
                    const score = this.getAttribute('data-score');
                    const url = this.getAttribute('data-url');
                    
                    // Set sentiment badge
                    let sentimentBadge = '';
                    if (sentiment === 'positif') {
                        sentimentBadge = '<span class="badge bg-success">Positif</span>';
                    } else if (sentiment === 'negatif') {
                        sentimentBadge = '<span class="badge bg-danger">Negatif</span>';
                    } else {
                        sentimentBadge = '<span class="badge bg-secondary">Netral</span>';
                    }
                    
                    // Populate modal
                    document.getElementById('modalTitle').textContent = title;
                    document.getElementById('modalReview').textContent = review;
                    document.getElementById('modalName').textContent = name;
                    document.getElementById('modalRating').innerHTML = rating + ' ⭐';
                    document.getElementById('modalSentiment').innerHTML = sentimentBadge;
                    document.getElementById('modalScore').textContent = score;
                    document.getElementById('modalUrl').innerHTML = '<a href="' + url + '" target="_blank" class="text-decoration-none">' + url + '</a>';
                });
            });
        });
    </script>

    <?php include('footer.php'); ?>
    <?php include('script.php'); ?>
</body>

</html>
