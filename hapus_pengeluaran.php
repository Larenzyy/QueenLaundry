<?php
include 'config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // pastikan integer

    // Hapus hanya 1 baris yang sesuai id
    $sql = "DELETE FROM pengeluaran WHERE id_luar = $id";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
?>
