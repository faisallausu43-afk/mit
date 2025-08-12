<?php
include 'config.php';
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['nim'])) {
    header("Location: list_mahasiswa.php");
    exit;
}

$nim = $_GET['nim'];

// Validasi format NIM (misalnya hanya angka, max 20 digit)
if (!preg_match('/^[0-9]{1,20}$/', $nim)) {
    header("Location: list_mahasiswa.php?error=invalid_nim");
    exit;
}

// Gunakan prepared statement (hindari SQL Injection & validasi IDOR)
$stmt = $koneksi->prepare("SELECT * FROM mahasiswa WHERE nim = ?");
$stmt->bind_param("s", $nim);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: list_mahasiswa.php?error=not_found");
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = $_POST['nama'];
    $email  = $_POST['email'];
    $alamat = $_POST['alamat'];
    $status = $_POST['status'];

    $stmt = $koneksi->prepare("UPDATE mahasiswa SET nama=?, email=?, alamat=?, status=? WHERE nim=?");
    $stmt->bind_param("sssss", $nama, $email, $alamat, $status, $nim);
    $stmt->execute();
    $stmt->close();

    header("Location: list_mahasiswa.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
    <?php include 'sidebar.php'; ?>
    <div class="content p-4">
        <div class="card shadow p-4">
            <h3 class="mb-3">Edit Data Mahasiswa</h3>

            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-info">
                    <?= htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label for="nim" class="form-label">NIM</label>
                    <input type="text" class="form-control" id="nim" value="<?= htmlspecialchars($data['nim'], ENT_QUOTES, 'UTF-8') ?>" disabled />
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($data['nama'], ENT_QUOTES, 'UTF-8') ?>" required />
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8') ?>" required />
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="alamat" name="alamat" value="<?= htmlspecialchars($data['alamat'], ENT_QUOTES, 'UTF-8') ?>" required />
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <input type="text" class="form-control" id="status" name="status" value="<?= htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8') ?>" required />
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="list_mahasiswa.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</body>

</html>
