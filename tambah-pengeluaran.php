<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Transaksi - Queen Laundry Coin</title>
  <link rel="stylesheet" href="pengeluaran.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="wrapper">
  <div class="sidebar">
    <img src="logo.png" alt="Logo Queen Laundry">
    <h2>Queen Laundry Coin</h2>
    <a href="dashboard.php" class="nav-item">🧺 Dashboard</a>
    <a href="transaksi.php" class="nav-item">📊 Transaksi</a>
    <a href="manajemen.php" class="nav-item">📁 Manajemen Item</a>
    <a href="pengeluaran.php" class="nav-item">🚶 Data Pengeluaran</a>
    <a href="laporan.php" class="nav-item">📑 Data Laporan</a>
    <a href="setting.php" class="nav-item">🛠️ Setting Karyawan</a>
    <a href="index.php" class="nav-item">🔓 Log out</a>
  </div>

  <div class="main-content">
  <h1>Tambah Pengeluaran</h1>
  <div class="form-container">
    <form action="proses_tambah_pengeluaran.php" method="POST">
      <div class="form-group">
        <label for="pengeluaran">Pengeluaran</label>
        <input type="text" id="pengeluaran" name="pengeluaran" required>
      </div>
      <div class="form-group">
        <label for="tanggal">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" required>
      </div>
      <div class="form-group">
        <label for="harga">Harga</label>
        <input type="number" id="harga" name="harga" required>
      </div>
      <div class="form-group">
        <label for="catatan">Catatan</label>
        <textarea id="catatan" name="catatan" rows="3"></textarea>
      </div>
      <div class="form-button">
        <button type="submit" class="btn-tambah">Tambah</button>
        <a href="pengeluaran.php" class="btn-kembali">Kembali</a>
      </div>
    </form>
  </div>
</div>
</div>
</body>