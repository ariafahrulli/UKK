

<?php
// PHP_EOL untuk cross-platform newline
// error_reporting(E_ALL); // Aktifkan semua laporan kesalahan
// ini_set('display_errors', 1); // Tampilkan kesalahan di browser (hanya untuk development)

define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Default XAMPP user
define('DB_PASS', '');     // Default XAMPP password (kosong)
define('DB_NAME', 'db_mahasiswa');

// Buat koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set karakter set ke utf8
$conn->set_charset("utf8");
?>