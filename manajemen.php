<?php
include 'config.php'; // Koneksi ke database

if (isset($_POST['simpan_item'])) {
    $id = $_POST['id_item'];
    $nama = $_POST['nama_item'];
    $harga = $_POST['harga'];
    $jumlah = $_POST['jumlah'];

    if ($id) {
        // Update
        $stmt = $conn->prepare("UPDATE item SET nama_item=?, harga=?, jumlah=? WHERE id_item=?");
        $stmt->bind_param("siii", $nama, $harga, $jumlah, $id);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO item (nama_item, harga, jumlah) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $nama, $harga, $jumlah);
    }

    if ($stmt->execute()) {
        echo "";
    } else {
        echo "Gagal menyimpan item: " . $stmt->error;
    }
}
?>


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
  <h2>Manajemen Item</h2>
    <div class="form-transaksi">
    <div class="input-group" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 0px;">

    <a href="tambah-item.php" class="add-item-btn" style="transform: translateY(-10px);">+ Tambah Item</a>

</div>

  <table class="item-table">
        <thead>
          <tr>
            <th>#</th>
        <th>Nama Item</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
         $sql = "SELECT 
                t.id_item AS id,
                t.nama_item,
                t.jumlah,
                t.harga
                FROM item t
                ORDER BY id DESC
                ";
          $result = $conn->query($sql);

          if (!$result) {
    die("Query Error: " . $conn->error);
}

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $no++ . "</td>";
              echo "<td>" . htmlspecialchars($row['nama_item']) . "</td>";
              echo "<td>Rp. " . number_format($row['harga'], 0, ',', '.') . "</td>";
              echo "<td>" . htmlspecialchars($row['jumlah']) . "</td>";

              $id_item = $row['id'];

                 echo "<td>
                        <a href='#' class='btn-edit' data-id='" . $row['id'] . "'>Edit</a>
                      <button class='btn-delete' onclick='hapusItem($id_item)'>Hapus</button>
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

<!-- Modal Tambah/Edit Item -->
<div id="itemModal" class="modal" style="display:none;">
  <div class="modal-content">
    <h3 id="modal-title">Tambah Item</h3>
    <form id="itemForm" method="POST">
      <input type="hidden" name="id_item" id="id_item">
      <label>Nama Item:</label>
      <input type="text" name="nama_item" id="nama_item" required><br>
      <label>Harga:</label>
      <input type="number" name="harga" id="harga" required><br>
      <label>Jumlah:</label>
      <input type="number" name="jumlah" id="jumlah" required><br>
      <div class="modal-buttons">
        <button type="submit" name="simpan_item" class="bton-ok">OK</button>
        <button type="button" class="bton-cancel" onclick="closeModal()">Cancel</button>
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
  
  const modal = document.getElementById("itemModal");
  const form = document.getElementById("itemForm");
  const title = document.getElementById("modal-title");

  function openAddModal() {
    form.reset();
    title.innerText = "Tambah Item";
    document.getElementById("id_item").value = "";
    modal.style.display = "flex";
  }

  function openEditModal(id, nama, harga, jumlah) {
    title.innerText = "Edit Item";
    document.getElementById("id_item").value = id;
    document.getElementById("nama_item").value = nama;
    document.getElementById("harga").value = harga;
    document.getElementById("jumlah").value = jumlah;
    modal.style.display = "flex";
  }

  function closeModal() {
    modal.style.display = "none";
  }

  // Tangkap tombol tambah
  document.querySelector(".add-item-btn").addEventListener("click", function(e) {
    e.preventDefault();
    openAddModal();
  });

  // Tangkap semua tombol edit
  document.querySelectorAll(".btn-edit").forEach(function(button) {
    button.addEventListener("click", function(e) {
      e.preventDefault();
      const row = this.closest("tr");
      const id = this.dataset.id;
      const nama = row.children[1].innerText;
      const harga = row.children[2].innerText.replace(/[^\d]/g, '');
      const jumlah = row.children[3].innerText;
      openEditModal(id, nama, harga, jumlah);
    });
  });

  let idItemHapus = null;

function hapusItem(id_item) {
  idItemHapus = id_item;
  document.getElementById('popup-hapus').style.display = 'flex';
}


function tutupPopupHapus() {
  idItemHapus = null;
  document.getElementById('popup-hapus').style.display = 'none';
}

function prosesHapus() {
  fetch(`hapus_item.php?id=${idItemHapus}`)
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
