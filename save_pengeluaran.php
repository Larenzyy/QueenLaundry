<?php
include 'config.php';

$pengeluaran = $_POST['pengeluaran'];
$tanggal = $_POST['tanggal'];
$qty = $_POST['qty'];
$harga = $_POST['harga'];
$catatan = $_POST['catatan'];

$sql = "INSERT INTO pengeluaran (pengeluaran, tanggal, qty, harga, catatan) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdds", $pengeluaran, $tanggal, $qty, $harga, $catatan);

if ($stmt->execute()) {
    location.reload();
} else {
    echo "Gagal menyimpan: " . $stmt->error;
}
?>
