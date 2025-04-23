<?php
require('koneksi.php');
$error = '';
$success = '';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($con, $_POST['nama']);
    $email    = mysqli_real_escape_string($con, $_POST['email']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $alamat   = mysqli_real_escape_string($con, $_POST['alamat']);
    $role     = 'pemesan'; // Role default

    if (!empty($nama) && !empty($email) && !empty($username) && !empty($password) && !empty($alamat)) {
        $cekUser = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($cekUser) == 0) {
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($con, "INSERT INTO users (nama, email, username, password, alamat, role) 
                                          VALUES ('$nama', '$email', '$username', '$hashPassword', '$alamat', '$role')");

            if ($insert) {
                $success = "Registrasi berhasil! Silakan <a href='login.php'>login di sini</a>.";
            } else {
                $error = "Terjadi kesalahan saat menyimpan data.";
            }
        } else {
            $error = "Username sudah digunakan, silakan pilih yang lain.";
        }
    } else {
        $error = "Semua field harus diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi | Tiket Kereta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #00c9ff, #92fe9d);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        .btn-custom {
            background-color: #00c9ff;
            color: #fff;
            border: none;
        }
        .btn-custom:hover {
            background-color: #00b5e2;
        }
    </style>
</head>
<body>
<div class="card bg-white">
    <h4 class="text-center mb-4">Form Registrasi Penumpang</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" name="nama" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" name="alamat" rows="3" required></textarea>
        </div>

        <!-- Role diset otomatis sebagai pemesan -->
        <input type="hidden" name="role" value="pemesan">

        <button type="submit" name="register" class="btn btn-custom w-100">Daftar</button>

        <div class="mt-3 text-center">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </form>
</div>
</body>
</html>
