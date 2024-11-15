<?php
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "db_tes";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch products
$query = "SELECT * FROM product";
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark mb-5 bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Form Penjualan</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tabel_sales.php">Tabel Penjualan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="product.php">Produk</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <form id="penjualanForm" action="simpan_data.php" method="POST" enctype="multipart/form-data">
            <!-- Form: Nama Pelanggan -->
            <div class="form-group row mb-3">
                <label for="nama_pelanggan" class="col-sm-2 col-form-label">Nama Pelanggan</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="nama_pelanggan" id="nama_pelanggan" placeholder="Masukan nama" >
                </div>
            </div>

            <!-- Form: Produk dengan Dropdown -->
            <div class="form-group row mb-3">
                <label for="produk" class="col-sm-2 col-form-label">Produk</label>
                <div class="col-sm-10">
                    <select class="form-control" name="produk" id="produk" >
                        <option value="">Pilih Produk</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $stokTersisa = $row['qty_stock'] - $row['total_qty_sold'];
                                if ($stokTersisa > 0) {
                                    echo "<option value='" . $row["product_id"] . "' data-harga='" . $row["price"] . "' data-qty='" . $stokTersisa . "'>" . $row["product_name"] . "</option>";
                                }
                            }
                        } else {
                            echo "<option value=''>Produk tidak ditemukan</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Form: Harga -->
            <div class="form-group row mb-3">
                <label for="harga" class="col-sm-2 col-form-label">Harga</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="harga" id="harga" placeholder="Harga" readonly>
                </div>
            </div>

            <!-- Form: Qty -->
            <div class="form-group row mb-3">
                <label for="Qty" class="col-sm-2 col-form-label">Qty</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="qty" id="Qty" min="1" >
                </div>
            </div>

            <!-- Form: Total -->
            <div class="form-group row mb-3">
                <label for="Total" class="col-sm-2 col-form-label">Total</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="total" id="Total" placeholder="Isi otomatis" readonly>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="mb-3 row mt-4">
                <div class="col">
                    <button type="submit" name="aksi" value="save" class="btn btn-primary text-white me-2">Kirim</button>
                    <button type="button" class="btn btn-secondary" onclick="location.href='tabel_sales.php'">Batal</button>
                </div>
            </div>  
        </form>
    </div>

    <!-- Modal Stok Habis -->
    <div class="modal fade" id="stokHabisModal" tabindex="-1" aria-labelledby="stokHabisModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stokHabisModalLabel">Stok Habis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Stok produk ini sudah habis.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Alert -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Peringatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Semua kolom harus diisi.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Tambahkan JavaScript Bootstrap dan Validasi -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
            var stokHabisModal = new bootstrap.Modal(document.getElementById('stokHabisModal'));

            document.getElementById('penjualanForm').addEventListener('submit', function(e) {
                var isValid = true;
                var inputs = document.querySelectorAll('#penjualanForm input, #penjualanForm select');

                inputs.forEach(function(input) {
                    if (input.value === '') {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alertModal.show();
                }
            });

            document.getElementById('produk').addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var harga = selectedOption.getAttribute('data-harga');
                var qtyStock = selectedOption.getAttribute('data-qty');

                console.log("Selected Product Price: ", harga);
                console.log("Selected Product Qty: ", qtyStock);

                if (qtyStock <= 0) {
                    stokHabisModal.show();
                    document.getElementById('harga').value = '';
                    document.getElementById('Qty').value = '';
                    document.getElementById('Total').value = '';
                    document.getElementById('Qty').setAttribute('disabled', 'true');
                } else {
                    document.getElementById('harga').value = 'Rp ' + parseFloat(harga).toLocaleString('id-ID');
                    document.getElementById('Qty').removeAttribute('disabled');
                    document.getElementById('Qty').setAttribute('max', qtyStock);
                    document.getElementById('Qty').value = 1; // Setting minimum quantity to 1
                    document.getElementById('Total').value = 'Rp ' + (parseFloat(harga)).toLocaleString('id-ID');
                }
            });

            document.getElementById('Qty').addEventListener('input', function() {
                var qtyInput = document.getElementById('Qty');
                var maxQty = parseInt(qtyInput.getAttribute('max'), 10);
                var qty = parseInt(qtyInput.value, 10);

                if (qty < 1) {
                    qtyInput.value = 1;
                }

                if (qty > maxQty) {
                    qtyInput.value = maxQty;
                    stokHabisModal.show();
                    qty = maxQty; // Set qty to maxQty to ensure correct total calculation
                }

                var harga = parseFloat(document.getElementById('harga').value.replace(/[^0-9]/g, ''));
                qty = parseInt(qtyInput.value, 10); // Mengambil kembali nilai qty yang telah diubah
                if (!isNaN(harga) && !isNaN(qty)) {
                    var total = harga * qty;
                    document.getElementById('Total').value = 'Rp ' + total.toLocaleString('id-ID');
                }
            });
        });
    </script>
</body>
</html>
