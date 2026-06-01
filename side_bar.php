<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
            <span class="d-none d-lg-block">Sentiment Analysis</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
        
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle" class="rounded-circle" style="margin-right:10px;font-size:30px;"></i>
                    <!-- ini funsi menampilkan nama  -->
                <?php echo $_SESSION['NAMA'];?>
                </a><!-- End Profile Image Icon -->
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?php echo $_SESSION['USERNAME'];?></h6>
                        <span><?php echo $_SESSION['EMAIL']; ?></span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a href="#" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#myModal1"> 
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </a>
                    </li>

                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->
        </ul>
    </nav><!-- End Icons Navigation -->
</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
<?php 
    // Get current page name
    $current_page = basename($_SERVER['PHP_SELF']); 
?>
    <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Sentiment Analysis Menu -->
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'index.php' ? '' : 'collapsed'; ?>" href="index.php">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'dataset.php' ? '' : 'collapsed'; ?>" href="dataset.php">
                <i class="bi bi-database"></i>
                <span>Dataset</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'visualisasi.php' ? '' : 'collapsed'; ?>" href="visualisasi.php">
                <i class="bi bi-bar-chart"></i>
                <span>Visualisasi</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'prediksi.php' ? '' : 'collapsed'; ?>" href="prediksi.php">
                <i class="bi bi-search"></i>
                <span>Prediksi Sentimen</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'riwayat.php' ? '' : 'collapsed'; ?>" href="riwayat.php">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Prediksi</span>
            </a>
        </li>
    </ul>
    
</aside><!-- End Sidebar -->

<!-- menampilkan peesan logout -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="logout.php" enctype="multipart/form-data">    
                    Apakah anda yakin akan keluar?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button name="save" type="submit" class="btn btn-primary">Ya</button>
                </form>  
            </div>
        </div>
    </div>
</div>