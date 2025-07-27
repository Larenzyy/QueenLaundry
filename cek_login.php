<?php
include 'config.php';

// Ambil data dari POST
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';

// Cek input kosong
if (empty($username) || empty($password) || empty($role)) {
    echo "error: input kosong";
    exit;
}

// Tentukan nama tabel berdasarkan role
$table = '';
if ($role === 'admin') {
    $table = 'lgn_admin';
} elseif ($role === 'owner') {
    $table = 'lgn_owner';
} else {
    echo "error: role tidak dikenal";
    exit;
}

// Gunakan prepared statement
$stmt = $conn->prepare("SELECT * FROM $table WHERE username = ? AND password = ?");
if (!$stmt) {
    echo "error: prepare gagal - " . $conn->error;
    exit;
}

$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah user ditemukan
if ($result && $result->num_rows > 0) {
    echo "success";
} else {
    echo "error: login gagal";
}

$stmt->close();
$conn->close();
?>
