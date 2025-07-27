<?php
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Kasir</title>
  <link rel="stylesheet" href="transaksi.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="wrapper">
    <div class="sidebar">
      <img src="logo.png" alt="Logo Queen Laundry">
      <h2>Queen Laundry Coin</h2>
      <a href="dashboard-ad.php" class="nav-item">🧺 Dashboard</a>
      <a href="transaksi-ad.php" class="nav-item">📊 Transaksi</a>
      <a href="stok-item.php" class="nav-item">📁 Stok Item</a>
      <a href="index.php" class="nav-item">🔓 Log out</a>
    </div>

    <div class="main-content">
      <h2>Dashboard Kasir</h2>
      <div class="form-transaksi">
        <form action="#" method="POST">
          <div class="form-grid">
            <div class="form-left">
              <label>Nama Pelanggan:
                <input type="text" name="nama" required />
              </label>
              <label>Tanggal:
                <input type="date" name="tanggal" required />
              </label>
              <label>Jenis Laundry:
                <select name="jenis" required>
                  <option value="">-- Pilih Jenis --</option>
                  <option value="Cuci Kering">Cuci Kering</option>
                  <option value="Cuci Basah">Cuci Basah</option>
                </select>
              </label>
              <label>Berat (kg):
                <input type="number" name="berat" step="0.1" required />
              </label>
              <label>Tambah Item:
                <select name="item">
                  <option value="">-- Pilih Item --</option>
                  <option value="Setrika">Setrika</option>
                  <option value="Lipatan">Lipatan</option>
                </select>
              </label>
            </div>

            <div class="form-right">
              <label>Total Bayar:
                <input type="text" name="total" readonly />
              </label>
              <label>Bayar:
                <div class="input-group">
                  <input type="text" name="bayar" />
                  <button type="button" class="btn-ok">OK</button>
                </div>
              </label>
              <label>Kembali:
                <input type="text" name="kembali" readonly />
              </label>
              <label>Status:
                <select name="status">
                  <option value="Belum Lunas">Belum Lunas</option>
                  <option value="Lunas">Lunas</option>
                </select>
              </label>
            </div>
          </div>

          <div class="form-buttons">
            <button type="submit" class="btn-tambah">Tambah</button>
            <a href="transaksi.php" class="btn-kembali">Kembali</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>