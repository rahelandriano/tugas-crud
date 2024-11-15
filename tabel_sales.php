<?php
// Buat koneksi ke database
$conn = new mysqli("localhost", "root", "123", "db_tes");

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari tabel sales
$sql = "SELECT s.sales_id, s.costumer_name, p.product_name, s.price, s.qty, s.total
        FROM sales s
        JOIN product p ON s.product_id = p.product_id";
$result = $conn->query($sql);

if ($result === false) {
    die("Error pada query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
                            <a class="nav-link " href="index.php">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="tabel_sales.php">Tabel Penjualan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="product.php">Produk</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
            <h2>Data Penjualan</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Penjualan</th>
                            <th>Nama Pelanggan</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            // Tampilkan data ke tabel
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['sales_id']}</td>
                                        <td>{$row['costumer_name']}</td>
                                        <td>{$row['product_name']}</td>
                                        <td>Rp " . number_format($row['price'], 0, ',', '.') . "</td>
                                        <td>{$row['qty']}</td>
                                        <td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>
                                        <td><a href='edit.php?id={$row['sales_id']}' class='btn btn-warning'>Edit</a></td>
                                        <td><button class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='{$row['sales_id']}'>Delete</button></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>Tidak ada data penjualan</td></tr>";
                        }

                        // Tutup koneksi
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Buttons -->
            <div class="mb-3 row mt-4">
                <div class="col">
                    <a href="index.php" class="btn btn-primary text-white">Tambahkan</a>
                </div>
            </div>
        </div>
    </div>

       <!-- Modal Konfirmasi Penghapusan -->
       <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Tombol yang memicu modal
                var id = button.getAttribute('data-id'); // Ambil id dari data-id
                var href = 'delete.php?id=' + id; // Buat URL untuk penghapusan
                var confirmDelete = deleteModal.querySelector('#confirmDelete');
                confirmDelete.setAttribute('href', href); // Set URL ke tombol konfirmasi
            });

            const alertPlaceholder = document.getElementById('liveAlertPlaceholder');

            const showAlert = (message, type) => {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                        ${message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                     </div>`;

                alertPlaceholder.append(wrapper);
            };

            const produkSelect = document.getElementById('produk');
            const hargaInput = document.getElementById('harga');
            const qtyInput = document.getElementById('qty');
            const totalInput = document.getElementById('total');
            const originalQtyInput = document.getElementById('original_qty');

            const updateTotal = () => {
                const selectedOption = produkSelect.options[produkSelect.selectedIndex];
                const price = parseFloat(selectedOption.getAttribute('data-harga'));
                const qty = parseInt(qtyInput.value);

                if (!isNaN(price)) {
                    hargaInput.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
                }

                if (!isNaN(price) && !isNaN(qty)) {
                    const total = price * qty;
                    totalInput.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                }
            };

            qtyInput.addEventListener('input', function() {
                const selectedOption = produkSelect.options[produkSelect.selectedIndex];
                const stock = parseInt(selectedOption.getAttribute('data-qty'));
                const originalQty = parseInt(originalQtyInput.value);
                const qty = parseInt(qtyInput.value);

                if (qty < 1) {
                    qtyInput.value = 1;
                }

                const qtyDifference = qty - originalQty;

                if (qtyDifference > stock) {
                    qtyInput.value = originalQty + stock;
                    const remainingStock = stock - qtyDifference + originalQty;
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
    <div id="liveAlertPlaceholder"></div>
</body>
</html>
