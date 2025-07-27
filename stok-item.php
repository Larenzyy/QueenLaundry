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
            <a href="dashboard-ad.php" class="nav-item">🧺 Dashboard</a>
            <a href="transaksi-ad.php" class="nav-item">📊 Transaksi</a>
            <a href="stok-item.php" class="nav-item">📁 Stok Item</a>
            <a href="index.php" class="nav-item">🔓 Log out</a>
    </div>
  </div>

   <div class="main-content">
  <h2>Stok Item</h2>
    <div class="form-transaksi">
    <div class="input-group" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 0px;">

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

                 echo "<td>
                        <a href='#' class='btn-edit' data-id='" . $row['id'] . "'>Edit</a>
                        <button class='btn-delete'>Hapus</button>
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

<script>
const modal = document.getElementById("itemModal");
  const form = document.getElementById("itemForm");
  const title = document.getElementById("modal-title");

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
</script>

</body>
</html>
