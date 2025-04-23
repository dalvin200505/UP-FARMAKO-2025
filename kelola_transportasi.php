<?php
include 'koneksi.php';
$error = '';
$success = '';

// CREATE / INSERT
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $jenis_transportasi = $_POST['jenis_transportasi'];
    $kapasitas = $_POST['kapasitas'];

    if ($nama && $jenis_transportasi && $kapasitas) {
        $query = mysqli_query($con, "INSERT INTO transportasi (nama, jenis_transportasi, kapasitas) VALUES ('$nama', '$jenis_transportasi', '$kapasitas')");
        if ($query) {
            $success = "Data berhasil ditambahkan!";
            header("Location: kelola_rute.php"); // Redirect ke halaman kelola_rute.php
            exit;
        } else {
            $error = "Gagal menambahkan data!";
        }
    } else {
        $error = "Semua field wajib diisi!";
    }
}

// DELETE
// DELETE
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Cek apakah transportasi digunakan dalam rute
    $cek_rute = mysqli_query($con, "SELECT * FROM rute_transportasi WHERE id_transportasi = $id");
    if (mysqli_num_rows($cek_rute) > 0) {
        $error = "Gagal menghapus! Transportasi ini masih digunakan pada data rute.";
    } else {
        mysqli_query($con, "DELETE FROM transportasi WHERE id_transportasi=$id");
        $success = "Data berhasil dihapus!";
        // header("Location: kelola_rute.php");
header("Location: kelola_transportasi.php");
// ubah ke halaman ini, bukan ke kelola_rute
        exit;
    }
}


// UPDATE
if (isset($_POST['update'])) {
    $id_transportasi = $_POST['id_transportasi'];
    $nama = $_POST['nama'];
    $jenis_transportasi = $_POST['jenis_transportasi'];
    $kapasitas = $_POST['kapasitas'];

    mysqli_query($con, "UPDATE transportasi SET nama='$nama', jenis_transportasi='$jenis_transportasi', kapasitas='$kapasitas' WHERE id_transportasi=$id_transportasi");
    header("Location: kelola_rute.php"); // Redirect ke halaman kelola_rute.php setelah update
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Transportasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Kelola Data Transportasi</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- FORM TAMBAH -->
    <form method="POST" class="card p-4 shadow mb-4">
        <h5 class="mb-3">Tambah Transportasi</h5>
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control" name="nama" placeholder="Nama Transportasi" required>
            </div>
            <div class="mb-3">
                <label for="jenis_transportasi" class="form-label">Jenis Transportasi</label>
                <select class="form-control w-50" id="jenis_transportasi" name="jenis_transportasi" required>
                    <option value="">-- Pilih Jenis Kelas Kereta --</option>
                    <option value="pesawat">Kereta kelas Bisnis </option>
                    <option value="kereta">Kereta kelas Vip</option>
                    <option value="kapal">Kereta kelas ekonomi</option>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <input type="number" class="form-control" name="kapasitas" placeholder="Kapasitas" required>
            </div>
        </div>
        <button type="submit" name="simpan" class="btn btn-success mt-3">Simpan</button>
    </form>

    <!-- TABEL DATA -->
    <table class="table table-bordered table-striped table-hover shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Transportasi</th>
                <th>Jenis Transportasi</th>
                <th>Kapasitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = mysqli_query($con, "SELECT * FROM transportasi ORDER BY id_transportasi DESC");
            $no = 1; 
            while ($row = mysqli_fetch_assoc($result)) :
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['nama']; ?></td>
                <td><?= $row['jenis_transportasi']; ?></td>
                <td><?= $row['kapasitas']; ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_transportasi']; ?>">Edit</button>
                    <a href="?hapus=<?= $row['id_transportasi']; ?>" onclick="return confirm('Yakin hapus data ini?')" class="btn btn-danger btn-sm">Hapus</a>
                </td>
            </tr>

            <!-- MODAL EDIT -->
            <div class="modal fade" id="editModal<?= $row['id_transportasi']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Transportasi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_transportasi" value="<?= $row['id_transportasi']; ?>">
                                <div class="mb-3">
                                    <label>Nama Transportasi</label>
                                    <input type="text" class="form-control" name="nama" value="<?= $row['nama']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label>Jenis</label>
                                    <input type="text" class="form-control" name="jenis_transportasi" value="<?= $row['jenis_transportasi']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label>Kapasitas</label>
                                    <input type="number" class="form-control" name="kapasitas" value="<?= $row['kapasitas']; ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<a href="dashboard.php" class="btn btn-outline-secondary mt-3">← Kembali ke Dashboard</a>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
