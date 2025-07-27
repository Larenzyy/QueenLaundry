<?php
include 'config.php';

$status = isset($_GET['status']) ? $_GET['status'] : '';
$keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$sql = "SELECT 
            t.id_kasir AS id,
            t.pelanggan,
            t.jenis_laundry,
            t.mesin,
            t.berat,
            t.opsi,
            t.total,
            t.status,
            MAX(dt.tanggal) AS tanggal,
            GROUP_CONCAT(i.nama_item SEPARATOR '<br>') AS item
        FROM dsbrd_kasir t
        LEFT JOIN detail_kasir dt ON dt.id_kasir = t.id_kasir
        LEFT JOIN item i ON i.id_item = dt.id_item
        WHERE 1=1";

// Filter berdasarkan status
if ($status === 'Lunas') {
    $sql .= " AND t.status = 'Lunas'";
} elseif ($status === 'Belum Lunas') {
    $sql .= " AND t.status = 'Belum Lunas'";
}

// Filter berdasarkan keyword
if (!empty($keyword)) {
    $sql .= " AND (
        t.pelanggan LIKE '%$keyword%' OR 
        t.jenis_laundry LIKE '%$keyword%' OR
        DATE_FORMAT(dt.tanggal, '%d/%m/%Y') LIKE '%$keyword%' OR
        i.nama_item LIKE '%$keyword%'
    )";
}

$sql .= " GROUP BY t.id_kasir, t.pelanggan, t.jenis_laundry, t.mesin, t.berat, t.opsi, t.total, t.status
          ORDER BY tanggal DESC
          LIMIT $start, $limit";

$result = $conn->query($sql);

if (!$result) {
    echo "<tr><td colspan='11'>Query Error: " . $conn->error . "</td></tr>";
    exit;
}

$no = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['pelanggan']) . "</td>";
        $tgl = $row['tanggal'] ? date('d/m/Y', strtotime($row['tanggal'])) : '-';
        echo "<td>$tgl</td>";
        echo "<td>" . htmlspecialchars($row['jenis_laundry']) . "</td>";
        echo "<td>" . number_format($row['mesin'], 0, ',', '.') . "</td>";
        echo "<td>" . number_format($row['berat'], 0, ',', '.') . "</td>";
        echo "<td>" . htmlspecialchars($row['opsi']) . "</td>";
        echo "<td>" . $row['item'] . "</td>";
        echo "<td>Rp. " . number_format($row['total'], 0, ',', '.') . "</td>";
        $status = $row['status'];
        $id_kasir = $row['id'];
        if ($status === 'Lunas') {
            echo "<td><span class='status-lunas' style='font-size: 12px;'>Lunas</span></td>";
        } else {
            echo "<td><span class='status-belum' style='cursor:pointer; font-size: 12px; white-space: nowrap;' onclick='konfirmasiLunas($id_kasir, this)'>Belum Lunas</span></td>";
        }
        echo "<td>
                <button class='btn-cetak'>Cetak</button>
                <button class='btn-edit' style='height: 25px; margin-top: 5px;'>Edit</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='11' style='text-align:center;'>Belum ada transaksi</td></tr>";
}
?>
