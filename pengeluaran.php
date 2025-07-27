<?php
include 'config.php'; // Koneksi ke database
?>

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

    <div class="nav-container">
    <a href="dashboard.php" class="nav-item">🧺 Dashboard</a>
    <a href="transaksi.php" class="nav-item">📊 Transaksi</a>
    <a href="manajemen.php" class="nav-item">📁 Item</a>
    <a href="pengeluaran.php" class="nav-item">🚶 Pengeluaran</a>
    <a href="laporan.php" class="nav-item">📑 Laporan</a>
    <a href="#" id="btn-set-admin" class="nav-item">🛠️ Set Admin</a>
    <a href="index.php" class="nav-item">🔓 Log out</a>
</div>
  </div>
  
  <div class="main-content">
    <h2>Pengeluaran</h2>
    <div class="form-transaksi">
    <div class="input-group" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 20px;">

    <a href="tambah-pengeluaran.php" class="add-pengeluaran-btn" style="transform: translateY(0px);">+ Tambah Pengeluaran Laundry</a>


<form class="search-box" method="GET" style="position: relative; width: 350px;">
  <input type="text" id="search-keyword" name="keyword" placeholder="Cari..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>" style="width: 100%; padding-right: 100px; height: 36px;">
  <button class="search-btn" type="submit" id="btn-search" style="
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    height: 28px;
    padding: 0 8px;
    border: none;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    margin-bottom: 30px;
  ">🔍</button>
</form>
</div>
  
 <table class="transaksi-table">
        <thead>
          <tr>
           <th>#</th>
          <th>Pengeluaran</th>
          <th>Tanggal</th>
          <th>Qty</th>
          <th>Harga</th>
          <th>Catatan</th>
          <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
         $keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';
$whereClause = $keyword ? "WHERE t.pengeluaran LIKE '%$keyword%' OR t.catatan LIKE '%$keyword%'" : '';

$sql = "SELECT 
    t.id_luar AS id,
    t.pengeluaran,
    t.harga,
    t.tanggal,
    t.qty,
    t.catatan
    FROM pengeluaran t
    $whereClause
    ORDER BY id DESC";

          $result = $conn->query($sql);

          if (!$result) {
    die("Query Error: " . $conn->error);
}

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $no++ . "</td>";
              echo "<td>" . htmlspecialchars($row['pengeluaran']) . "</td>";
              echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
              echo "<td>" . number_format($row['qty'], 0, ',', '.') . "</td>";
              echo "<td>Rp. " . number_format($row['harga'], 0, ',', '.') . "</td>";
              echo "<td>" . htmlspecialchars($row['catatan']) . "</td>";

              $id_luar = $row['id'];

                 echo "<td>
                       <button class='btn-hapus' onclick='hapusLuar($id_luar)'>Hapus</button>
                    </td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='9' style='text-align:center;'>Belum ada transaksi</td></tr>";
          }
          ?>
      </tr>
    </tbody>
  </table>
</div>
</div>
</div>


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

<div id="modal-tambah-pengeluaran" class="modal">
  <div class="modal-content">
    <h3>Tambah Pengeluaran</h3>
    <form id="form-tambah-pengeluaran">
      <label for="pengeluaran">Pengeluaran:</label>
      <input type="text" id="pengeluaran" name="pengeluaran" required>

      <label for="tanggal">Tanggal:</label>
      <input type="date" id="tanggal" name="tanggal" required>

      <label for="harga">Qty:</label>
      <input type="number" id="qty" name="qty" required>

      <label for="harga">Harga:</label>
      <input type="number" id="harga" name="harga" required>

      <label for="catatan">Catatan:</label>
      <textarea id="catatan" name="catatan" rows="3"></textarea>

      <div class="modal-buttons">
        <button type="submit" class="bton-ok">OK</button>
        <button type="button" class="bton-cancel">Cancel</button>
      </div>
    </form>
  </div>
</div>

<div id="popup-hapus" class="modal" style="display:none;">
  <div class="modal-content">
    <p style="margin-bottom: 20px; font-weight: 600;">Yakin ingin menghapus transaksi ini?</p>
    <div class="modal-buttons" style="justify-content: center;">
      <button class="bton-ok" onclick="prosesHapus()">🗑️ Hapus</button>
      <button class="bton-cancel" onclick="tutupPopupHapus()">❌ Batal</button>
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
  // Contoh kirim data via fetch ke PHP (buat proses simpan di server)
  fetch('save_admin.php', {
    method: 'POST',
    body: data,
  })
  .then(res => res.text())
  .then(response => {
    alert(response); // Bisa ganti sesuai response server
    modalSetAdmin.style.display = 'none';
    formSetAdmin.reset();
  })
  .catch(() => alert('Gagal menyimpan data admin'));
});


document.getElementById('search-keyword').addEventListener('keydown', function (e) {
  if (e.key === 'Enter') {
    document.getElementById('btn-search').click();
  }
});
</script>

<script>
const btnTambahPengeluaran = document.querySelector('.add-pengeluaran-btn');
const modalPengeluaran = document.getElementById('modal-tambah-pengeluaran');
const formPengeluaran = document.getElementById('form-tambah-pengeluaran');
const cancelPengeluaran = modalPengeluaran.querySelector('.bton-cancel');

// Tampilkan popup saat tombol diklik
btnTambahPengeluaran.addEventListener('click', (e) => {
  e.preventDefault();
  modalPengeluaran.style.display = 'flex';
});

// Cancel atau klik luar modal
cancelPengeluaran.addEventListener('click', () => {
  modalPengeluaran.style.display = 'none';
  formPengeluaran.reset();
});
window.addEventListener('click', (e) => {
  if (e.target === modalPengeluaran) {
    modalPengeluaran.style.display = 'none';
    formPengeluaran.reset();
  }
});

// Submit form pengeluaran
formPengeluaran.addEventListener('submit', (e) => {
  e.preventDefault();
  const data = new FormData(formPengeluaran);
  fetch('save_pengeluaran.php', {
    method: 'POST',
    body: data
  })
  .then(res => res.text())
  .then(response => {
    alert(response);
    modalPengeluaran.style.display = 'none';
    formPengeluaran.reset();
    location.reload(); // reload supaya tabel update
  })
  .catch(() => alert('Gagal menyimpan pengeluaran.'));
});

   let idLuarHapus = null;

function hapusLuar(id_luar) {
  idLuarHapus = id_luar;
  document.getElementById('popup-hapus').style.display = 'flex';
}


function tutupPopupHapus() {
  idLuarHapus = null;
  document.getElementById('popup-hapus').style.display = 'none';
}

function prosesHapus() {
  fetch(`hapus_pengeluaran.php?id=${idLuarHapus}`)
    .then(res => res.text())
    .then(response => {
      if (response === 'success') {
        location.reload();
      } else {
        alert('Gagal menghapus transaksi');
      }
    })
    .catch(err => alert('Terjadi kesalahan: ' + err))
    .finally(() => {
      tutupPopupHapus();
    });
}
</script>
</body>
</html>