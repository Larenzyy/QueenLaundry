<?php
include 'config.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$nama_lengkap = $_POST['nama_lengkap'] ?? '';

if (!$username || !$password || !$nama_lengkap ) {
    echo "Semua field harus diisi.";
    exit;
}


// Pastikan tabel `admin` sudah ada di database
$sql = "INSERT INTO lgn_admin (username, password, nama_lengkap) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $password, $nama_lengkap);

if ($stmt->execute()) {
    echo "Admin berhasil disimpan.";
} else {
    echo "Gagal menyimpan admin: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
