<?php
include 'koneksi.php';

// --- AKSI TAMBAH PRODUK ---
if (isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama_produk = $_POST['nama_produk'];
    $kategori_id = $_POST['kategori_id'];
    $deskripsi   = $_POST['deskripsi'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    
    $gambar   = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    
    if ($gambar != "") {
        $gambar_baru = time() . "_" . $gambar;
        if (move_uploaded_file($tmp_name, "uploads/" . $gambar_baru)) {
            $gambar_db = $gambar_baru;
        } else {
            $gambar_db = null;
        }
    } else {
        $gambar_db = null;
    }

    $query = "INSERT INTO produk (kategori_id, nama_produk, deskripsi, harga, stok, gambar) 
              VALUES ('$kategori_id', '$nama_produk', '$deskripsi', '$harga', '$stok', '$gambar_db')";
    mysqli_query($koneksi, $query);
    header("Location: tambah_produk.php?status=success_add");
    exit;
}

// --- AKSI UPDATE PRODUK ---
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id          = $_POST['id'];
    $nama_produk = $_POST['nama_produk'];
    $kategori_id = $_POST['kategori_id'];
    $deskripsi   = $_POST['deskripsi'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    
    $old_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT gambar FROM produk WHERE id = $id"));
    
    $gambar   = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    
    if ($gambar != "") {
        $gambar_baru = time() . "_" . $gambar;
        if (move_uploaded_file($tmp_name, "uploads/" . $gambar_baru)) {
            if ($old_data['gambar'] && file_exists("uploads/" . $old_data['gambar'])) {
                unlink("uploads/" . $old_data['gambar']);
            }
            $gambar_query = ", gambar = '$gambar_baru'";
        } else {
            $gambar_query = "";
        }
    } else {
        $gambar_query = "";
    }

    $query = "UPDATE produk SET 
                kategori_id = '$kategori_id', 
                nama_produk = '$nama_produk', 
                deskripsi = '$deskripsi', 
                harga = '$harga',
                stok = '$stok'
                $gambar_query 
              WHERE id = $id";
    mysqli_query($koneksi, $query);
    header("Location: tambah_produk.php?status=success_update");
    exit;
}

// --- AKSI HAPUS PRODUK ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT gambar FROM produk WHERE id = $id"));
    if ($data['gambar'] && file_exists("uploads/" . $data['gambar'])) {
        unlink("uploads/" . $data['gambar']);
    }
    mysqli_query($koneksi, "DELETE FROM produk WHERE id = $id");
    header("Location: tambah_produk.php?status=success_delete");
    exit;
}

// LOGIKA EDIT & VIEW
$is_edit = false;
$edit_data = [];
if (isset($_GET['edit'])) {
    $is_edit = true;
    $id_edit = $_GET['edit'];
    $res_edit = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = $id_edit");
    $edit_data = mysqli_fetch_assoc($res_edit);
}

$produk_query = mysqli_query($koneksi, "SELECT produk.*, kategori.nama_kategori FROM produk JOIN kategori ON produk.kategori_id = kategori.id ORDER BY produk.id DESC");
$kategori_options = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outdoor Store - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .card-custom {
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }
        .table backend-table th {
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php">⛺ OUTDOOR STORE ADMIN</a>
            <span class="navbar-text text-white-50 d-none d-sm-inline">Panel Pengelolaan Data Produk</span>
        </div>
    </nav>

    <div class="bg-white py-4 shadow-sm mb-4">
        <div class="container-fluid px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h1 class="fw-bold text-dark fs-3 mb-1">Manajemen Inventaris</h1>
                <p class="text-muted small mb-0">Tambah, ubah, atau hapus item katalog toko dari satu tempat.</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-sm btn-outline-dark fw-bold px-3 py-2">
                    👀 Lihat Toko Utama
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 mb-5 flex-grow-1">
        
        <?php if(isset($_GET['status'])): ?>
            <div class="alert alert-dark alert-dismissible fade show shadow-sm text-center fw-bold mb-4" role="alert">
                <?php
                    if($_GET['status'] == 'success_add') echo "🎉 Produk baru berhasil ditambahkan!";
                    if($_GET['status'] == 'success_update') echo "📝 Data produk berhasil diperbarui!";
                    if($_GET['status'] == 'success_delete') echo "🗑️ Produk berhasil dihapus dari sistem!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            
            <div class="col-xl-3 col-lg-4">
                <div class="card shadow-sm card-custom">
                    <div class="card-header bg-dark text-white fw-bold py-3">
                        <?= $is_edit ? "📝 Edit Item: #" . $edit_data['id'] : "➕ Tambah Produk Baru" ?>
                    </div>
                    <div class="card-body bg-white p-4">
                        <form action="tambah_produk.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?= $is_edit ? 'update' : 'tambah' ?>">
                            <?php if($is_edit): ?>
                                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-secondary">Nama Produk</label>
                                <input type="text" name="nama_produk" class="form-control" value="<?= $is_edit ? htmlspecialchars($edit_data['nama_produk']) : '' ?>" placeholder="Contoh: Tenda Eiger 4P" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-secondary">Kategori</label>
                                <select name="kategori_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php 
                                    mysqli_data_seek($kategori_options, 0);
                                    while($kat = mysqli_fetch_assoc($kategori_options)) { 
                                        $selected = ($is_edit && $kat['id'] == $edit_data['kategori_id']) ? 'selected' : '';
                                        echo "<option value='{$kat['id']}' $selected>{$kat['nama_kategori']}</option>";
                                    } 
                                    ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-7 mb-3">
                                    <label class="form-label fw-bold small text-secondary">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" value="<?= $is_edit ? $edit_data['harga'] : '' ?>" placeholder="750000" required>
                                </div>
                                <div class="col-5 mb-3">
                                    <label class="form-label fw-bold small text-secondary">Stok</label>
                                    <input type="number" name="stok" class="form-control" value="<?= $is_edit ? $edit_data['stok'] : '0' ?>" min="0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-secondary">Deskripsi Produk</label>
                                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Spesifikasi atau deskripsi barang..."><?= $is_edit ? htmlspecialchars($edit_data['deskripsi']) : '' ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">Gambar Utama</label>
                                <?php if($is_edit && $edit_data['gambar']): ?>
                                    <img src="uploads/<?= $edit_data['gambar']; ?>" class="d-block mb-2 rounded border shadow-sm" width="60" style="height:60px; object-fit:cover;">
                                <?php endif; ?>
                                <input type="file" name="gambar" class="form-control">
                            </div>

                            <div class="d-grid gap-2 pt-2 border-top">
                                <button type="submit" class="btn btn-success fw-bold py-2 shadow-sm" style="background-color: #198754; border: none;">
                                    <?= $is_edit ? '💾 Perbarui Produk' : '🚀 Simpan ke Katalog' ?>
                                </button>
                                <?php if($is_edit): ?>
                                    <a href="tambah_produk.php" class="btn btn-outline-secondary py-2 fw-bold">❌ Batal</a>
                                <?php endif; ?>
                                
                                <hr class="my-1 text-muted">
                                
                                <a href="index.php" class="btn btn-outline-dark fw-bold py-2">
                                    ➡️ Ke Halaman Utama (Index)
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 col-lg-8">
                <div class="card shadow-sm card-custom">
                    <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span>📋 Item Terdaftar</span>
                        <span class="badge bg-secondary px-2 py-1" style="font-size: 11px;"><?= mysqli_num_rows($produk_query); ?> Unit</span>
                    </div>
                    <div class="card-body p-0 table-responsive bg-white">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th width="4%" class="text-center py-3">No</th>
                                    <th width="8%">Cover</th>
                                    <th width="43%">Detail Produk</th>
                                    <th width="15%">Kategori</th>
                                    <th width="15%">Nilai & Sisa</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if(mysqli_num_rows($produk_query) > 0) {
                                    while($row = mysqli_fetch_assoc($produk_query)) { 
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                        <td>
                                            <?php if($row['gambar'] && file_exists("uploads/" . $row['gambar'])): ?>
                                                <img src="uploads/<?= $row['gambar']; ?>" class="rounded shadow-sm border" style="width: 45px; height: 45px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary-subtle text-muted rounded text-center small d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px; font-size: 8px; font-weight: bold;">NO IMG</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark text-capitalize text-truncate" style="max-width: 400px;" title="<?= htmlspecialchars($row['nama_produk']); ?>">
                                                <?= htmlspecialchars($row['nama_produk']); ?>
                                            </div>
                                            <small class="text-muted text-truncate d-block" style="max-width: 400px; font-size: 11px;"><?= $row['deskripsi'] ? htmlspecialchars($row['deskripsi']) : '-' ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark text-uppercase" style="font-size: 9px; font-weight: 700; letter-spacing: 0.3px;">
                                                <?= htmlspecialchars($row['nama_kategori']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-success" style="font-size: 13px;">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></div>
                                            <div class="small mt-1 text-secondary" style="font-size: 11px;">
                                                Stok: <span class="badge <?= $row['stok'] > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> fw-bold" style="font-size: 10px;"><?= $row['stok']; ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm border rounded">
                                                <a href="tambah_produk.php?edit=<?= $row['id']; ?>" class="btn btn-light text-primary px-3 fw-bold border-end">Edit</a>
                                                <a href="tambah_produk.php?hapus=<?= $row['id']; ?>" class="btn btn-light text-danger px-3 fw-bold" onclick="return confirm('Hapus total item ini dari database?')">Hapus</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                    } 
                                } else {
                                    echo '<tr><td colspan="6" class="text-center py-5 text-muted fw-bold">📦 Belum ada data produk di database.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0 small">&copy; 2026 Outdoor Store Admin. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>