<?php include('session.php'); ?>
<?php include('head.php'); ?>

<body>
    <!-- Sidebar -->
    <?php include('side_bar.php'); ?>

    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="pagetitle">
                        <h1>Riwayat Prediksi</h1>
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Riwayat Prediksi</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Daftar Riwayat Prediksi</h5>
                            <div class="table-responsive">
                                <table class="table table-hover datatable" id="historyTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Waktu</th>
                                            <th scope="col">Teks Input</th>
                                            <th scope="col">Hasil</th>
                                            <th scope="col">Skor</th>
                                            <th scope="col">Confidence</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM sentiment_predictions ORDER BY created_at DESC LIMIT 200";
                                        $result = $conn->query($query);
                                        
                                        if ($result->num_rows > 0) {
                                            $no = 1;
                                            while ($row = $result->fetch_assoc()) {
                                                $sentimentBadge = '';
                                                if ($row['sentiment_result'] == 'positif') {
                                                    $sentimentBadge = '<span class="badge bg-success">Positif</span>';
                                                } elseif ($row['sentiment_result'] == 'negatif') {
                                                    $sentimentBadge = '<span class="badge bg-danger">Negatif</span>';
                                                } else {
                                                    $sentimentBadge = '<span class="badge bg-secondary">Netral</span>';
                                                }
                                                
                                                // Format tanggal
                                                $date = date('d/m/Y H:i', strtotime($row['created_at']));
                                                
                                                echo "
                                                <tr>
                                                    <th scope='row'>" . $no++ . "</th>
                                                    <td><small>$date</small></td>
                                                    <td><small>" . substr(htmlspecialchars($row['user_input']), 0, 50) . "...</small></td>
                                                    <td>" . $sentimentBadge . "</td>
                                                    <td><small>" . number_format($row['score'], 2) . "</small></td>
                                                    <td>
                                                        <div style='width: 100px;'>
                                                            <div class='progress'>
                                                                <div class='progress-bar' style='width: " . $row['confidence'] . "%'></div>
                                                            </div>
                                                            <small>" . $row['confidence'] . "%</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                                ";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center text-muted'>Belum ada riwayat prediksi. Lakukan prediksi terlebih dahulu.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mt-4">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Prediksi</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-search"></i>
                                </div>
                                <div class="ps-3">
                                    <?php
                                    $query = "SELECT COUNT(*) as total FROM sentiment_predictions";
                                    $result = $conn->query($query);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h6><?= $row['total']; ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Total Prediksi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Rata-rata Confidence</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-percent"></i>
                                </div>
                                <div class="ps-3">
                                    <?php
                                    $query = "SELECT AVG(confidence) as avg_confidence FROM sentiment_predictions";
                                    $result = $conn->query($query);
                                    $row = $result->fetch_assoc();
                                    $avgConfidence = $row['avg_confidence'] ? round($row['avg_confidence'], 2) : 0;
                                    ?>
                                    <h6><?= $avgConfidence; ?>%</h6>
                                    <span class="text-muted small pt-2 ps-1">Rata-rata</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Sentimen Terbanyak</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-bar-chart"></i>
                                </div>
                                <div class="ps-3">
                                    <?php
                                    $query = "SELECT sentiment_result, COUNT(*) as total FROM sentiment_predictions 
                                             GROUP BY sentiment_result ORDER BY total DESC LIMIT 1";
                                    $result = $conn->query($query);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h6><?= ucfirst($row['sentiment_result'] ?? '-'); ?></h6>
                                    <span class="text-muted small pt-2 ps-1"><?= $row['total']; ?> Prediksi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribution Chart -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Distribusi Sentimen Dari Prediksi User</h5>
                            <div id="predictionChart" style="min-height: 400px;" class="echart"></div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main><!-- End #main -->

    <?php include('footer.php'); ?>
    <?php include('script.php'); ?>

    <!-- ECharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>

    <script>
        // Get distribution data
        <?php
        $query = "SELECT sentiment_result, COUNT(*) as total FROM sentiment_predictions 
                 GROUP BY sentiment_result";
        $result = $conn->query($query);
        
        $positif = 0;
        $negatif = 0;
        $netral = 0;
        
        while ($row = $result->fetch_assoc()) {
            if ($row['sentiment_result'] == 'positif') $positif = $row['total'];
            elseif ($row['sentiment_result'] == 'negatif') $negatif = $row['total'];
            else $netral = $row['total'];
        }
        ?>

        // Pie Chart untuk Prediksi User
        var predictionChart = echarts.init(document.getElementById('predictionChart'));
        var option = {
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left'
            },
            series: [
                {
                    name: 'Jumlah',
                    type: 'pie',
                    radius: '50%',
                    data: [
                        { value: <?= $positif; ?>, name: 'Positif' },
                        { value: <?= $negatif; ?>, name: 'Negatif' },
                        { value: <?= $netral; ?>, name: 'Netral' }
                    ],
                    color: ['#198754', '#dc3545', '#6c757d'],
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };
        predictionChart.setOption(option);

        // Resize chart on window resize
        window.addEventListener('resize', function() {
            predictionChart.resize();
        });
    </script>

</body>

</html>
