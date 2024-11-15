<?php
$conn = new mysqli("localhost", "root", "123", "db_tes");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_penjualan = $_POST['id_penjualan'] ?? null;
$nama_pelanggan = $_POST['nama_pelanggan'] ?? '';
$produk = $_POST['produk'] ?? '';
$harga = isset($_POST['harga']) ? preg_replace('/\D/', '', $_POST['harga']) : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
$original_qty = isset($_POST['original_qty']) ? (int)$_POST['original_qty'] : 0;
$original_product = $_POST['original_product'] ?? '';

$total = $harga * $qty;

$conn->begin_transaction();

try {
    if ($id_penjualan) {
        // If the product changes, return the stock to the original product and then reduce the stock from the new product
        if ($original_product !== $produk) {
            // Return stock to the original product
            $sql_update_original_stok = "UPDATE product SET qty_stock = qty_stock + ? WHERE product_id = ?";
            $stmt_update_original_stok = $conn->prepare($sql_update_original_stok);

            if (!$stmt_update_original_stok) {
                throw new Exception("Error on updating original product stock: " . $conn->error);
            }

            $stmt_update_original_stok->bind_param("ii", $original_qty, $original_product);

            if (!$stmt_update_original_stok->execute()) {
                throw new Exception("Error executing update original product stock: " . $stmt_update_original_stok->error);
            }

            // Reduce stock for the new product
            $sql_update_new_stok = "UPDATE product SET qty_stock = qty_stock - ? WHERE product_id = ?";
            $stmt_update_new_stok = $conn->prepare($sql_update_new_stok);

            if (!$stmt_update_new_stok) {
                throw new Exception("Error on updating new product stock: " . $conn->error);
            }

            $stmt_update_new_stok->bind_param("ii", $qty, $produk);

            if (!$stmt_update_new_stok->execute()) {
                throw new Exception("Error executing update new product stock: " . $stmt_update_new_stok->error);
            }
        } else {
            // If the product does not change, update stock based on the quantity difference
            $qty_difference = $qty - $original_qty;

            $sql_update_stok = "UPDATE product SET qty_stock = qty_stock - ? WHERE product_id = ?";
            $stmt_update_stok = $conn->prepare($sql_update_stok);

            if (!$stmt_update_stok) {
                throw new Exception("Error on updating product stock: " . $conn->error);
            }

            $stmt_update_stok->bind_param("ii", $qty_difference, $produk);

            if (!$stmt_update_stok->execute()) {
                throw new Exception("Error executing update product stock: " . $stmt_update_stok->error);
            }
        }

        // Update sales data
        $sql = "UPDATE sales SET product_id = ?, price = ?, qty = ?, total = ? WHERE sales_id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error on updating sales: " . $conn->error);
        }

        $stmt->bind_param("iiiii", $produk, $harga, $qty, $total, $id_penjualan);

        if (!$stmt->execute()) {
            throw new Exception("Error executing update sales: " . $stmt->error);
        }
    } else {
        // Insert new sales data
        $sql = "INSERT INTO sales (costumer_name, product_id, price, qty, total) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error on inserting sales: " . $conn->error);
        }

        $stmt->bind_param("siiii", $nama_pelanggan, $produk, $harga, $qty, $total);

        if (!$stmt->execute()) {
            throw new Exception("Error executing insert sales: " . $stmt->error);
        }

        // Reduce stock for the new product when adding a new sale
        $sql_update_stok = "UPDATE product SET qty_stock = qty_stock - ? WHERE product_id = ?";
        $stmt_update_stok = $conn->prepare($sql_update_stok);

        if (!$stmt_update_stok) {
            throw new Exception("Error on updating product stock: " . $conn->error);
        }

        $stmt_update_stok->bind_param("ii", $qty, $produk);

        if (!$stmt_update_stok->execute()) {
            throw new Exception("Error executing update product stock: " . $stmt_update_stok->error);
        }
    }

    $conn->commit();
    header("Location: tabel_sales.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Transaction failed: " . $e->getMessage());
}

$stmt->close();
$stmt_update_stok->close();
$conn->close();
?>
