<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pelanggan = $_POST['pelanggan'];
    $jenis = $_POST['jenis'];
    $mesin = intval($_POST['mesin']);
    $berat = floatval($_POST['berat']);
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];

    // Tambahan layanan
    $setrika = isset($_POST['setrika']) ? 1 : 0;
    $lipat = isset($_POST['lipat']) ? 1 : 0;

    // Opsi layanan
$opsi = '';
if ($setrika && $lipat) {
    $opsi = 'Setrika Lipat';
} elseif ($setrika) {
    $opsi = 'Setrika';
} elseif ($lipat) {
    $opsi = 'Lipat';
}


    // Jumlah item
    $jumlah_sabun = intval($_POST['sabun']);
    $jumlah_pewangi = intval($_POST['pewangi']);
    $jumlah_plastik = intval($_POST['plastik']);

    // Hitung harga layanan
    $harga_layanan = 0;
    if ($jenis == "Cuci Basah") {
        $harga_layanan = 10000 * $mesin;
    } elseif ($jenis == "Cuci Kering") {
        $harga_layanan = 20000 * $mesin;
        if ($setrika) $harga_layanan += 8000 * $berat;
        if ($lipat) $harga_layanan += 5000 * $berat;
    }

    // Harga item
    $total_item = ($jumlah_sabun * 1000) + ($jumlah_pewangi * 1000) + ($jumlah_plastik * 2000);
    $total = $harga_layanan + $total_item;

    // Pembayaran
    $bayar = floatval($_POST['bayar']);
    $kembali = $bayar - $total;
    $status = ($kembali >= 0) ? "Lunas" : "Belum Lunas";

    // Simpan ke tabel dsbrd_kasir
    $query1 = "INSERT INTO dsbrd_kasir (pelanggan, jenis_laundry, mesin, berat, total, bayar, kembali, status) 
               VALUES ('$nama_pelanggan', '$jenis', $mesin, $berat, $total, $bayar, $kembali, '$status')";

    if (mysqli_query($conn, $query1)) {
        $id_transaksi = mysqli_insert_id($conn);

        $detail_values = [];

        if ($jumlah_sabun > 0) {
            $detail_values[] = "($id_transaksi, '$tanggal', '$jam', 1, $jumlah_sabun)";
            mysqli_query($conn, "UPDATE item SET jumlah = jumlah - $jumlah_sabun WHERE id_item = 1");
        }
        if ($jumlah_pewangi > 0) {
            $detail_values[] = "($id_transaksi, '$tanggal', '$jam', 2, $jumlah_pewangi)";
            mysqli_query($conn, "UPDATE item SET jumlah = jumlah - $jumlah_pewangi WHERE id_item = 2");
        }
        if ($jumlah_plastik > 0) {
            $detail_values[] = "($id_transaksi, '$tanggal', '$jam', 3, $jumlah_plastik)";
            mysqli_query($conn, "UPDATE item SET jumlah = jumlah - $jumlah_plastik WHERE id_item = 3");
        }

        if (!empty($detail_values)) {
            $query2 = "INSERT INTO detail_kasir (id_kasir, tanggal, jam, id_item, jumlah) VALUES " . implode(',', $detail_values);
            mysqli_query($conn, $query2);
        }

        echo "";
    } else {
        echo "Gagal menyimpan transaksi: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir</title>
    <link rel="stylesheet" href="transaksi.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        function resetForm() {
            document.querySelector("form").reset();
        }
    </script>
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
            <h2>Dashboard Kasir</h2>
            <div class="form-transaksi">
                <form action="#" method="POST">
                    <div class="form-grid">
                        <div class="form-left">
                            <label>Nama Pelanggan:
                                <input type="text" name="pelanggan" required style="width: 350px"/>
                            </label>
                            <div class="datetime-group">
                                <label>Tanggal & Jam:
                                    <div class="datetime-inputs">
                                        <input type="date" name="tanggal" id="tanggal" required>
                                        <input type="time" name="jam" id="jam" required>
                                    </div>
                                </label>
                            </div>
                            <div class="input-group">
                                <label>Jenis Laundry:
                                    <div class="laundry-type-container">  
                                        <select name="jenis" id="jenis" required>
                                            <option value="">-- Pilih Jenis --</option>
                                            <option value="Cuci Kering">Cuci Kering</option>
                                            <option value="Cuci Basah">Cuci Basah</option>
                                        </select>
                                        
                                        </div>
                                    </label>
                                </div>
                            <div class="input-group">
                            <div class="form-field">
                                    <label>Mesin</label>
                                    <input type="number" name="mesin" step="1" min="1" required style="width: 50px; height:30px;">  <!-- ~3 karakter -->
                                </div>
                                <div class="form-field">
                                    <label>Berat(kg)</label>
                                    <input type="number" name="berat" id="berat" step="0.1" min="0.1" required style="width: 60px; height:30px;">
                                <input type="checkbox" name="setrika" id="setrika" value="1" style="margin-left: 20px;"> Setrika
                                <input type="checkbox" name="lipat" id="lipat" value="1" style="margin-left: 10px;"> Lipat
                                </div>
                            </div>
                            <div class="input-group">
                        <div class="form-field">
                        <label>Item:</label>
                                    <input type="number" name="sabun" id="sabun" step="1" min="0" required style="width: 50px; height:30px;"> Sabun
                                    <input type="number" name="pewangi" id="pewangi"  step="1" min="0" required style="width: 50px; height:30px;"> Pewangi
                                    <input type="number" name="plastik" id="plastik"  step="1" min="0" required style="width: 50px; height:30px;"> Plastik
      </div>
      </div>
                        </div>
                        
                                    <div class="form-right">
                            <div class="input-group">
                                <label>Total:
                                <input type="text" name="total" id="total" style="width:350px;"  readonly />
                                </label>
                            </div>
                            <label>Bayar:
                                <div class="input-group">
                                    <input type="text" name="bayar" id="bayar" style="width: 300px;"/>
                                    <button type="button" id="btn-ok" class="btn-ok">OK</button>
                                </div>
                            </label>
                            <label>Kembali:
                                <input type="text" name="kembali" id="kembali" style="width:350px;" readonly />
                            </label>
                            <label>Status:
                                <select name="status" id="status" style="width:350px;">
                                    <option value="Belum Lunas">Belum Lunas</option>
                                    <option value="Lunas">Lunas</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-tambah">Tambah</button>
                        <a href="" class="btn-kembali" onclick="resetForm()">Hapus</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
   document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk mengatur tanggal dan jam
    function setTanggalDanJam() {
        const sekarang = new Date();

        // Format tanggal (YYYY-MM-DD)
        const tahun = sekarang.getFullYear();
        const bulan = String(sekarang.getMonth() + 1).padStart(2, '0');
        const hari = String(sekarang.getDate()).padStart(2, '0');
        const tanggalFormat = `${tahun}-${bulan}-${hari}`;
        document.getElementById('tanggal').value = tanggalFormat;

        // Format waktu (HH:MM)
        const jam = String(sekarang.getHours()).padStart(2, '0');
        const menit = String(sekarang.getMinutes()).padStart(2, '0');
        const waktuFormat = `${jam}:${menit}`;
        document.getElementById('jam').value = waktuFormat;
    }

    // Set awal saat halaman dimuat
    setTanggalDanJam();

    // Perbarui jam setiap 60 detik
    setInterval(() => {
        const sekarang = new Date();
        const jam = String(sekarang.getHours()).padStart(2, '0');
        const menit = String(sekarang.getMinutes()).padStart(2, '0');
        const waktuFormat = `${jam}:${menit}`;
        document.getElementById('jam').value = waktuFormat;
    }, 60000);
});

    </script>
</body>


<script>
function hitungMesinOtomatis() {
    const jenis = document.getElementById("jenis").value;
    const berat = parseFloat(document.getElementById("berat").value) || 0;
    let mesin = 1;

    if (jenis === "Cuci Basah") {
        mesin = 1 + Math.floor(berat / 8.1); // Tambah 1 mesin tiap >8kg
    } else if (jenis === "Cuci Kering") {
        mesin = 2 + 2 * Math.floor(berat / 8.1); // Tambah 2 mesin tiap >8kg
    }

    document.querySelector('input[name="mesin"]').value = mesin;
    return mesin; // agar bisa digunakan oleh fungsi lain
}

function hitungTotal() {
    const jenis = document.getElementById("jenis").value;
    const berat = parseFloat(document.getElementById("berat").value) || 0;
    const setrika = document.getElementById("setrika").checked;
    const lipat = document.getElementById("lipat").checked;

    const sabun = parseInt(document.getElementById("sabun").value) || 0;
    const pewangi = parseInt(document.getElementById("pewangi").value) || 0;
    const plastik = parseInt(document.getElementById("plastik").value) || 0;

    const mesin = hitungMesinOtomatis(); // Dapatkan jumlah mesin otomatis
    let hargaLayanan = 10000 * mesin;

    if (jenis === "Cuci Kering") {
        if (setrika) hargaLayanan += 8000 * berat;
        if (lipat) hargaLayanan += 5000 * berat;
    }

    const totalItem = (sabun * 1000) + (pewangi * 1000) + (plastik * 2000);
    const total = hargaLayanan + totalItem;

    document.getElementById("total").value = total;
}


function hitungKembali() {
    const total = parseFloat(document.getElementById("total").value) || 0;
    const bayar = parseFloat(document.getElementById("bayar").value) || 0;
    const kembali = bayar - total;

    document.getElementById("kembali").value = kembali;

    const status = document.getElementById("status");
    status.value = (kembali >= 0) ? "Lunas" : "Belum Lunas";
}

// Total otomatis saat input berubah
const inputs = ["jenis", "berat", "setrika", "lipat", "sabun", "pewangi", "plastik"];
inputs.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener("change", () => {
            hitungMesinOtomatis();
            hitungTotal();
        });
    }
});


// Kembali otomatis saat klik OK
document.getElementById("btn-ok").addEventListener("click", hitungKembali);
</script>

</html>