<?php
include 'config.php';
include 'functions.php';
redirect_if_not_logged_in();
include 'header.php';

$action = isset($_GET['action']) ? sanitize_input($conn, $_GET['action']) : 'read';
$message = '';
$message_type = '';

// Get All Jurusan for dropdown
$jurusan_list = [];
$result_jurusan = $conn->query("SELECT id_jurusan, nama_jurusan FROM jurusan ORDER BY nama_jurusan");
while ($row = $result_jurusan->fetch_assoc()) {
    $jurusan_list[] = $row;
}

// Proses Form Submit (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_prodi'])) {
        $nama_prodi = sanitize_input($conn, $_POST['nama_prodi']);
        $id_jurusan = (int)$_POST['id_jurusan'];

        $stmt = $conn->prepare("INSERT INTO prodi (nama_prodi, id_jurusan) VALUES (?, ?)");
        $stmt->bind_param("si", $nama_prodi, $id_jurusan);

        if ($stmt->execute()) {
            $message = "Data program studi berhasil ditambahkan!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    } elseif (isset($_POST['edit_prodi'])) {
        $id_prodi = (int)$_POST['id_prodi'];
        $nama_prodi = sanitize_input($conn, $_POST['nama_prodi']);
        $id_jurusan = (int)$_POST['id_jurusan'];

        $stmt = $conn->prepare("UPDATE prodi SET nama_prodi = ?, id_jurusan = ? WHERE id_prodi = ?");
        $stmt->bind_param("sii", $nama_prodi, $id_jurusan, $id_prodi);

        if ($stmt->execute()) {
            $message = "Data program studi berhasil diupdate!";
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
    $id_prodi = (int)$_GET['id'];
    // Periksa apakah ada mahasiswa yang terkait dengan prodi ini
    $stmt_check = $conn->prepare("SELECT COUNT(*) AS total FROM mahasiswa WHERE id_prodi = ?");
    $stmt_check->bind_param("i", $id_prodi);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    $stmt_check->close();

    if ($row_check['total'] > 0) {
        $message = "Gagal menghapus! Terdapat " . $row_check['total'] . " mahasiswa yang terdaftar di program studi ini.";
        $message_type = "danger";
    } else {
        $stmt = $conn->prepare("DELETE FROM prodi WHERE id_prodi = ?");
        $stmt->bind_param("i", $id_prodi);
        if ($stmt->execute()) {
            $message = "Data program studi berhasil dihapus!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
    $action = 'read';
}

// Get Data for Edit Form
$prodi_data = [];
if ($action === 'edit') {
    $id_prodi_edit = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM prodi WHERE id_prodi = ?");
    $stmt->bind_param("i", $id_prodi_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $prodi_data = $result->fetch_assoc();
    } else {
        $message = "Data program studi tidak ditemukan.";
        $message_type = "danger";
        $action = 'read';
    }
    $stmt->close();
}
?>

<h2>Manajemen Data Program Studi</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <div class="card mb-4">
        <div class="card-header">
            <?= ($action === 'add') ? 'Tambah Data Program Studi Baru' : 'Edit Data Program Studi' ?>
        </div>
        <div class="card-body">
            <form action="prodi.php" method="POST">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id_prodi" value="<?= htmlspecialchars($prodi_data['id_prodi']) ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="nama_prodi" class="form-label">Nama Program Studi</label>
                    <input type="text" class="form-control" id="nama_prodi" name="nama_prodi" value="<?= ($action === 'edit') ? htmlspecialchars($prodi_data['nama_prodi']) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label for="id_jurusan" class="form-label">Jurusan</label>
                    <select class="form-select" id="id_jurusan" name="id_jurusan" required>
                        <option value="">Pilih Jurusan</option>
                        <?php foreach ($jurusan_list as $jurusan): ?>
                            <option value="<?= $jurusan['id_jurusan'] ?>" <?= ($action === 'edit' && $prodi_data['id_jurusan'] == $jurusan['id_jurusan']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($jurusan['nama_jurusan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="<?= ($action === 'add') ? 'add_prodi' : 'edit_prodi' ?>" class="btn btn-primary">
                    <?= ($action === 'add') ? 'Tambah' : 'Update' ?>
                </button>
                <a href="prodi.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Daftar Program Studi
        <a href="prodi.php?action=add" class="btn btn-success btn-sm">Tambah Prodi</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Prodi</th>
                        <th>Nama Prodi</th>
                        <th>Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.*, j.nama_jurusan FROM prodi p JOIN jurusan j ON p.id_jurusan = j.id_jurusan ORDER BY p.nama_prodi ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id_prodi']) ?></td>
                                <td><?= htmlspecialchars($row['nama_prodi']) ?></td>
                                <td><?= htmlspecialchars($row['nama_jurusan']) ?></td>
                                <td>
                                    <a href="prodi.php?action=edit&id=<?= urlencode($row['id_prodi']) ?>" class="btn btn-warning btn-sm me-1">Edit</a>
                                    <a href="prodi.php?action=delete&id=<?= urlencode($row['id_prodi']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini? Semua mahasiswa yang terkait harus dihapus terlebih dahulu.');">Hapus</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>Tidak ada data program studi.</td></tr>";
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