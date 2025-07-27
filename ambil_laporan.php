<?php
include 'config.php';

$tgl_awal = $_GET['tanggal_awal'] ?? null;
$tgl_akhir = $_GET['tanggal_akhir'] ?? null;

$filter = "";
if ($tgl_awal && $tgl_akhir) {
    $filter = "WHERE dk.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query_pemasukan = "
    SELECT dk.tanggal, SUM(k.total) AS total_pemasukan, COUNT(DISTINCT dk.id_kasir) AS jumlah_transaksi
    FROM detail_kasir dk
    JOIN dsbrd_kasir k ON dk.id_kasir = k.id_kasir
    $filter
    GROUP BY dk.tanggal
";

$query_pengeluaran = "
    SELECT DATE(tanggal) AS tanggal, 
           COUNT(*) AS jumlah_pengeluaran, 
           SUM(harga) AS total_pengeluaran
    FROM pengeluaran
    " . ($tgl_awal && $tgl_akhir ? "WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'" : "") . "
    GROUP BY DATE(tanggal)
";

$result_pemasukan = mysqli_query($conn, $query_pemasukan);
$result_pengeluaran = mysqli_query($conn, $query_pengeluaran);

$data_pengeluaran = [];
while ($row = mysqli_fetch_assoc($result_pengeluaran)) {
    $tgl = $row['tanggal'];
    $data_pengeluaran[$tgl] = [
        'jumlah_pengeluaran' => $row['jumlah_pengeluaran'],
        'total_pengeluaran' => $row['total_pengeluaran']
    ];
}

$data = [];
$total_pemasukan = 0;
$total_pengeluaran = 0;

while ($row = mysqli_fetch_assoc($result_pemasukan)) {
    $tanggal = $row['tanggal'];
    $jumlah_transaksi = $row['jumlah_transaksi'];
    $pemasukan = $row['total_pemasukan'];
    $pengeluaran = $data_pengeluaran[$tanggal]['total_pengeluaran'] ?? 0;
    $jumlah_pengeluaran = $data_pengeluaran[$tanggal]['jumlah_pengeluaran'] ?? 0;

    $catatan = "{$jumlah_transaksi} transaksi masuk";
    if ($jumlah_pengeluaran > 0) {
        $catatan .= ", {$jumlah_pengeluaran} pengeluaran";
    }

    $keterangan = ($jumlah_pengeluaran > 0) ? 'Transaksi dan Pengeluaran' : 'Transaksi';

    $data[] = [
        'tanggal' => $tanggal,
        'keterangan' => $keterangan,
        'catatan' => $catatan,
        'pemasukan' => $pemasukan,
        'pengeluaran' => $pengeluaran
    ];

    $total_pemasukan += $pemasukan;
    $total_pengeluaran += $pengeluaran;
}

echo json_encode([
    'data' => $data,
    'total_pemasukan' => $total_pemasukan,
    'total_pengeluaran' => $total_pengeluaran,
    'saldo_akhir' => $total_pemasukan - $total_pengeluaran
]);
