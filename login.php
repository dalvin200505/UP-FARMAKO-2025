<?php
session_start();
require('koneksi.php');

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($con, "SELECT * FROM users WHERE username='$username' LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'admin' || $data['role'] == 'petugas') {
            header('Location: dashboard.php');
        } elseif ($data['role'] == 'pemesan') {
            header('Location: index.html');
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | Tiket Kereta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(to right, #667eea, #764ba2);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      width: 100%;
      max-width: 400px;
      padding: 2rem;
      border-radius: 20px;
      background: #fff;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .btn-custom {
      background: #667eea;
      color: white;
      transition: 0.3s;
    }
    .btn-custom:hover {
      background: #5a67d8;
    }
    .link-daftar {
      text-decoration: none;
      color: #667eea;
    }
    .link-daftar:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="card" data-aos="zoom-in">
    <h4 class="text-center mb-4">Masuk ke Akun</h4>
    
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" name="login" class="btn btn-custom w-100">Login</button>
      <div class="mt-3 text-center">
        Belum punya akun? <a href="register.php" class="link-daftar">Daftar di sini</a>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
</body>
</html>
