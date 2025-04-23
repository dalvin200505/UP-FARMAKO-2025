<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemesan') {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$query_user = mysqli_query($con, "SELECT * FROM users WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($query_user);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .card-custom {
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">TiketKu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
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
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card card-custom text-center p-4">
                <img src="<?= $user['foto'] ? 'uploads/' . $user['foto'] : 'https://via.placeholder.com/150' ?>" class="profile-img mb-3" alt="Foto Profil">
                <h4 class="fw-bold mb-0"><?= htmlspecialchars($user['nama']) ?></h4>
                <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                <hr>
                <div class="text-start px-4">
                    <p><i class="bi bi-geo-alt-fill me-2"></i><strong>Alamat:</strong> <?= htmlspecialchars($user['alamat']) ?></p>
                    <p><i class="bi bi-person-badge me-2"></i><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                </div>
                <a href="edit_profil.php" class="btn btn-outline-primary w-100 mt-3"><i class="bi bi-pencil-square me-1"></i> Edit Profil</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
