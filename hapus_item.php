<?php
include 'config.php';

if (isset($_POST['id'])) {
  $id = intval($_POST['id']);
  $query = "DELETE FROM item WHERE id_item = $id";

  if ($conn->query($query) === TRUE) {
    echo "success";
  } else {
    echo "error";
  }
}

if (!isset($_GET['id'])) {
    echo 'invalid';
    exit;
}

$id = intval($_GET['id']);

// Hapus dari detail_kasir terlebih dahulu (karena foreign key ke dsbrd_kasir)
$sql_detail = "DELETE FROM detail_kasir WHERE id_kasir = $id";
$conn->query($sql_detail);

// Hapus dari dsbrd_kasir
$sql_main = "DELETE FROM dsbrd_kasir WHERE id_kasir = $id";
if ($conn->query($sql_main)) {
    echo 'success';
} else {
    echo 'error';
}

if (!isset($_GET['id'])) {
    echo 'invalid';
    exit;
}

$id = intval($_GET['id']);

$sql = "DELETE FROM pengeluaran WHERE id_luar = $id";

if ($conn->query($sql)) {
    echo "success";
} else {
    echo "error";
}

?>
