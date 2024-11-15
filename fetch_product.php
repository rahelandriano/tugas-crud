<?php
// Konfigurasi koneksi database
$servername = "localhost";
$username = "root"; // sesuaikan nama pengguna
$password = "123"; // sesuaikan kata sandi
$dbname = "db_tes";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data dari tabel 'product'
$sql = "SELECT product_id, product_name, price, qty_stock FROM product";
$result = $conn->query($sql);

if ($result === false) {
    die("Error pada query: " . $conn->error);
}

echo '<table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID Produk</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Jumlah Stok</th>
            </tr>
        </thead>
        <tbody>';

if ($result->num_rows > 0) {
    // Keluarkan data dari setiap baris
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['product_id']}</td>
                <td>{$row['product_name']}</td>
                <td>Rp " . number_format($row['price'], 0, ',', '.') . "</td>
                <td>{$row['qty_stock']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4' class='text-center'>Tidak ada data yang ditemukan</td></tr>";
}

echo '</tbody></table>';

// Tutup koneksi
$conn->close();
?>
