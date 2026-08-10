<?php
session_start();
if (isset($_SESSION['ID'])) {
    header("Location:index.php");
    exit();
}
include_once('dbcon.php');

$errorMsg = "";
$successMsg = "";

// Login Handler
if (isset($_POST['submit'])) {
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

// Reset Password Handler
if (isset($_POST['reset_password'])) {
    $resetUser = $conn->real_escape_string(trim($_POST['reset_username_email'] ?? ''));
    $newPass = trim($_POST['new_password'] ?? '');
    $confirmPass = trim($_POST['confirm_password'] ?? '');

    if (empty($resetUser) || empty($newPass) || empty($confirmPass)) {
        $errorMsg = "Semua field lupa password harus diisi.";
    } elseif ($newPass !== $confirmPass) {
        $errorMsg = "Konfirmasi password baru tidak cocok.";
    } else {
        $checkQuery = "SELECT * FROM users WHERE username = '$resetUser' OR email = '$resetUser'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {
            $userRow = $checkResult->fetch_assoc();
            $targetUserId = $userRow['id_user'];
            $newHashed = $conn->real_escape_string(md5($newPass));

            $updateQuery = "UPDATE users SET password = '$newHashed' WHERE id_user = '$targetUserId'";
            if ($conn->query($updateQuery)) {
                $successMsg = "Password berhasil diperbarui! Silakan login dengan password baru Anda.";
            } else {
                $errorMsg = "Gagal memperbarui password: " . $conn->error;
            }
        } else {
            $errorMsg = "Username atau Email tidak ditemukan dalam sistem.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login - Sentiment Analysis</title>
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
            
            <div class="col-lg-5 col-md-7">
              <div class="card shadow-sm border-0 rounded-3">

                <div class="card-body p-4">
                  <div class="text-center pt-2 pb-3">
                    <h5 class="card-title text-center pb-0 fs-4 text-primary font-weight-bold">Login Sistem</h5>
                    <p class="text-muted small mb-0">Masukkan username & password untuk mengakses aplikasi</p>
                  </div>

                  <?php if (!empty($errorMsg)) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($errorMsg); ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php } ?>

                  <?php if (!empty($successMsg)) { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($successMsg); ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php } ?>
                  
                  <form class="row g-3 needs-validation" method="POST" action="">

                    <div class="col-12">
                      <label for="yourUsername" class="form-label font-weight-bold">Username</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="username" class="form-control" id="yourUsername" placeholder="Masukkan username" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="yourPassword" class="form-label mb-0 font-weight-bold">Password</label>
                        <a href="#" class="small text-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Lupa Password?</a>
                      </div>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control" id="yourPassword" placeholder="Masukkan password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" title="Tampilkan / Sembunyikan Password">
                          <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                        <div class="invalid-feedback">Please enter your password!</div>
                      </div>
                    </div>

                    <div class="col-12 pt-2">
                      <button type="submit" name="submit" class="btn btn-primary w-100 py-2 font-weight-bold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                      </button>
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

  <!-- Modal Lupa Password -->
  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="forgotPasswordModalLabel"><i class="bi bi-key me-2"></i>Lupa Password</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
          <div class="modal-body p-4">
            <p class="text-muted small mb-3">Masukkan username atau email terdaftar untuk menyetel ulang password akun Anda.</p>
            
            <div class="mb-3">
              <label for="resetUsernameEmail" class="form-label font-weight-bold">Username / Email</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-person-badge text-muted"></i></span>
                <input type="text" class="form-control" id="resetUsernameEmail" name="reset_username_email" placeholder="Masukkan username atau email" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="newPassword" class="form-label font-weight-bold">Password Baru</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Masukkan password baru" required>
                <button class="btn btn-outline-secondary" type="button" id="toggleNewPasswordBtn" title="Tampilkan / Sembunyikan Password">
                  <i class="bi bi-eye" id="newEyeIcon"></i>
                </button>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="confirmPassword" class="form-label font-weight-bold font-weight-bold">Konfirmasi Password Baru</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-shield-lock text-muted"></i></span>
                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Ulangi password baru" required>
                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPasswordBtn" title="Tampilkan / Sembunyikan Password">
                  <i class="bi bi-eye" id="confirmEyeIcon"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="reset_password" class="btn btn-primary">Simpan Password Baru</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Toggle Password Visibility JS -->
  <script>
    function setupPasswordToggle(buttonId, inputId, iconId) {
      var btn = document.getElementById(buttonId);
      var input = document.getElementById(inputId);
      var icon = document.getElementById(iconId);
      
      if (btn && input && icon) {
        btn.addEventListener('click', function () {
          if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
          } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
          }
        });
      }
    }

    setupPasswordToggle('togglePasswordBtn', 'yourPassword', 'eyeIcon');
    setupPasswordToggle('toggleNewPasswordBtn', 'newPassword', 'newEyeIcon');
    setupPasswordToggle('toggleConfirmPasswordBtn', 'confirmPassword', 'confirmEyeIcon');
  </script>

</body>

</html>