<?php
session_start();
require 'koneksi.php';

// Pastikan admin yang mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_user = $_GET['id'];

    // Ambil data pengguna berdasarkan ID
    $sql = "SELECT * FROM users WHERE id_user = '$id_user'";
    $result = mysqli_query($con, $sql);
    $user = mysqli_fetch_assoc($result);
}

if (isset($_POST['submit'])) {
    // Ambil data dari form
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Update data pengguna
    $sql_update = "UPDATE users SET username = '$username', role = '$role' WHERE id_user = '$id_user'";
    if (mysqli_query($con, $sql_update)) {
        echo "<script>alert('Data pengguna berhasil diperbarui!'); window.location.href = 'kelola_pengguna.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memperbarui data pengguna!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
    <h3 class="text-center mb-4">Edit Pengguna</h3>

    <div class="card p-4 shadow-lg">
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= $user['username']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="petugas" <?= $user['role'] == 'petugas' ? 'selected' : ''; ?>>Petugas</option>
                    <option value="pemesan" <?= $user['role'] == 'pemesan' ? 'selected' : ''; ?>>Pemesan</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
