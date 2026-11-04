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

$action = isset($_GET['action']) ? $_GET['action'] : '';
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// 1. TAMBAH KARYA (All Authenticated)
if ($action == 'create') {
    $nama_project = mysqli_real_escape_string($conn, $_POST['nama_project']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $link_project = mysqli_real_escape_string($conn, $_POST['link_project']);

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file_name = $_FILES['gambar']['name'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_name = uniqid('proj_') . '.' . $ext;
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (move_uploaded_file($file_tmp, 'uploads/' . $new_name)) {
            mysqli_query($conn, "INSERT INTO projects (user_id, nama_project, deskripsi, link_project, gambar) VALUES ('$user_id', '$nama_project', '$deskripsi', '$link_project', '$new_name')");
        }
    }
    header("Location: dashboard.php");
    exit;
}

// 2. EDIT KARYA (Owner / Admin / Super Admin)
if ($action == 'edit') {
    $id = intval($_POST['id']);
    $nama_project = mysqli_real_escape_string($conn, $_POST['nama_project']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $link_project = mysqli_real_escape_string($conn, $_POST['link_project']);

    $check = mysqli_query($conn, "SELECT * FROM projects WHERE id = '$id'");
    if ($row = mysqli_fetch_assoc($check)) {
        if ($user_role == 'super_admin' || $user_role == 'admin' || $row['user_id'] == $user_id) {
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $file_name = $_FILES['gambar']['name'];
                $file_tmp = $_FILES['gambar']['tmp_name'];
                $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $new_name = uniqid('proj_') . '.' . $ext;
                if (move_uploaded_file($file_tmp, 'uploads/' . $new_name)) {
                    @unlink('uploads/' . $row['gambar']);
                    mysqli_query($conn, "UPDATE projects SET nama_project='$nama_project', deskripsi='$deskripsi', link_project='$link_project', gambar='$new_name' WHERE id='$id'");
                }
            } else {
                mysqli_query($conn, "UPDATE projects SET nama_project='$nama_project', deskripsi='$deskripsi', link_project='$link_project' WHERE id='$id'");
            }
        }
    }
    header("Location: dashboard.php");
    exit;
}

// 3. HAPUS KARYA (Owner / Admin / Super Admin sesuai dokumen PDF)[cite: 9]
if ($action == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $check = mysqli_query($conn, "SELECT * FROM projects WHERE id = '$id'");
    if ($row = mysqli_fetch_assoc($check)) {
        if ($user_role == 'super_admin' || $user_role == 'admin' || $row['user_id'] == $user_id) {
            @unlink('uploads/' . $row['gambar']);
            mysqli_query($conn, "DELETE FROM projects WHERE id = '$id'");
        }
    }
    header("Location: dashboard.php");
    exit;
}
?>