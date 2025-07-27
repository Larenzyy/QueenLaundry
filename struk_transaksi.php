<?php
include 'config.php';

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id_kasir = intval($_GET['id']);

$sql = "SELECT 
          t.pelanggan,
          t.jenis_laundry,
          t.mesin,
          t.berat,
          t.opsi,
          t.total,
          t.status,
          MAX(dt.tanggal) AS tanggal,
          GROUP_CONCAT(i.nama_item SEPARATOR ', ') AS item
        FROM dsbrd_kasir t
        LEFT JOIN detail_kasir dt ON dt.id_kasir = t.id_kasir
        LEFT JOIN item i ON i.id_item = dt.id_item
        WHERE t.id_kasir = $id_kasir
        GROUP BY t.id_kasir";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Transaksi tidak ditemukan.";
    exit;
}

$row = $result->fetch_assoc();

echo "<pre>";
echo "====== QUEEN LAUNDRY COIN ======\n";
echo "Tanggal   : " . date('d/m/Y', strtotime($row['tanggal'])) . "\n";
echo "Pelanggan : " . $row['pelanggan'] . "\n";
echo "Layanan   : " . $row['jenis_laundry'] . "\n";
echo "Mesin     : " . $row['mesin'] . "\n";
echo "Berat     : " . $row['berat'] . " kg\n";
echo "Opsi      : " . $row['opsi'] . "\n";
echo "Item      : " . $row['item'] . "\n";
echo "---------------------------------\n";
echo "TOTAL     : Rp. " . number_format($row['total'], 0, ',', '.') . "\n";
echo "Status    : " . $row['status'] . "\n";
echo "=================================";
echo "</pre>";
?>
