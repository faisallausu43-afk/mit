<?php
include 'config.php';
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Gunakan prepared statement untuk mencegah SQL Injection
if ($search) {
    $like_search = "%" . $search . "%";
    $stmt = $koneksi->prepare("SELECT * FROM mahasiswa WHERE nama LIKE ? OR nim LIKE ?");
    $stmt->bind_param("ss", $like_search, $like_search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $koneksi->query("SELECT * FROM mahasiswa");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>List Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
    <?php include 'sidebar.php'; ?>
    <div class="content p-4">
        <div class="card shadow p-4">
            <h3 class="mb-3">Daftar Mahasiswa</h3>

            <form method="get" action="" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Cari nama atau NIM..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>

            <div class="mb-3">
                <a href="tambah_mahasiswa.php" class="btn btn-success">
                    <i class="bi bi-person-plus-fill"></i> Tambah Mahasiswa
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nim'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['alamat'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="edit_mahasiswa.php?nim=<?= urlencode($row['nim']) ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="admin_dashboard.php" class="btn btn-warning mb-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</body>

</html>
