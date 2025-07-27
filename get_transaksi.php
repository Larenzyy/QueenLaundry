<?php
include 'config.php';
$id = $_GET['id'] ?? '';
if (!$id) exit(json_encode(['error' => 'ID kosong']));

$sql = "SELECT * FROM dsbrd_kasir WHERE id_kasir = '$id'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
echo json_encode($data);
?>
