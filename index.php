<?php
include 'config.php'; // Memasukkan file konfigurasi untuk koneksi database

// Menambahkan item baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    try {
        $item_name = $_POST['item_name'];
        $quantity = $_POST['quantity'];
        $category_id = $_POST['category_id'];

        // Validasi input
        if (empty($item_name) || !is_numeric($quantity) || empty($category_id)) {
            echo "<script>alert('Input tidak valid!');</script>";
            return; // Menghentikan eksekusi jika ada input yang tidak valid
        }

        $stmt = $conn->prepare("INSERT INTO Items (item_name, quantity, category_id) VALUES (?, ?, ?)");
        $stmt->execute([$item_name, $quantity, $category_id]);
        echo "<script>alert('Item berhasil ditambahkan!');</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Menangani kesalahan dengan pesan
    }
}

// Mengambil daftar item dari database
$items = $conn->query("SELECT i.item_id, i.item_name, i.quantity, c.category_name 
                        FROM Items i 
                        JOIN Categories c ON i.category_id = c.category_id")->fetchAll(PDO::FETCH_ASSOC);

// Memperbarui item
if (isset($_POST['update_item'])) {
    try {
        $item_id = $_POST['item_id'];
        $item_name = $_POST['item_name'];
        $quantity = $_POST['quantity'];
        $category_id = $_POST['category_id'];

        // Validasi input
        if (empty($item_name) || !is_numeric($quantity) || empty($category_id)) {
            echo "<script>alert('Input tidak valid!');</script>";
            return;
        }

        $stmt = $conn->prepare("UPDATE Items SET item_name = ?, quantity = ?, category_id = ? WHERE item_id = ?");
        $stmt->execute([$item_name, $quantity, $category_id, $item_id]);
        echo "<script>alert('Item berhasil diperbarui!');</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Menghapus item
if (isset($_GET['delete'])) {
    $item_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM Items WHERE item_id = ?");
    $stmt->execute([$item_id]);
    header("Location: index.php"); 
    exit(); // Menghentikan eksekusi setelah redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping List App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background-color: #f0f4f8;
        }

        .modal {
            animation: fadeIn 0.5s; 
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        table th, table td {
            text-align: center;
        }

        button:hover {
            opacity: 0.8; 
        }

        .btn-danger {
            transition: background-color 0.3s; 
        }

        .btn-danger:hover {
            background-color: #dc3545; 
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Shopping List</h1>

        <form method="POST" action="" class="mb-4">
            <div class="form-row">
                <div class="col">
                    <input type="text" class="form-control" name="item_name" placeholder="Nama Item" required>
                </div>
                <div class="col">
                    <input type="number" class="form-control" name="quantity" placeholder="Jumlah" required>
                </div>
                <div class="col">
                    <select name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <?php
                        // Mengambil kategori dari database
                        $categories = $conn->query("SELECT * FROM Categories")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($categories as $category) {
                            echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" name="add_item" class="btn btn-primary">Tambah Item</button>
                </div>
            </div>
        </form>

        <h2>Item yang Perlu Dibeli</h2>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama Item</th>
                    <th>Jumlah</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_id']) ?></td>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars($item['category_name']) ?></td>
                        <td>
                            <a href="?delete=<?= htmlspecialchars($item['item_id']) ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')">Hapus</a>
                            <button class="btn btn-warning btn-sm" 
                                    onclick="document.getElementById('updateModal<?= $item['item_id'] ?>').style.display='block'">Edit</button>
                        </td>
                    </tr>

                    <div id="updateModal<?= $item['item_id'] ?>" class="modal" style="display:none;">
                        <div class="modal-content">
                            <form method="POST" action="">
                                <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['item_id']) ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Item</h5>
                                    <button type="button" class="close" 
                                            onclick="document.getElementById('updateModal<?= $item['item_id'] ?>').style.display='none'">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <input type="text" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required class="form-control">
                                    <input type="number" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" required class="form-control">
                                    <select name="category_id" required class="form-control">
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= htmlspecialchars($category['category_id']) ?>" <?= ($category['category_id'] == $item['category_id']) ? 'selected' : '' ?>><?= htmlspecialchars($category['category_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="update_item" class="btn btn-primary">Update Item</button>
                                    <button type="button" class="btn btn-secondary" 
                                            onclick="document.getElementById('updateModal<?= $item['item_id'] ?>').style.display='none'">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
