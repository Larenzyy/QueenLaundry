<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Queen Laundry Coin</title>
  <link rel="stylesheet" href="manajemen.css">
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
  <h2>Stok Item</h2>
    <div class="form-transaksi">
    <form action="proses_edit_item.php" method="POST">
      <div class="form-group">
        <label for="nama">Nama Item</label>
        <input type="text" id="nama" name="nama" required>
      </div>
      <div class="form-group">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori" required>
          <option value="">-- Pilih Kategori --</option>
          <option value="Bahan Laundry">Bahan Laundry</option>
          <option value="Aksesoris">Aksesoris</option>
        </select>
      </div>
      <div class="form-group">
        <label for="harga">Harga</label>
        <input type="number" id="harga" name="harga" required>
      </div>
      <div class="form-group">
        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok" required>
      </div>
      <div class="form-buttons">
        <button type="submit" class="btn-edit">Edit</button>
        <a href="manajemen.php" class="btn-kembali">Kembali</a>
      </div>
    </form>
  </div>
</div>
</div>
</body>
