<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['simpanA'])) {
        $usn = trim($_POST['username']);
        $pw = $_POST['password'];
        $nm = trim($_POST['nama']);

        // Enkripsi password
        $passwordHash = password_hash($pw, PASSWORD_DEFAULT);

        // Pakai prepared statement untuk keamanan
        $stmt = $conn->prepare("INSERT INTO lgn_admin (username, password, nama_lengkap) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usn, $passwordHash, $nm);

        if ($stmt->execute()) {
            echo "<script>alert('Admin berhasil ditambahkan!'); window.location.href='setting.php';</script>";
            exit();
        } else {
            echo "<script>alert('Gagal menyimpan admin: " . addslashes($stmt->error) . "');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Queen Laundry Coin</title>
  <link rel="stylesheet" href="setting.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="wrapper">
  <div class="sidebar">
    <img src="logo.png" alt="Logo Queen Laundry">
    <h2>Queen Laundry Coin</h2>
    <div class="nav-container">
    <a href="dashboard.php" class="nav-item">🧺 Dashboard</a>
    <a href="transaksi.php" class="nav-item">📊 Transaksi</a>
    <a href="manajemen.php" class="nav-item">📁 Item</a>
    <a href="pengeluaran.php" class="nav-item">🚶  Pengeluaran</a>
    <a href="laporan.php" class="nav-item">📑 Laporan</a>
    <a href="setting.php" class="nav-item">🛠️ Set Admin</a>
    <a href="index.php" class="nav-item">🔓 Log out</a>
</div>
</div>

  <div class="main-content">
      <h2>Setting Karyawan</h2>
    <div class="form-transaksi">
      <form action="" method="POST">
        <div class="form-group">
          <label for="username">Username Admin</label>
          <input type="text" id="username" name="username" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required />
        </div>

        <div class="form-group">
          <label for="nama">Nama Lengkap</label>
          <input type="text" id="nama" name="nama" required />
        </div>

        <div class="form-buttons">
          <button type="submit" name="simpanA" class="btn-tambah">Simpan Admin</button>
          <a href="dashboard.php" class="btn-kembali">Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
