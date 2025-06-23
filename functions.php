<?php
session_start();

/**
 * Fungsi untuk membersihkan input dari user (sanitasi)
 * Menggunakan real_escape_string untuk mencegah SQL injection
 * @param mysqli $conn Objek koneksi database
 * @param string $data Data yang akan disanitasi
 * @return string Data yang sudah disanitasi
 */
function sanitize_input($conn, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); // Mencegah XSS
    return $conn->real_escape_string($data);
}

/**
 * Fungsi untuk memverifikasi login pengguna
 * @param mysqli $conn Objek koneksi database
 * @param string $userId ID pengguna
 * @param string $password Password mentah
 * @return bool True jika login berhasil, false jika gagal
 */
function verify_login($conn, $userId, $password) {
    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT passw FROM users WHERE userId = ?");
    $stmt->bind_param("s", $userId); // 's' menandakan string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // VERIFIKASI PASSWORD MANUAL (PLAIN TEXT)
        if ($password === $row['passw']) { // Bandingkan password mentah dengan yang ada di DB
            return true;
        }
    }
    return false;
}

/**
 * Fungsi untuk memeriksa apakah user sudah login
 * @return bool True jika sudah login, false jika belum
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Fungsi untuk redirect ke halaman login jika belum login
 */
function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// Fungsi hash_password dihapus karena kita tidak menggunakan hashing lagi.
// Jika ada fungsi lain yang memanggilnya, harus dihapus juga.

/**
 * Fungsi untuk mendapatkan nama pengguna yang sedang login
 * @return string Nama pengguna atau 'Tamu'
 */
function get_current_user_name() {
    global $conn;
    if (is_logged_in()) {
        $stmt = $conn->prepare("SELECT nama FROM users WHERE userId = ?");
        $stmt->bind_param("s", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return $row['nama'];
        }
    }
    return 'Tamu';
}
?>