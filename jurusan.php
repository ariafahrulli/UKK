<?php
include 'config.php';
include 'functions.php';
redirect_if_not_logged_in();
include 'header.php';

$action = isset($_GET['action']) ? sanitize_input($conn, $_GET['action']) : 'read';
$message = '';
$message_type = '';

// Proses Form Submit (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_jurusan'])) {
        $nama_jurusan = sanitize_input($conn, $_POST['nama_jurusan']);

        $stmt = $conn->prepare("INSERT INTO jurusan (nama_jurusan) VALUES (?)");
        $stmt->bind_param("s", $nama_jurusan);

        if ($stmt->execute()) {
            $message = "Data jurusan berhasil ditambahkan!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    } elseif (isset($_POST['edit_jurusan'])) {
        $id_jurusan = (int)$_POST['id_jurusan'];
        $nama_jurusan = sanitize_input($conn, $_POST['nama_jurusan']);

        $stmt = $conn->prepare("UPDATE jurusan SET nama_jurusan = ? WHERE id_jurusan = ?");
        $stmt->bind_param("si", $nama_jurusan, $id_jurusan);

        if ($stmt->execute()) {
            $message = "Data jurusan berhasil diupdate!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
}

// Proses Delete
if ($action === 'delete') {
    $id_jurusan = (int)$_GET['id'];
    // Periksa apakah ada prodi yang terkait dengan jurusan ini
    $stmt_check = $conn->prepare("SELECT COUNT(*) AS total FROM prodi WHERE id_jurusan = ?");
    $stmt_check->bind_param("i", $id_jurusan);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    $stmt_check->close();

    if ($row_check['total'] > 0) {
        $message = "Gagal menghapus! Terdapat " . $row_check['total'] . " program studi yang terkait dengan jurusan ini.";
        $message_type = "danger";
    } else {
        $stmt = $conn->prepare("DELETE FROM jurusan WHERE id_jurusan = ?");
        $stmt->bind_param("i", $id_jurusan);
        if ($stmt->execute()) {
            $message = "Data jurusan berhasil dihapus!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
    $action = 'read'; // Kembali ke tampilan baca data
}

// Get Data for Edit Form
$jurusan_data = [];
if ($action === 'edit') {
    $id_jurusan_edit = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM jurusan WHERE id_jurusan = ?");
    $stmt->bind_param("i", $id_jurusan_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $jurusan_data = $result->fetch_assoc();
    } else {
        $message = "Data jurusan tidak ditemukan.";
        $message_type = "danger";
        $action = 'read';
    }
    $stmt->close();
}
?>

<h2>Manajemen Data Jurusan</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <div class="card mb-4">
        <div class="card-header">
            <?= ($action === 'add') ? 'Tambah Data Jurusan Baru' : 'Edit Data Jurusan' ?>
        </div>
        <div class="card-body">
            <form action="jurusan.php" method="POST">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id_jurusan" value="<?= htmlspecialchars($jurusan_data['id_jurusan']) ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="nama_jurusan" class="form-label">Nama Jurusan</label>
                    <input type="text" class="form-control" id="nama_jurusan" name="nama_jurusan" value="<?= ($action === 'edit') ? htmlspecialchars($jurusan_data['nama_jurusan']) : '' ?>" required>
                </div>
                <button type="submit" name="<?= ($action === 'add') ? 'add_jurusan' : 'edit_jurusan' ?>" class="btn btn-primary">
                    <?= ($action === 'add') ? 'Tambah' : 'Update' ?>
                </button>
                <a href="jurusan.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Daftar Jurusan
        <a href="jurusan.php?action=add" class="btn btn-success btn-sm">Tambah Jurusan</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Jurusan</th>
                        <th>Nama Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM jurusan ORDER BY nama_jurusan ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id_jurusan']) ?></td>
                                <td><?= htmlspecialchars($row['nama_jurusan']) ?></td>
                                <td>
                                    <a href="jurusan.php?action=edit&id=<?= urlencode($row['id_jurusan']) ?>" class="btn btn-warning btn-sm me-1">Edit</a>
                                    <a href="jurusan.php?action=delete&id=<?= urlencode($row['id_jurusan']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini? Semua prodi yang terkait harus dihapus terlebih dahulu.');">Hapus</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>Tidak ada data jurusan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
include 'footer.php';
$conn->close();
?>