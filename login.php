<?php
session_start();
if (isset($_SESSION['ID'])) {
    header("Location:index.php");
    exit();
}
include_once('dbcon.php');

if (isset($_POST['submit'])) {

    $errorMsg = "";

    $username = $conn->real_escape_string(trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $hashedPassword = $conn->real_escape_string(md5($password));

    if (!empty($username) && !empty($password)) {
        $query  = "SELECT * FROM users WHERE username = '$username' AND password='$hashedPassword'";
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['ID'] = $row['id_user'];
            $_SESSION['USERNAME'] = $row['username'];
            $_SESSION['NAMA'] = $row['nama'];
            $_SESSION['EMAIL'] = $row['email'];

            header("Location: index.php?login=success");
            exit();
        } else {
            $errorMsg = "Username atau password salah.";
        }
    } else {
        $errorMsg = "Username dan Password harus diisi.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>SPK Waspas</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            
            <div class="col-lg-6">
              <div class="card">

                <div class="card-body">
                <div class="col-lg-12 col-md-6 pt-4 text-center">
                  <div class="pt-2 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login Sistem</h5>
                  </div>
                </div>

                  <?php if (isset($errorMsg)) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $errorMsg; ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php } ?>
                  
                  <form class="row g-3 needs-validation" method="POST" action="">

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <input type="text" name="username" class="form-control" id="yourUsername"  placeholder="Masukkan username" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <div class="input-group has-validation">
                      <input type="password" name="password" class="form-control" id="yourPassword" placeholder="Masukkan password" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                      </div>
                    </div>
                    <div class="col-12">
                    <input type="submit" name="submit" class="btn btn-primary w-100" value="Login">
                    </div>
                    
                  </form>

                </div>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>