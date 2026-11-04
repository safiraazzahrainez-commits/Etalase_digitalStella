<?php
require_once 'config/database.php';
require_once 'middlewares/authMiddleware.php';
checkGuest();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'siswa';

            $insertStmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("ssss", $nama, $email, $hashed_password, $role);

            if ($insertStmt->execute()) {
                header("Location: login.php?success=Pendaftaran berhasil! Silakan login.");
                exit();
            } else {
                $error = 'Gagal mendaftarkan akun. Coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun - Etalase Digital Stella</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .auth-container {
      max-width: 450px;
      margin: auto;
      width: 100%;
      padding: 20px;
    }
    .auth-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      padding: 35px 30px;
    }
    .logo-placeholder {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      border: 2px dashed #990000;
      background-color: #fff0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px auto;
      color: #990000;
      font-size: 11px;
      text-align: center;
      overflow: hidden;
    }
    .btn-stella {
      background-color: #800000;
      color: white;
      font-weight: 600;
    }
    .btn-stella:hover {
      background-color: #660000;
      color: white;
    }
    .corner-logo {
      position: absolute;
      top: 15px;
      left: 15px;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      border: 2px solid #800000;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 8px;
      color: #800000;
      text-align: center;
    }
    footer {
      margin-top: auto;
      background: #1e1e1e;
      color: #ccc;
      padding: 15px 0;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <!-- Small corner logo placeholder -->
<div class="corner-logo">
  <img src="logo.jpg" alt="Logo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
</div>

  <div class="auth-container">
    <div class="auth-card">
      <!-- Middle logo placeholder -->
      <div class="logo-placeholder" style="width: 130px; height: 130px; border: none; background: transparent;">
  <img src="logo.jpg" alt="Logo Telkom School" style="width: 180%; height: 180%; object-fit: contain;">
</div>

      <h4 class="text-center font-weight-bold mb-4" style="color: #800000;">Pendaftaran Siswa Baru</h4>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 fs-6"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form action="register.php" method="POST">
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap Siswa" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Alamat Email</label>
          <input type="email" name="email" class="form-control" placeholder="siswa@stella.sch.id" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Kata Sandi</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Konfirmasi Kata Sandi</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-stella w-100 py-2 mb-3">Daftar Sekarang</button>
      </form>

      <div class="text-center mt-3">
        <p class="text-muted">Sudah punya akun? <a href="login.php" style="color: #800000; font-weight: bold;">Halaman Login</a></p>
      </div>
    </div>
  </div>

  <footer class="text-center">
    <div class="container">
      <p class="mb-0">&copy; 2026 Telkom School - Etalase Digital Stella. All Rights Reserved.</p>
    </div>
  </footer>
</body>
</html>
