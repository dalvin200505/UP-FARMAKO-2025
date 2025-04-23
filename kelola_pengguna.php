<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Create & Update
if (isset($_POST['simpan'])) {
    $id_user  = $_POST['id_user'] ?? '';
    $nama     = mysqli_real_escape_string($con, $_POST['nama']);
    $email    = mysqli_real_escape_string($con, $_POST['email']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $alamat   = mysqli_real_escape_string($con, $_POST['alamat']);
    $role     = mysqli_real_escape_string($con, $_POST['role']);

    if ($id_user) {
        // Update
        $query = "UPDATE users SET nama='$nama', email='$email', username='$username', alamat='$alamat', role='$role' WHERE id_user=$id_user";
        if (mysqli_query($con, $query)) {
            $success = "Data pengguna berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui data.";
        }
    } else {
        // Tambah
        $password = password_hash(mysqli_real_escape_string($con, $_POST['password']), PASSWORD_DEFAULT);
        $cek = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($cek) == 0) {
            $query = "INSERT INTO users (nama, email, username, password, alamat, role) 
                      VALUES ('$nama', '$email', '$username', '$password', '$alamat', '$role')";
            if (mysqli_query($con, $query)) {
                $success = "Pengguna baru berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan pengguna.";
            }
        } else {
            $error = "Username sudah terdaftar!";
        }
    }
}

// Delete
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Hapus data penumpang terkait dengan pemesanan
    $delete_penumpang = mysqli_query($con, "DELETE FROM penumpang WHERE id_pemesanan IN (SELECT id_pemesanan FROM pemesanan WHERE id_user=$id)");

    if ($delete_penumpang) {
        // Hapus pemesanan yang terkait
        $delete_pemesanan = mysqli_query($con, "DELETE FROM pemesanan WHERE id_user=$id");
        
        if ($delete_pemesanan) {
            // Hapus pengguna setelah pemesanan dan penumpang dihapus
            $delete_user = mysqli_query($con, "DELETE FROM users WHERE id_user=$id");

            if ($delete_user) {
                header("Location: kelola_pengguna.php");
                exit();
            } else {
                $error = "Gagal menghapus pengguna.";
            }
        } else {
            $error = "Gagal menghapus pemesanan pengguna.";
        }
    } else {
        $error = "Gagal menghapus penumpang pengguna.";
    }
}

// Ambil data untuk edit
$edit_user = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($con, "SELECT * FROM users WHERE id_user=$id");
    $edit_user = mysqli_fetch_assoc($res);
}

// Ambil semua pengguna
$users = mysqli_query($con, "SELECT * FROM users ORDER BY id_user DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f9fc; }
        .card { border-radius: 12px; box-shadow: 0 5px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4 text-center">👥 Manajemen Pengguna</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Form Tambah/Edit -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <?= $edit_user ? 'Edit Pengguna' : 'Tambah Pengguna' ?>
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <?php if ($edit_user): ?>
                    <input type="hidden" name="id_user" value="<?= $edit_user['id_user'] ?>">
                <?php endif; ?>
                <div class="col-md-4">
                    <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required value="<?= $edit_user['nama'] ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <input type="email" name="email" class="form-control" placeholder="Email" required value="<?= $edit_user['email'] ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="username" class="form-control" placeholder="Username" required value="<?= $edit_user['username'] ?? '' ?>">
                </div>
                <?php if (!$edit_user): ?>
                    <div class="col-md-4">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                <?php endif; ?>
                <div class="col-md-4">
                    <textarea name="alamat" class="form-control" placeholder="Alamat" rows="1" required><?= $edit_user['alamat'] ?? '' ?></textarea>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" <?= (isset($edit_user) && $edit_user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="petugas" <?= (isset($edit_user) && $edit_user['role'] == 'petugas') ? 'selected' : '' ?>>Petugas</option>
                        <option value="pemesan" <?= (isset($edit_user) && $edit_user['role'] == 'pemesan') ? 'selected' : '' ?>>Pemesan</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" name="simpan" class="btn btn-success w-100"><?= $edit_user ? 'Update' : 'Tambah' ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Pengguna -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Daftar Pengguna</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Alamat</th>
                        <th>Role</th>
                        <th width="150px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                <?php $no = 1; while ($row = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['alamat']) ?></td>
                        <td><?= ucfirst($row['role']) ?></td>
                        <td>
                            <a href="?edit=<?= $row['id_user'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?hapus=<?= $row['id_user'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<a href="dashboard.php" class="btn btn-outline-secondary mt-3">← Kembali ke Dashboard</a>
</body>
</html>
