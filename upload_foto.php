<?php
include 'config.php';
session_start();

if (!isset($_SESSION['nim'])) {
    header("Location: index.php");
    exit;
}

$nim = $_SESSION['nim'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';

        // Pastikan folder ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Validasi ukuran file (maks 2 MB)
        if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            $error = "Ukuran file terlalu besar. Maksimal 2MB.";
        } else {
            // Validasi tipe MIME
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['foto']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime_type, $allowed_types)) {
                $error = "Format file tidak didukung. Hanya JPG, PNG, dan GIF.";
            } else {
                // Tentukan ekstensi aman
                $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $new_filename = uniqid('foto_', true) . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;

                // Pindahkan file
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                    $db_path = 'uploads/' . $new_filename;
                    $stmt = $koneksi->prepare("UPDATE mahasiswa SET foto = ? WHERE nim = ?");
                    $stmt->bind_param("ss", $db_path, $nim);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $message = "Foto profil berhasil diperbarui.";
                    } else {
                        $error = "Gagal memperbarui database.";
                    }
                    $stmt->close();
                } else {
                    $error = "Gagal mengupload file.";
                }
            }
        }
    } else {
        $error = "Silakan pilih file untuk diupload.";
    }
}

$stmt = $koneksi->prepare("SELECT * FROM mahasiswa WHERE nim = ?");
$stmt->bind_param("s", $nim);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Upload Foto</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
  <?php include 'sidebar.php'; ?>
  <div class="content p-4">
    <div class="card shadow p-4">
      <h3 class="mb-3">Upload Foto Profil</h3>
      
      <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
      
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="text-center">
            <img src="<?= htmlspecialchars($user_data['foto']) ?>" class="img-fluid rounded" style="max-height: 200px;" alt="Foto Profil">
            <p class="mt-2">Foto Profil Saat Ini</p>
          </div>
        </div>
        <div class="col-md-8">
          <form method="post" action="" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="foto" class="form-label">Pilih Foto Baru</label>
              <input type="file" class="form-control" id="foto" name="foto" required>
              <div class="form-text">Format yang didukung: JPG, PNG, GIF, dll.</div>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
          </form>
        </div>
      </div>
      
      <a href="pengguna.php" class="btn btn-secondary">Kembali</a>
    </div>
  </div>
</body>

</html>