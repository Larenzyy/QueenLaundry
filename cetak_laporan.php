<?php
require_once 'dompdf/autoload.inc.php';
include 'config.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$tgl_awal = $_GET['tanggal-awal'] ?? '';
$tgl_akhir = $_GET['tanggal-akhir'] ?? '';

$filter = "";
if ($tgl_awal && $tgl_akhir) {
    $filter = "WHERE dk.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

// Ambil data
$query = "
    SELECT DATE(dk.tanggal) AS tanggal, 
           SUM(k.total) AS total_pemasukan, 
           COUNT(DISTINCT dk.id_kasir) AS jumlah_transaksi
    FROM detail_kasir dk
    JOIN dsbrd_kasir k ON dk.id_kasir = k.id_kasir
    $filter
    GROUP BY DATE(dk.tanggal)
";
$result = mysqli_query($conn, $query);

// Ambil pengeluaran
$query_pengeluaran = "
    SELECT DATE(tanggal) AS tanggal, 
           COUNT(*) AS jumlah_pengeluaran, 
           SUM(harga) AS total_pengeluaran
    FROM pengeluaran
    ".($tgl_awal && $tgl_akhir ? "WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'" : "")."
    GROUP BY DATE(tanggal)
";
$result_pengeluaran = mysqli_query($conn, $query_pengeluaran);

$data_pengeluaran = [];
while ($row = mysqli_fetch_assoc($result_pengeluaran)) {
    $data_pengeluaran[$row['tanggal']] = $row;
}

// Buat isi HTML
$html = "<h2 style='text-align: center;'>Laporan Keuangan</h2>";
$html .= "<p>Periode: {$tgl_awal} s.d. {$tgl_akhir}</p>";
$html .= "<table border='1' cellpadding='6' cellspacing='0' style='width: 100%; border-collapse: collapse; font-size: 12px;'>
<thead>
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Keterangan</th>
<th>Catatan</th>
<th>Pemasukan</th>
<th>Pengeluaran</th>
</tr>
</thead><tbody>";

$no = 1;
$total_pemasukan = 0;
$total_pengeluaran = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $tanggal = $row['tanggal'];
    $jumlah_transaksi = $row['jumlah_transaksi'];
    $pemasukan = $row['total_pemasukan'];
    $pengeluaran = 0;
    $jumlah_pengeluaran = 0;

    if (isset($data_pengeluaran[$tanggal])) {
        $pengeluaran = $data_pengeluaran[$tanggal]['total_pengeluaran'];
        $jumlah_pengeluaran = $data_pengeluaran[$tanggal]['jumlah_pengeluaran'];
    }

    $catatan = "{$jumlah_transaksi} transaksi masuk";
    if ($jumlah_pengeluaran > 0) {
        $catatan .= ", {$jumlah_pengeluaran} pengeluaran";
    }

    $keterangan = ($jumlah_pengeluaran > 0) ? 'Transaksi & Pengeluaran' : 'Transaksi';

    $html .= "<tr>
        <td>{$no}</td>
        <td>{$tanggal}</td>
        <td>{$keterangan}</td>
        <td>{$catatan}</td>
        <td>Rp " . number_format($pemasukan, 0, ',', '.') . "</td>
        <td>Rp " . number_format($pengeluaran, 0, ',', '.') . "</td>
    </tr>";

    $total_pemasukan += $pemasukan;
    $total_pengeluaran += $pengeluaran;
    $no++;
}

$saldo = $total_pemasukan - $total_pengeluaran;

$html .= "
<tr>
    <td colspan='4'><strong>Total</strong></td>
    <td><strong>Rp " . number_format($total_pemasukan, 0, ',', '.') . "</strong></td>
    <td><strong>Rp " . number_format($total_pengeluaran, 0, ',', '.') . "</strong></td>
</tr>
<tr>
    <td colspan='4'><strong>Saldo Akhir</strong></td>
    <td colspan='2'><strong>Rp " . number_format($saldo, 0, ',', '.') . "</strong></td>
</tr>
</tbody></table>";

// Inisialisasi Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output ke browser (inline)
$dompdf->stream("Laporan_{$tgl_awal}_{$tgl_akhir}.pdf", ["Attachment" => false]);
