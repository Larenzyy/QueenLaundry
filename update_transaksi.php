<?php
include 'config.php';

$id = $_POST['id_kasir'];
$pelanggan = $_POST['pelanggan'];
$jenis = $_POST['jenis_laundry'];
$berat = $_POST['berat'];
$opsi = $_POST['opsi'];

$sql = "UPDATE dsbrd_kasir SET 
          pelanggan = '$pelanggan', 
          jenis_laundry = '$jenis', 
          berat = '$berat', 
          opsi = '$opsi' 
        WHERE id_kasir = '$id'";

if ($conn->query($sql)) {
  echo "Perubahan berhasil disimpan.";
} else {
  echo "Gagal: " . $conn->error;
}
?>
