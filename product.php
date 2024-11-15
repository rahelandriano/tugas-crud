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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Form Penjualan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tabel_sales.php">Tabel Penjualan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="product.php">Produk</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Daftar Produk</h2>
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" id="reloadTableButton">Reload Tabel</button>
        </div>
        <div id="productTable">
            <!-- Tabel Produk akan di-load di sini -->
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reloadButton = document.getElementById('reloadTableButton');
            const productTableDiv = document.getElementById('productTable');

            const loadTable = () => {
                fetch('fetch_product.php')
                    .then(response => response.text())
                    .then(html => {
                        productTableDiv.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching product table:', error);
                    });
            };

            reloadButton.addEventListener('click', loadTable);

            // Initial load
            loadTable();
        });
    </script>
</body>
</html>
