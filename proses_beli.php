<?php
include 'koneksi.php';

// Menangkap kiriman form POST dari tombol final halaman nota_pembayaran.php
if (isset($_POST['konfirmasi_bayar'])) {
    
    // Menggunakan $_POST untuk mengambil data kiriman form secara aman
    $id_produk    = (int)$_POST['id_produk'];
    $metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);

    // 1. Ambil stok barang dari database
    $query_check = mysqli_query($koneksi, "SELECT nama_produk, stok FROM produk WHERE id = $id_produk");

    if (mysqli_num_rows($query_check) > 0) {
        $produk = mysqli_fetch_assoc($query_check);
        $stok_sekarang = (int)$produk['stok'];
        $nama_barang   = urlencode($produk['nama_produk']); 

        // 2. Cek apakah stok di atas 0
        if ($stok_sekarang > 0) {
            $stok_baru = $stok_sekarang - 1;

            // 3. Eksekusi pemotongan stok di database
            $query_update = mysqli_query($koneksi, "UPDATE produk SET stok = $stok_baru WHERE id = $id_produk");

            if ($query_update) {
                // Berhasil! Beralih kembali ke index.php dengan status sukses
                header("Location: index.php?beli_status=success&nama_barang=$nama_barang&metode=$metode_bayar");
                exit;
            } else {
                echo "Gagal memotong stok: " . mysqli_error($koneksi);
            }
        } else {
            // Jika ternyata stok keburu habis dipesan orang lain
            header("Location: index.php?beli_status=habis");
            exit;
        }
    } else {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?><?php
include 'koneksi.php';

// Menangkap kiriman form POST dari tombol final halaman nota_pembayaran.php
if (isset($_POST['konfirmasi_bayar'])) {
    
    // Menggunakan $_POST untuk mengambil data kiriman form secara aman
    $id_produk    = (int)$_POST['id_produk'];
    $metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);

    // 1. Ambil stok barang dari database
    $query_check = mysqli_query($koneksi, "SELECT nama_produk, stok FROM produk WHERE id = $id_produk");

    if (mysqli_num_rows($query_check) > 0) {
        $produk = mysqli_fetch_assoc($query_check);
        $stok_sekarang = (int)$produk['stok'];
        $nama_barang   = urlencode($produk['nama_produk']); 

        // 2. Cek apakah stok di atas 0
        if ($stok_sekarang > 0) {
            $stok_baru = $stok_sekarang - 1;

            // 3. Eksekusi pemotongan stok di database
            $query_update = mysqli_query($koneksi, "UPDATE produk SET stok = $stok_baru WHERE id = $id_produk");

            if ($query_update) {
                // Berhasil! Beralih kembali ke index.php dengan status sukses
                header("Location: index.php?beli_status=success&nama_barang=$nama_barang&metode=$metode_bayar");
                exit;
            } else {
                echo "Gagal memotong stok: " . mysqli_error($koneksi);
            }
        } else {
            // Jika ternyata stok keburu habis dipesan orang lain
            header("Location: index.php?beli_status=habis");
            exit;
        }
    } else {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>