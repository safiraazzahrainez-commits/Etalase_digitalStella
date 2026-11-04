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

// Filter Pencarian Karya
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = $search ? "WHERE projects.nama_project LIKE '%$search%' OR projects.deskripsi LIKE '%$search%'" : "";

$result_projects = mysqli_query($conn, "SELECT projects.*, users.nama as nama_siswa FROM projects JOIN users ON projects.user_id = users.id $where ORDER BY projects.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Etalase Digital Stella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .top-navbar { background: #ffffff; border-bottom: 1px solid #e0e0e0; padding: 12px 30px; display: flex; justify-content: space-between; align-items: center; }
        .corner-logo { width: 38px; height: 38px; border-radius: 50%; border: 2px solid #800000; background: #fff; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .project-card { transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer; border: none; border-radius: 8px; overflow: hidden; }
        .project-card:hover { transform: translateY(-4px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.12)!important; }
        .banner-card { background: linear-gradient(135deg, #800000 0%, #a52a2a 100%); color: #fff; border-radius: 12px; padding: 25px; }
    </style>
</head>
<body>

<!-- TOP NAVBAR -->
<div class="top-navbar shadow-sm">
    <div class="d-flex align-items-center">
        <div class="corner-logo me-2">
            <img src="logo.jpg" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=150&auto=format&fit=crop&q=80';">
        </div>
        <span class="fs-5 fw-bold text-dark">Etalase Digital Stella</span>
    </div>
    <div class="d-flex align-items-center">
        <span class="me-3 text-secondary small">Selamat datang, <strong class="text-dark"><?= htmlspecialchars($user['nama']) ?></strong> <span class="badge bg-danger ms-1"><?= strtoupper(str_replace('_', ' ', $user['role'])) ?></span></span>
        
        <!-- HANYA SUPER ADMIN YANG BISA AKSES KELOLA USER -->
        <?php if ($user['role'] == 'super_admin'): ?>
            <a href="users.php" class="btn btn-outline-dark btn-sm me-2 fw-semibold"><i class="fas fa-users me-1"></i> Kelola User</a>
        <?php endif; ?>
        
        <a href="logout.php" class="btn btn-outline-danger btn-sm fw-semibold"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
    </div>
</div>

<div class="container my-4">
    
    <!-- BANNER CARD -->
    <div class="banner-card shadow-sm mb-4">
        <div class="row align-items-center">
            <div class="col-md-7 mb-3 mb-md-0">
                <h3 class="fw-bold mb-1">Galeri Kreativitas Digital</h3>
                <p class="mb-0 text-white-50 small">Temukan berbagai karya inovatif dan proyek pemrograman siswa Stella. Cari karya atau tambahkan karya baru Anda dengan mudah.</p>
            </div>
            <div class="col-md-5">
                <div class="d-flex gap-2">
                    <form method="GET" class="d-flex flex-grow-1">
                        <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Cari karya..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-light btn-sm fw-semibold text-danger"><i class="fas fa-search"></i></button>
                    </form>
                    <button class="btn btn-warning btn-sm fw-bold text-dark text-nowrap" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                        <i class="fas fa-plus me-1"></i> Tambah Karya
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- GRID KARYA -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark m-0"><i class="fas fa-th-large me-2 text-danger"></i> Daftar Karya Terbaru</h5>
        <span class="text-muted small"><?= mysqli_num_rows($result_projects) ?> karya ditemukan</span>
    </div>

    <div class="row">
        <?php if (mysqli_num_rows($result_projects) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result_projects)): ?>
                <div class="col-md-3 mb-3">
                    <div class="card h-100 shadow-sm project-card" data-bs-toggle="modal" data-bs-target="#projectModal<?= $row['id'] ?>">
                        <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" class="card-img-top" alt="Project" style="height: 140px; object-fit: cover;">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold text-dark text-truncate mb-1"><?= htmlspecialchars($row['nama_project']) ?></h6>
                            <p class="text-muted small mb-2" style="font-size: 0.75rem;"><i class="fas fa-user me-1"></i> <?= htmlspecialchars($row['nama_siswa']) ?></p>
                            <p class="card-text text-secondary small mb-0" style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($row['deskripsi']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- MODAL DETAIL KARYA -->
                <div class="modal fade" id="projectModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold"><?= htmlspecialchars($row['nama_project']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" class="img-fluid rounded mb-3 shadow-sm" alt="Detail" style="max-height: 350px; width: 100%; object-fit: contain; background: #000;">
                                <p class="text-start text-muted small mb-2"><i class="fas fa-user me-1"></i> Dibuat oleh: <strong><?= htmlspecialchars($row['nama_siswa']) ?></strong></p>
                                <p class="text-start text-dark"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                                
                                <div class="d-flex gap-2 mt-4">
                                    <?php if (!empty($row['link_project'])): ?>
                                        <a href="<?= htmlspecialchars($row['link_project']) ?>" target="_blank" class="btn btn-dark btn-sm fw-bold flex-grow-1">
                                            <i class="fab fa-github me-2"></i> Kunjungi GitHub / Live View
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <div>
                                    <!-- HAPUS: Hanya Super Admin atau pembuat karya yang bisa hapus (Admin tidak bisa hapus) -->
                                    <?php if ($user['role'] == 'super_admin' || $user_id == $row['user_id']): ?>
                                        <a href="project_action.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus karya ini?')">
                                            <i class="fas fa-trash me-1"></i> Hapus Karya
                                        </a>
                                    <?php endif; ?>

                                    <!-- EDIT: Super Admin & Admin bisa mengedit karya -->
                                    <?php if ($user['role'] == 'super_admin' || $user['role'] == 'admin' || $user_id == $row['user_id']): ?>
                                        <button type="button" class="btn btn-outline-warning btn-sm text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#editProjectModal<?= $row['id'] ?>" data-bs-dismiss="modal">
                                            <i class="fas fa-edit me-1"></i> Edit Karya
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL EDIT KARYA -->
                <div class="modal fade" id="editProjectModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="project_action.php?action=edit" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Karya</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Nama Proyek</label>
                                        <input type="text" class="form-control" name="nama_project" value="<?= htmlspecialchars($row['nama_project']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Deskripsi Singkat</label>
                                        <textarea class="form-control" name="deskripsi" rows="3" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Link GitHub / Demo</label>
                                        <input type="url" class="form-control" name="link_project" value="<?= htmlspecialchars($row['link_project']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Ganti Foto (Opsional)</label>
                                        <input type="file" class="form-control" name="gambar" accept="image/*">
                                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger btn-sm fw-bold">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Belum ada karya digital yang ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- MODAL TAMBAH KARYA -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="project_action.php?action=create" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Karya Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Proyek</label>
                        <input type="text" class="form-control" name="nama_project" placeholder="Contoh: Web Portofolio" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi Singkat</label>
                        <textarea class="form-control" name="deskripsi" rows="3" placeholder="Jelaskan tentang proyek Anda..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Link GitHub / Demo (Opsional)</label>
                        <input type="url" class="form-control" name="link_project" placeholder="https://github.com/username/repo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Upload Screenshot / Foto Karya</label>
                        <input type="file" class="form-control" name="gambar" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm fw-bold">Simpan Karya</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>