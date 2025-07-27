<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Queen Laundry Coin</title>
  <link rel="stylesheet" href="laporan.css">
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
      <a href="#" id="btn-set-admin" class="nav-item">🛠️ Set Admin</a>
      <a href="index.php" class="nav-item">🔓 Log out</a>
    </div>
  </div>

  <div class="main-content">
    <h2>Laporan</h2>
    <div class="form-transaksi">
      <div class="filter-tanggal">
        <form method="get" style="display: inline-flex; align-items: center; gap: 8px;">
          <button type="button" class="btn-cetak">Cetak Laporan</button>
          <label for="tanggal-awal" style="padding-left: 20px;">Tanggal Awal</label>
          <input type="date" id="tanggal-awal" name="tanggal_awal" value="<?= $_GET['tanggal_awal'] ?? '' ?>">
          <label for="tanggal-akhir">Tanggal Akhir</label>
          <input type="date" id="tanggal-akhir" name="tanggal_akhir" value="<?= $_GET['tanggal_akhir'] ?? '' ?>">
          <button type="submit" class="btn-search">🔍</button>
        </form>
      </div>

      <table class="tabel-laporan">
        <thead>
          <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Catatan</th>
            <th>Pemasukan</th>
            <th>Pengeluaran</th>
          </tr>
        </thead>
        <tbody id="laporan-body">
          <?php
          $tgl_awal = $_GET['tanggal_awal'] ?? null;
          $tgl_akhir = $_GET['tanggal_akhir'] ?? null;

          $filter = "";
          if ($tgl_awal && $tgl_akhir) {
              $filter = "WHERE dk.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
          }

          $query_pemasukan = "
              SELECT dk.tanggal, SUM(k.total) AS total_pemasukan, COUNT(DISTINCT dk.id_kasir) AS jumlah_transaksi
              FROM detail_kasir dk
              JOIN dsbrd_kasir k ON dk.id_kasir = k.id_kasir
              $filter
              GROUP BY dk.tanggal
          ";

          $query_pengeluaran = "
              SELECT DATE(tanggal) AS tanggal, COUNT(*) AS jumlah_pengeluaran, SUM(harga) AS total_pengeluaran
              FROM pengeluaran
              " . ($tgl_awal && $tgl_akhir ? "WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'" : "") . "
              GROUP BY DATE(tanggal)
          ";

          $result_pemasukan = mysqli_query($conn, $query_pemasukan) or die("Query pemasukan error: " . mysqli_error($conn));
          $result_pengeluaran = mysqli_query($conn, $query_pengeluaran) or die("Query pengeluaran error: " . mysqli_error($conn));

          $data_pengeluaran = [];
          while ($row = mysqli_fetch_assoc($result_pengeluaran)) {
              $tgl = $row['tanggal'];
              $data_pengeluaran[$tgl] = [
                  'jumlah_pengeluaran' => $row['jumlah_pengeluaran'],
                  'total_pengeluaran' => $row['total_pengeluaran']
              ];
          }

          $total_pemasukan = 0;
          $total_pengeluaran = 0;
          $no = 1;

          while ($row = mysqli_fetch_assoc($result_pemasukan)) {
              $tanggal = $row['tanggal'];
              $jumlah_transaksi = $row['jumlah_transaksi'];
              $pemasukan = $row['total_pemasukan'];
              $pengeluaran = 0;
              $jumlah_pengeluaran = 0;

              if (isset($data_pengeluaran[$tanggal])) {
                  $pengeluaran = $data_pengeluaran[$tanggal]['total_pengeluaran'];
                  $jumlah_pengeluaran = $data_pengeluaran[$tanggal]['jumlah_pengeluaran'];
              }

              $catatan = "{$jumlah_transaksi} transaksi masuk";
              if ($jumlah_pengeluaran > 0) {
                  $catatan .= ", {$jumlah_pengeluaran} pengeluaran";
              }

              $keterangan = ($jumlah_pengeluaran > 0) ? 'Transaksi dan Pengeluaran' : 'Transaksi';

              echo "<tr>
                  <td>{$no}</td>
                  <td>{$tanggal}</td>
                  <td>{$keterangan}</td>
                  <td>{$catatan}</td>
                  <td>Rp. " . number_format($pemasukan, 0, ',', '.') . "</td>
                  <td>Rp. " . number_format($pengeluaran, 0, ',', '.') . "</td>
              </tr>";

              $total_pemasukan += $pemasukan;
              $total_pengeluaran += $pengeluaran;
              $no++;
          }
          ?>
        </tbody>
        <tfoot id="laporan-total">
          <tr>
            <td colspan='4'><strong>Total</strong></td>
            <td><strong>Rp. <?= number_format($total_pemasukan, 0, ',', '.') ?></strong></td>
            <td><strong>Rp. <?= number_format($total_pengeluaran, 0, ',', '.') ?></strong></td>
          </tr>
          <tr>
            <td colspan='4'><strong>Saldo Akhir</strong></td>
            <td colspan='2'><strong>Rp. <?= number_format($total_pemasukan - $total_pengeluaran, 0, ',', '.') ?></strong></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<!-- Modal Set Admin -->
<div id="modal-set-admin" class="modal">
  <div class="modal-content">
    <h3>Set Admin</h3>
    <form id="form-set-admin">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
      <label for="nama_lengkap">Nama Lengkap:</label>
      <input type="text" id="nama_lengkap" name="nama_lengkap" required>
      <div class="modal-buttons">
        <button type="submit" class="bton-ok">OK</button>
        <button type="button" class="bton-cancel">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Cetak -->
<div id="modal-cetak" class="modal">
  <div class="modal-content">
    <h3>Cetak</h3>
    <p style="margin-top: 10px; align-text: center;">Unduh Laporan?</p>
    <div class="modal-buttons">
      <a id="btn-download-pdf" href="#" class="bton-ok" target="_blank" style="font-size: 12px;">PDF</a>
      <button type="button" class="bton-cancel" onclick="tutupModalCetak()">Batal</button>
    </div>
  </div>
</div>

<script>
const btnSetAdmin = document.getElementById('btn-set-admin');
const modalSetAdmin = document.getElementById('modal-set-admin');
const btonCancel = modalSetAdmin.querySelector('.bton-cancel');
const formSetAdmin = document.getElementById('form-set-admin');

btnSetAdmin.addEventListener('click', (e) => {
  e.preventDefault();
  modalSetAdmin.style.display = 'flex';
});

btonCancel.addEventListener('click', () => {
  modalSetAdmin.style.display = 'none';
  formSetAdmin.reset();
});

window.addEventListener('click', (e) => {
  if (e.target === modalSetAdmin) {
    modalSetAdmin.style.display = 'none';
    formSetAdmin.reset();
  }
});

formSetAdmin.addEventListener('submit', (e) => {
  e.preventDefault();
  const data = new FormData(formSetAdmin);
  fetch('save_admin.php', {
    method: 'POST',
    body: data,
  })
  .then(res => res.text())
  .then(response => {
    alert(response);
    modalSetAdmin.style.display = 'none';
    formSetAdmin.reset();
  })
  .catch(() => alert('Gagal menyimpan data admin'));
});

const btnCetak = document.querySelector('.btn-cetak');
const modalCetak = document.getElementById('modal-cetak');
const btnDownloadPDF = document.getElementById('btn-download-pdf');

btnCetak.addEventListener('click', () => {
  const tglAwal = document.getElementById('tanggal-awal').value;
  const tglAkhir = document.getElementById('tanggal-akhir').value;

  if (!tglAwal || !tglAkhir) {
    alert('Harap pilih tanggal awal dan akhir terlebih dahulu.');
    return;
  }

  btnDownloadPDF.href = `cetak_laporan.php?tanggal_awal=${tglAwal}&tanggal_akhir=${tglAkhir}`;
  modalCetak.style.display = 'flex';
});

function tutupModalCetak() {
  modalCetak.style.display = 'none';
}
</script>
</body>
</html>
