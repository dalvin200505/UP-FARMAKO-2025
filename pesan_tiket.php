<?php
include 'koneksi.php';
session_start();

// Cek jika user belum login atau bukan role 'pemesan'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemesan') {
    header("Location: login.php");
    exit();
}

// Ambil data rute dari database
$query = "SELECT * FROM rute_transportasi ORDER BY tanggal_keberangkatan ASC";
$result = mysqli_query($con, $query);

// Proses pemesanan tiket
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_rute = $_POST['id_rute'];
    $username = $_SESSION['username'];
    $tanggal_pemesanan = date('Y-m-d H:i:s');
    $jumlah_tiket = 1; // Karena satu form hanya untuk satu penumpang

    // Ambil data penumpang
    $nama_penumpang = $_POST['nama_penumpang'];
    $no_identitas = $_POST['no_identitas'];
    $kursi = $_POST['kursi'];

    // Cek apakah kursi sudah dipesan untuk rute ini
    $cek_kursi = mysqli_query($con, "
        SELECT p.kursi FROM penumpang p
        JOIN pemesanan pm ON p.id_pemesanan = pm.id_pemesanan
        WHERE pm.id_rute = '$id_rute' AND p.kursi = '$kursi'
    ");

    if (mysqli_num_rows($cek_kursi) > 0) {
        $message = "Nomor kursi $kursi sudah dipesan! Silakan pilih nomor kursi lain.";
    } else {
        // Ambil id_user dari tabel users
        $user_query = mysqli_query($con, "SELECT id_user FROM users WHERE username = '$username'");
        $user_data = mysqli_fetch_assoc($user_query);
        $id_user = $user_data['id_user'];

        // Insert data pemesanan ke tabel pemesanan
        $insert_query = "INSERT INTO pemesanan (id_user, id_rute, username, tanggal_pemesanan, jumlah_tiket) 
                         VALUES ('$id_user', '$id_rute', '$username', '$tanggal_pemesanan', '$jumlah_tiket')";
        $insert_result = mysqli_query($con, $insert_query);

        if ($insert_result) {
            $id_pemesanan = mysqli_insert_id($con);

            // Insert data penumpang
            $insert_penumpang_query = "INSERT INTO penumpang (id_pemesanan, nama_penumpang, no_identitas, kursi) 
                                       VALUES ('$id_pemesanan', '$nama_penumpang', '$no_identitas', '$kursi')";
            $insert_penumpang_result = mysqli_query($con, $insert_penumpang_query);

            if ($insert_penumpang_result) {
                // Redirect ke halaman pembayaran setelah berhasil
                header("Location: pembayaran.php?id_pemesanan=$id_pemesanan");
                exit();
            } else {
                $message = "Terjadi kesalahan saat menambahkan penumpang.";
            }
        } else {
            $message = "Terjadi kesalahan saat memesan tiket.";
        }
    }
}

// Handle Tambah / Update Rute
if (isset($_POST['submit_rute'])) {
    $id_transportasi = $_POST['id_transportasi'];
    $asal = $_POST['asal'];
    $tujuan = $_POST['tujuan'];
    $tanggal_keberangkatan = $_POST['tanggal_keberangkatan'];
    $waktu_keberangkatan = $_POST['waktu_keberangkatan'];
    $harga = $_POST['harga'];
    $kelas = $_POST['kelas'];

    if ($id_transportasi && $asal && $tujuan && $tanggal_keberangkatan && $waktu_keberangkatan && $harga && $kelas) {
        if (isset($_POST['id_rute']) && $_POST['id_rute'] != '') {
            // Update
            $id_rute = $_POST['id_rute'];
            $sql = "UPDATE rute_transportasi SET 
                        id_transportasi='$id_transportasi',
                        asal='$asal',
                        tujuan='$tujuan',
                        tanggal_keberangkatan='$tanggal_keberangkatan',
                        waktu_keberangkatan='$waktu_keberangkatan',
                        harga='$harga',
                        kelas='$kelas' 
                    WHERE id_rute='$id_rute'";
            $success = "Rute berhasil diperbarui!";
        } else {
            // Insert
            $sql = "INSERT INTO rute_transportasi (id_transportasi, asal, tujuan, tanggal_keberangkatan, waktu_keberangkatan, harga, kelas) 
                    VALUES ('$id_transportasi', '$asal', '$tujuan', '$tanggal_keberangkatan', '$waktu_keberangkatan', '$harga', '$kelas')";
            $success = "Rute berhasil ditambahkan!";
        }

        if (!mysqli_query($con, $sql)) {
            $error = "Terjadi kesalahan pada database.";
        }
    } else {
        $error = "Semua field wajib diisi.";
    }
}

// Hapus Rute
if (isset($_GET['delete'])) {
    $id_rute = $_GET['delete'];
    mysqli_query($con, "DELETE FROM rute_transportasi WHERE id_rute='$id_rute'");
    $success = "Rute berhasil dihapus.";
}

// Ambil data untuk update (jika ada)
$edit_rute = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $edit_query = mysqli_query($con, "SELECT * FROM rute_transportasi WHERE id_rute='$id_edit'");
    $edit_rute = mysqli_fetch_assoc($edit_query);
}

// Ambil daftar transportasi untuk select
$transportasi_list = mysqli_query($con, "SELECT * FROM transportasi ORDER BY nama ASC");

// Ambil semua rute
$rute_result = mysqli_query($con, "SELECT r.*, t.nama 
                                   FROM rute_transportasi r 
                                   JOIN transportasi t ON r.id_transportasi = t.id_transportasi 
                                   ORDER BY r.tanggal_keberangkatan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pemesanan Tiket dan Kelola Rute Transportasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f1f8e9, #fff);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        footer {
            background-color: #f8f9fa;
            margin-top: auto;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .alert-info, .alert-danger {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem Pemesanan Tiket</a>
            <div class="d-flex">
                <a href="dashboard_user.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-light text-primary">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="card p-4 shadow-lg">
            <h3 class="text-center mb-4 fw-bold text-primary">Pesan Tiket Transportasi</h3>

            <!-- Message -->
            <?php if (isset($message)) { ?>
                <div class="alert <?= strpos($message, 'berhasil') !== false ? 'alert-info' : 'alert-danger' ?> text-center" role="alert">
                    <?= $message; ?>
                </div>
            <?php } ?>

            <!-- Form Pemesanan Tiket -->
            <form method="POST">
                <div class="mb-3">
                    <label for="rute" class="form-label">Pilih Rute</label>
                    <select name="id_rute" id="rute" class="form-select" onchange="updateHarga()" required>
                        <option value="">Pilih Rute</option>
                        <?php mysqli_data_seek($result, 0); while ($row = mysqli_fetch_assoc($result)) { ?>
                            <option value="<?= $row['id_rute'] ?>" data-harga="<?= $row['harga'] ?>">
                                <?= htmlspecialchars($row['asal']) . " - " . htmlspecialchars($row['tujuan']) ?> 
                                (<?= date('d-m-Y', strtotime($row['tanggal_keberangkatan'])) ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nama_penumpang" class="form-label">Nama Penumpang</label>
                    <input type="text" class="form-control" id="nama_penumpang" name="nama_penumpang" required>
                </div>
                <div class="mb-3">
                    <label for="no_identitas" class="form-label">No. Identitas</label>
                    <input type="text" class="form-control" id="no_identitas" name="no_identitas" required>
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="text" class="form-control" id="harga" name="harga" readonly>
                </div>
                <div class="mb-3">
                    <label for="kursi" class="form-label">Nomor Kursi</label>
                    <input type="text" class="form-control" id="kursi" name="kursi" required>
                </div>

                <!-- Menampilkan harga otomatis setelah memilih rute -->
               

                <button type="submit" class="btn btn-primary w-100">Pesan Tiket</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3">
        <p class="mb-0">&copy; <?= date("Y"); ?> Sistem Pemesanan Transportasi. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- JavaScript -->
    <script>
        function updateHarga() {
            var ruteSelect = document.getElementById('rute');
            var hargaInput = document.getElementById('harga');
            var selectedOption = ruteSelect.options[ruteSelect.selectedIndex];
            var harga = selectedOption.getAttribute('data-harga');
            hargaInput.value = harga ? 'Rp ' + parseInt(harga).toLocaleString('id-ID') : '';
        }
    </script>
</body>
</html>
