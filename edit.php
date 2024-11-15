<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "123", "db_tes");

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID penjualan dari URL atau form
$id_penjualan = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id_penjualan']) ? $_POST['id_penjualan'] : 0);

// Query untuk mengambil data penjualan berdasarkan ID
$sql = "SELECT s.sales_id AS id, s.costumer_name AS nama_pelanggan, p.product_name, s.product_id, s.price, s.qty, s.total 
        FROM sales s 
        JOIN product p ON s.product_id = p.product_id 
        WHERE s.sales_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Kesalahan pada query: " . $conn->error);
}

$stmt->bind_param("i", $id_penjualan);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Periksa apakah data ditemukan
if (!$row) {
    echo "Data tidak ditemukan.";
    exit;
}

// Permintaan untuk mengambil daftar produk
$sql_products = "SELECT product_id, product_name, price, qty_stock FROM product";
$result_products = $conn->query($sql_products);
$products = [];

while ($product = $result_products->fetch_assoc()) {
    $products[] = $product;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-5">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Form Penjualan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-5">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Tambah produk</a>
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

    <div class="container">
        <h2 class="text-center mb-4">Edit Penjualan</h2>
        <div id="alertContainer"></div>
        <form id="penjualanForm" action="simpan_data.php" method="POST">
            <input type="hidden" name="id_penjualan" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="original_product" value="<?php echo $row['product_id']; ?>">
            <div class="form-group mb-3">
                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                <input type="text" class="form-control" name="nama_pelanggan" id="nama_pelanggan" value="<?php echo $row['nama_pelanggan']; ?>" readonly>
            </div>
            <div class="form-group mb-3">
                <label for="produk" class="form-label">Produk</label>
                <select class="form-select" name="produk" id="produk" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['product_id']; ?>" data-price="<?php echo $product['price']; ?>" data-stock="<?php echo $product['qty_stock']; ?>" <?php echo $row['product_id'] == $product['product_id'] ? 'selected' : ''; ?>>
                            <?php echo $product['product_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="text" class="form-control" name="harga" id="harga" value="Rp <?php echo number_format($row['price'], 0, ',', '.'); ?>" readonly>
            </div>
            <div class="form-group mb-3">
                <label for="qty" class="form-label">Qty</label>
                <input type="number" class="form-control" name="qty" id="qty" value="<?php echo $row['qty']; ?>" min="1" required>
            </div>
            <input type="hidden" name="original_qty" id="original_qty" value="<?php echo $row['qty']; ?>">
            <div class="form-group mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="text" class="form-control" name="total" id="total" value="Rp <?php echo number_format($row['total'], 0, ',', '.'); ?>" readonly>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="tabel_sales.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const produkSelect = document.getElementById('produk');
        const hargaInput = document.getElementById('harga');
        const qtyInput = document.getElementById('qty');
        const totalInput = document.getElementById('total');
        const originalQtyInput = document.getElementById('original_qty');
        const alertPlaceholder = document.getElementById('alertContainer');

        const updateTotal = () => {
            const selectedOption = produkSelect.options[produkSelect.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price'));
            const qty = parseInt(qtyInput.value);

            if (!isNaN(price)) {
                hargaInput.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
            }

            if (!isNaN(price) && !isNaN(qty)) {
                const total = price * qty;
                totalInput.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            }
        };

        const showAlert = (message, type) => {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                    ${message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                 </div>`;
            alertPlaceholder.append(wrapper);
        };

        qtyInput.addEventListener('input', function() {
            const selectedOption = produkSelect.options[produkSelect.selectedIndex];
            const stock = parseInt(selectedOption.getAttribute('data-stock'));
            const originalQty = parseInt(originalQtyInput.value);
            const qty = parseInt(qtyInput.value);

            if (qty < 1) {
                qtyInput.value = 1;
            }

            const totalStockAvailable = stock + originalQty;

            if (qty > totalStockAvailable) {
                qtyInput.value = originalQty;
                const remainingStock = stock;
                const alertMessage = `Qty tidak boleh lebih besar dari sisa stok! Stok tersisa: ${remainingStock}`;
                showAlert(alertMessage, 'danger');
                qtyInput.classList.add("is-invalid");
            } else {
                qtyInput.classList.remove("is-invalid");
            }

            updateTotal();
        });

        produkSelect.addEventListener('change', function() {
            updateTotal();
        });

        // Initial load
        updateTotal();
    });
</script>
<div id="alertContainer"></div>

</body>
</html>