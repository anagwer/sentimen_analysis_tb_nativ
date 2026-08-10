<?php include('session.php'); ?>
<?php include('head.php'); ?>
<?php require_once 'sentiment_classifier.php'; ?>

<style>
.img-hover-zoom {
    transition: transform .3s ease, filter .3s ease;
}
.img-hover-zoom:hover {
    transform: scale(1.05);
    filter: brightness(0.9);
}
</style>

<body>
    <!-- Sidebar -->
    <?php include('side_bar.php'); ?>

    <main id="main" class="main">
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="mb-4">Visualisasi & Analisis Dataset</h4>
                </div>
            </div>

            <?php
            $classifier = new SentimentClassifier($conn);
            $distribution = $classifier->getDatasetDistribution();
            
            // Get sentiment distribution for chart
            $positifCount = $distribution['positif']['count'];
            $negatifCount = $distribution['negatif']['count'];
            $netralCount = $distribution['netral']['count'];
            $total = $distribution['total'];
            ?>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Review</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $total; ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Total dataset</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Sentimen Positif</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-hand-thumbs-up"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $positifCount; ?></h6>
                                    <span class="text-muted small pt-2 ps-1"><?= $distribution['positif']['percentage']; ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Sentimen Negatif</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-hand-thumbs-down"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $negatifCount; ?></h6>
                                    <span class="text-muted small pt-2 ps-1"><?= $distribution['negatif']['percentage']; ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Sentimen Netral</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-dash-circle"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?= $netralCount; ?></h6>
                                    <span class="text-muted small pt-2 ps-1"><?= $distribution['netral']['percentage']; ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <!-- Pie Chart -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Distribusi Sentimen (Pie Chart)</h5>
                            <div id="pieChart" style="min-height: 400px;" class="echart"></div>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Distribusi Sentimen (Bar Chart)</h5>
                            <div id="barChart" style="min-height: 400px;" class="echart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doughnut Chart -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Analisis Sentimen Detail</h5>
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span class="badge bg-success">Positif</span>
                                        </td>
                                        <td><?= $positifCount; ?></td>
                                        <td><?= $distribution['positif']['percentage']; ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge bg-danger">Negatif</span>
                                        </td>
                                        <td><?= $negatifCount; ?></td>
                                        <td><?= $distribution['negatif']['percentage']; ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">Netral</span>
                                        </td>
                                        <td><?= $netralCount; ?></td>
                                        <td><?= $distribution['netral']['percentage']; ?>%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong><?= $total; ?></strong></td>
                                        <td><strong>100%</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Kategori Sentimen</h5>
                            <div id="doughnutChart" style="min-height: 400px;" class="echart"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Preview Gambar -->
            <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalTitle">Preview Visualisasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-3 bg-dark">
                            <img src="" id="modalImagePreview" class="img-fluid rounded" style="max-height: 75vh;">
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function openImageModal(src, title) {
                    document.getElementById('modalImagePreview').src = src;
                    document.getElementById('imageModalTitle').innerText = title;
                    var myModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
                    myModal.show();
                }
            </script>

        </section>
    </main><!-- End #main -->

    <?php include('footer.php'); ?>
    <?php include('script.php'); ?>

    <!-- ECharts -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>

    <script>
        // Pie Chart
        var pieChart = echarts.init(document.getElementById('pieChart'));
        var pieOption = {
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
                        { value: <?= $positifCount; ?>, name: 'Positif' },
                        { value: <?= $negatifCount; ?>, name: 'Negatif' },
                        { value: <?= $netralCount; ?>, name: 'Netral' }
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
        pieChart.setOption(pieOption);

        // Bar Chart
        var barChart = echarts.init(document.getElementById('barChart'));
        var barOption = {
            xAxis: {
                type: 'category',
                data: ['Positif', 'Negatif', 'Netral']
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    data: [<?= $positifCount; ?>, <?= $negatifCount; ?>, <?= $netralCount; ?>],
                    type: 'bar',
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#198754' },
                            { offset: 0.5, color: '#20c997' },
                            { offset: 1, color: '#12b886' }
                        ])
                    }
                }
            ]
        };
        barChart.setOption(barOption);

        // Doughnut Chart
        var doughnutChart = echarts.init(document.getElementById('doughnutChart'));
        var doughnutOption = {
            tooltip: {
                trigger: 'item'
            },
            series: [
                {
                    name: 'Sentimen',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: false,
                        position: 'center'
                    },
                    data: [
                        { value: <?= $positifCount; ?>, name: 'Positif' },
                        { value: <?= $negatifCount; ?>, name: 'Negatif' },
                        { value: <?= $netralCount; ?>, name: 'Netral' }
                    ],
                    color: ['#198754', '#dc3545', '#6c757d']
                }
            ]
        };
        doughnutChart.setOption(doughnutOption);

        // Resize charts on window resize
        window.addEventListener('resize', function() {
            pieChart.resize();
            barChart.resize();
            doughnutChart.resize();
        });
    </script>

</body>

</html>
