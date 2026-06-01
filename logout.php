<?php
// 1. Jalankan session sebelum bisa menghapusnya
session_start();

// 2. Bersihkan semua data variabel di dalam session
$_SESSION = array();

// 3. Hancurkan cookie session yang ada di browser (jika ada)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Hancurkan seluruh session yang tersisa di server
session_destroy();

// 5. UBAH DI SINI: Alihkan pengguna ke halaman login.php
header("Location: login.php"); 
exit;
?>