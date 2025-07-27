<?php
include 'config.php'; // Koneksi ke database
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Transaksi - Queen Laundry Coin</title>
  <link rel="stylesheet" href="transaksi.css">
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
     <h2>Riwayat Transaksi </h2>
     <div class="form-transaksi">
        <div class="input-group" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 0px;">
          <div class="form-buttons" style="transform: translateY(-18px);">
            <button id="btn-lunass" class="btn-green">Lunas</button>
            <button id="btn-belumm" class="btn-red">Belum Lunas</button>
            <button id="btn-semuaa" class="btn-default" style="margin-left:3px;"></button>
          </div>

          <div class="search-box" style="position: relative; width: 350px;">
            <input
              id="search-keywordd"
              type="text"
              placeholder="Cari..."
              style="width: 100%; padding-right: 40px; height: 36px;"
            />
            <button
              id="btn-searchh"
              class="search-btn"
              style="
                position: absolute;
                right: 5px;
                top: 50%;
                transform: translateY(-43%);
                height: 28px;
                padding: 0 8px;
                border: none;
                color: white;
                border-radius: 4px;
                cursor: pointer;
              "
            >
              🔍
            </button>
          </div>
        </div>

        <table class="transaksi-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Pelanggan</th>
              <th>Tgl</th>
              <th>Layanan</th>
              <th>🧺</th>
              <th>kg</th>
              <th>Opsi</th>
              <th>Item</th>
              <th>Total</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $start = ($page - 1) * $limit;

            $sql_count = "SELECT COUNT(*) AS total FROM dsbrd_kasir";
            $result_count = $conn->query($sql_count);
            $row_count = $result_count->fetch_assoc();
            $total_data = $row_count['total'];
            $total_page = ceil($total_data / $limit);

            $no = $start + 1;  // supaya nomor sesuai halaman
            $sql = "SELECT 
                      t.id_kasir AS id,
                      t.pelanggan,
                      t.jenis_laundry,
                      t.mesin,
                      t.berat,
                      t.opsi,   
                      t.total,
                      t.status,
                      MAX(dt.tanggal) AS tanggal,
                      GROUP_CONCAT(i.nama_item SEPARATOR '<br>') AS item
                    FROM dsbrd_kasir t
                    LEFT JOIN detail_kasir dt ON dt.id_kasir = t.id_kasir
                    LEFT JOIN item i ON i.id_item = dt.id_item
                    GROUP BY t.id_kasir, t.pelanggan, t.jenis_laundry, t.mesin, t.berat, t.opsi, t.total, t.kembali, t.status
                    ORDER BY tanggal DESC
                    LIMIT $start, $limit";

            $result = $conn->query($sql);

            if (!$result) {
                die("Query Error: " . $conn->error);
            }

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['pelanggan']) . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['jenis_laundry']) . "</td>";
                    echo "<td>" . number_format($row['mesin'], 0, ',', '.') . "</td>";
                    echo "<td>" . number_format($row['berat'], 0, ',', '.') . "</td>";
                    echo "<td>" . htmlspecialchars($row['opsi']) . "</td>";
                    echo "<td>" . $row['item'] . "</td>";
                    echo "<td>Rp. " . number_format($row['total'], 0, ',', '.') . "</td>";

                    $status = $row['status'];
                    $id_kasir = $row['id'];
                    if ($status === 'Lunas') {
                        echo "<td><span class='status-lunas' style='font-size: 12px;'>Lunas</span></td>";
                    } else {
                        echo "<td><span class='status-belum' style='cursor:pointer; font-size: 12px; white-space: nowrap;' onclick='konfirmasiLunas($id_kasir, this)'>Belum Lunas</span></td>";
                    }

                    echo "<td>
                             <button class='btn-cetak' onclick='showStruk($id_kasir)'>Cetak</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11' style='text-align:center;'>Belum ada transaksi</td></tr>";
            }
            ?>
          </tbody>
        </table>

        <!-- Navigasi halaman -->
        <div style="margin-top: 20px; text-align: center;">
          <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" style="margin-right: 10px;">&laquo;</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <?php $active = ($i == $page) ? "font-weight:bold; color:blue;" : ""; ?>
            <a href="?page=<?= $i ?>" style="margin: 0 5px; <?= $active ?>"><?= $i ?></a>
          <?php endfor; ?>

          <?php if ($page < $total_page): ?>
            <a href="?page=<?= $page + 1 ?>" style="margin-left: 10px;">&raquo;</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div id="popup" class="popup">
    <div class="popup-content">
      <p style="margin-bottom: 20px; font-weight: 600;">Apakah sudah lunas?</p>
      <div class="popup-buttons">
        <button class="btn-ya" onclick="prosesLunas()">✅ Sudah</button>
        <button class="btn-tidak" onclick="tutupPopup()">❌ Belum</button>
      </div>
    </div>
  </div>


  <script>
    let kasirIdToUpdate = null;
    let spanElement = null;

    function konfirmasiLunas(id, element) {
      kasirIdToUpdate = id;
      spanElement = element;
      document.getElementById('popup').style.display = 'block';
    }

    function tutupPopup() {
      document.getElementById('popup').style.display = 'none';
      kasirIdToUpdate = null;
    }

    function prosesLunas() {
      fetch("update_status.php?id=" + kasirIdToUpdate)
        .then(res => res.text())
        .then(response => {
          if (response === "success") {
            spanElement.className = 'status-lunas';
            spanElement.innerText = 'Lunas';
            spanElement.style.cursor = 'default';
            tutupPopup();
          } else {
            alert("Gagal mengubah status.");
          }
        })
        .catch(err => alert("Error: " + err));
    }

    // Search button & filter
    document.getElementById('btn-lunass').addEventListener('click', () => filterTransaksi('Lunas'));
    document.getElementById('btn-belumm').addEventListener('click', () => filterTransaksi('Belum Lunas'));
    document.getElementById('btn-semuaa').addEventListener('click', () => filterTransaksi('')); // tanpa filter status

    document.getElementById('btn-searchh').addEventListener('click', () => {
      const keyword = document.getElementById('search-keywordd').value.trim();
      filterTransaksi('', keyword);
    });

    function filterTransaksi(status = '', keyword = '') {
      let url = `filter_transaksi.php?status=${encodeURIComponent(status)}&keyword=${encodeURIComponent(keyword)}`;
      fetch(url)
        .then(res => res.text())
        .then(data => {
          document.querySelector('.transaksi-table tbody').innerHTML = data;
        })
        .catch(err => alert("Error saat memuat data: " + err));
    }

    const buttons = ['btn-lunass', 'btn-belumm', 'btn-semuaa'];
    buttons.forEach(id => {
      document.getElementById(id).addEventListener('click', () => {
        buttons.forEach(bid => document.getElementById(bid).classList.remove('active'));
        document.getElementById(id).classList.add('active');
      });
    });

    document.getElementById('search-keywordd').addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        document.getElementById('btn-search').click();
      }
    });

        let strukHTML = '';
let idKasirGlobal = null;

function showStruk(id_kasir) {
  idKasirGlobal = id_kasir;
  fetch(`struk_transaksi.php?id=${id_kasir}`)
    .then(res => res.text())
    .then(data => {
      strukHTML = data;
      document.getElementById('struk-content').innerHTML = data;
      document.getElementById('popup-struk').style.display = 'flex';
    })
    .catch(err => alert("Gagal memuat struk: " + err));
}

function tutupStruk() {
  document.getElementById('popup-struk').style.display = 'none';
  document.getElementById('struk-content').innerHTML = '';
  idKasirGlobal = null;
}

function unduhStruk() {
  const blob = new Blob([strukHTML], { type: 'text/html' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `struk-transaksi-${idKasirGlobal}.html`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
  </script>

    <div id="popup-struk" class="popup" style="display:none;">
  <div class="popup-content" style="width: 350px; min-height: 110vh; overflow-y: auto;">
    <h3 style="text-align: center;">🧾 Struk Transaksi</h3>
    <div id="struk-content" style="text-align: justify; margin-top: 15px; font-family: monospace; font-size: 14px;"></div>
    <div style="text-align: center; margin-top: 20px;">
      <button onclick="unduhStruk()" class="bton-ok">⬇️ Unduh</button>
      <button onclick="tutupStruk()" class="bton-cancel">❌ Batal</button>
    </div>
  </div>
</div>
</body>
</html>
