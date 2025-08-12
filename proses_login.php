<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // ====== LOGIN ADMIN ======
    $stmt = $koneksi->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['username'] = $admin['username'];
        $_SESSION['is_admin'] = true;
        header("Location: admin_dashboard.php");
        exit;
    }
    $stmt->close();

    // ====== LOGIN MAHASISWA ======
    $stmt = $koneksi->prepare("SELECT * FROM mahasiswa WHERE nim = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $mahasiswa = $result->fetch_assoc();
        $_SESSION['nim'] = $mahasiswa['nim'];
        $_SESSION['nama'] = $mahasiswa['nama'];
        $_SESSION['email'] = $mahasiswa['email'];
        $_SESSION['alamat'] = $mahasiswa['alamat'];
        $_SESSION['status'] = $mahasiswa['status'];
        $_SESSION['foto'] = $mahasiswa['foto'];
        $_SESSION['is_admin'] = false;
        header("Location: dashboard.php");
        exit;
    }
    $stmt->close();

    // Jika login gagal
    header("Location: index.php?error=1");
    exit;
}

header("Location: index.php");
exit;
?>
