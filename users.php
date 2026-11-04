<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "Etalase_digitalStella";
$conn = mysqli_connect($host, $user, $pass, $db);

$user_id = $_SESSION['user_id'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query_user);

// Validasi: Hanya Super Admin yang boleh akses halaman kelola user
if ($user['role'] != 'super_admin') {
    header("Location: dashboard.php");
    exit;
}

// Proses hapus user jika tombol hapus diklik
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $del_id = intval($_GET['id']);
    if ($del_id != $user_id) {
        mysqli_query($conn, "DELETE FROM users WHERE id = '$del_id'");
    }
    header("Location: users.php");
    exit;
}

// Ambil semua daftar user dari database
$users_result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Etalase Digital Stella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .top-navbar { background: #ffffff; border-bottom: 1px solid #e0e0e0; padding: 12px 30px; display: flex; justify-content: space-between; align-items: center; }
        .corner-logo { width: 38px; height: 38px; border-radius: 50%; border: 2px solid #800000; background: #fff; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    </style>
</head>
<body>

<!-- TOP NAVBAR -->
<div class="top-navbar shadow-sm">
    <div class="d-flex align-items-center">
        <div class="corner-logo me-2">
            <img src="logo.jpg" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=150&auto=format&fit=crop&q=80';">
        </div>
        <span class="fs-5 fw-bold text-dark">Manajemen Pengguna (Kelola User)</span>
    </div>
    <div class="d-flex align-items-center">
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm me-2 fw-semibold"><i class="fas fa-home me-1"></i> Dashboard</a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm fw-semibold"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
    </div>
</div>

<div class="container my-4">
    <div class="card shadow-sm border-0 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0"><i class="fas fa-users me-2 text-danger"></i> Daftar Akun Pengguna yang Terdaftar</h4>
            <span class="badge bg-danger p-2">Super Admin Access</span>
        </div>
        <p class="text-muted small">Berikut adalah daftar seluruh user/pengguna yang memiliki akses ke dalam sistem web Etalase Digital Stella.</p>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role / Peran</th>
                        <th>Tanggal Terdaftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($users_result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <?php if($row['role'] == 'super_admin'): ?>
                                <span class="badge bg-danger">Super Admin</span>
                            <?php elseif($row['role'] == 'admin'): ?>
                                <span class="badge bg-warning text-dark">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Siswa</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted"><?= $row['created_at'] ?></td>
                        <td class="text-center">
                            <?php if($row['id'] != $user_id): ?>
                                <a href="users.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengguna <?= htmlspecialchars($row['nama']) ?> ini?')">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </a>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">(Akun Anda)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>