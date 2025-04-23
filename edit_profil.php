<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemesan') {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Ambil data pengguna
$query_user = mysqli_query($con, "SELECT * FROM users WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($query_user);

// Proses edit profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $alamat = mysqli_real_escape_string($con, $_POST['alamat']);
    $email = mysqli_real_escape_string($con, $_POST['email']);

    // Proses upload foto jika ada
    $foto = $user['foto']; // default foto lama
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/";
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            // Hapus foto lama jika ada dan bukan default
            if (!empty($user['foto']) && file_exists("uploads/" . $user['foto'])) {
                unlink("uploads/" . $user['foto']);
            }
            $foto = $new_filename;
        } else {
            $_SESSION['error_message'] = "Gagal mengunggah foto.";
        }
    }

    $update_query = "UPDATE users SET nama='$nama', alamat='$alamat', email='$email', foto='$foto' WHERE id_user='$id_user'";
    if (mysqli_query($con, $update_query)) {
        $_SESSION['success_message'] = "Profil berhasil diperbarui.";
        header("Location: profil.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui profil.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-custom {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">TiketKu</a>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="dashboard_user.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="tiket_saya.php">Tiket Saya</a></li>
                <li class="nav-item"><a class="nav-link active" href="profil.php">Profil</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container my-5">
    <div class="mb-4 text-center">
        <h3 class="fw-semibold">Edit Profil Pengguna</h3>
        <p class="text-muted">Perbarui informasi profil Anda dan unggah foto baru.</p>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">Form Edit Profil</div>
                <div class="card-body text-center">
                    <!-- Foto Profil -->
                    <img src="<?= $user['foto'] ? 'uploads/' . $user['foto'] : 'https://via.placeholder.com/120' ?>" class="profile-img mb-3" alt="Foto Profil">
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3 text-start">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($user['alamat']) ?></textarea>
                        </div>
                        <div class="mb-3 text-start">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label for="foto" class="form-label">Foto Profil (Opsional)</label>
                            <input class="form-control" type="file" name="foto" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
