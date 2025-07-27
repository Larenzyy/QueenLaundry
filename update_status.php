<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE dsbrd_kasir SET status = 'Lunas' WHERE id_kasir = $id";
    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
?>
