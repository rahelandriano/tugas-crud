<?php
// Buat koneksi ke database
$conn = new mysqli("localhost", "root", "123", "db_tes");

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID penjualan dari parameter URL
$sales_id = $_GET['id'];

// Ambil data penjualan untuk mendapatkan product_id dan qty
$sql = "SELECT product_id, qty FROM sales WHERE sales_id = $sales_id";
$result = $conn->query($sql);

if ($result === false) {
    die("Error pada query: " . $conn->error);
}

$row = $result->fetch_assoc();
$produk_id = $row['product_id'];
$qty = $row['qty'];

// Hapus data penjualan
$sql = "DELETE FROM sales WHERE sales_id = $sales_id";
if ($conn->query($sql) === TRUE) {
    // Tambah stok kembali di tabel product
    $sql = "UPDATE product SET qty_stock = qty_stock + $qty WHERE product_id = $produk_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: tabel_sales.php");
        exit();
    } else {
        echo "Error menambah stok: " . $conn->error;
    }
} else {
    die("Error menghapus data penjualan: " . $conn->error);
}

// Tutup koneksi
$conn->close();
?>
