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
    if (isset($_POST['add_mahasiswa'])) {
        $nim = sanitize_input($conn, $_POST['nim']);
        $nama = sanitize_input($conn, $_POST['nama']);
        $tgl_lahir = sanitize_input($conn, $_POST['tgl_lahir']);
        $alamat = sanitize_input($conn, $_POST['alamat']);
        $agama = sanitize_input($conn, $_POST['agama']);
        $no_hp = sanitize_input($conn, $_POST['no_hp']);
        $email = sanitize_input($conn, $_POST['email']);
        $id_prodi = (int)$_POST['id_prodi'];

        $stmt = $conn->prepare("INSERT INTO mahasiswa (nim, nama, tgl_lahir, alamat, agama, no_hp, email, id_prodi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $nim, $nama, $tgl_lahir, $alamat, $agama, $no_hp, $email, $id_prodi);

        if ($stmt->execute()) {
            $message = "Data mahasiswa berhasil ditambahkan!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    } elseif (isset($_POST['edit_mahasiswa'])) {
        $nim_old = sanitize_input($conn, $_POST['nim_old']);
        $nim = sanitize_input($conn, $_POST['nim']); // New NIM if changed
        $nama = sanitize_input($conn, $_POST['nama']);
        $tgl_lahir = sanitize_input($conn, $_POST['tgl_lahir']);
        $alamat = sanitize_input($conn, $_POST['alamat']);
        $agama = sanitize_input($conn, $_POST['agama']);
        $no_hp = sanitize_input($conn, $_POST['no_hp']);
        $email = sanitize_input($conn, $_POST['email']);
        $id_prodi = (int)$_POST['id_prodi'];

        $stmt = $conn->prepare("UPDATE mahasiswa SET nim = ?, nama = ?, tgl_lahir = ?, alamat = ?, agama = ?, no_hp = ?, email = ?, id_prodi = ? WHERE nim = ?");
        $stmt->bind_param("sssssssis", $nim, $nama, $tgl_lahir, $alamat, $agama, $no_hp, $email, $id_prodi, $nim_old);

        if ($stmt->execute()) {
            $message = "Data mahasiswa berhasil diupdate!";
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
    $nim = sanitize_input($conn, $_GET['nim']);
    $stmt = $conn->prepare("DELETE FROM mahasiswa WHERE nim = ?");
    $stmt->bind_param("s", $nim);
    if ($stmt->execute()) {
        $message = "Data mahasiswa berhasil dihapus!";
        $message_type = "success";
    } else {
        $message = "Error: " . $stmt->error . " (Pastikan tidak ada relasi data lain yang bergantung)";
        $message_type = "danger";
    }
    $stmt->close();
    $action = 'read'; // Kembali ke tampilan baca data
}

// Get Data for Edit Form
$mahasiswa_data = [];
if ($action === 'edit') {
    $nim_edit = sanitize_input($conn, $_GET['nim']);
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE nim = ?");
    $stmt->bind_param("s", $nim_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $mahasiswa_data = $result->fetch_assoc();
    } else {
        $message = "Data mahasiswa tidak ditemukan.";
        $message_type = "danger";
        $action = 'read'; // Kembali ke tampilan baca
    }
    $stmt->close();
}

// Get All Prodi for dropdown
$prodi_list = [];
$result_prodi = $conn->query("SELECT id_prodi, nama_prodi FROM prodi ORDER BY nama_prodi");
while ($row = $result_prodi->fetch_assoc()) {
    $prodi_list[] = $row;
}
?>

<h2>Manajemen Data Mahasiswa</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <div class="card mb-4">
        <div class="card-header">
            <?= ($action === 'add') ? 'Tambah Data Mahasiswa Baru' : 'Edit Data Mahasiswa' ?>
        </div>
        <div class="card-body">
            <form action="mahasiswa.php" method="POST">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="nim_old" value="<?= htmlspecialchars($mahasiswa_data['nim']) ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="nim" class="form-label">NIM</label>
                    <input type="text" class="form-control" id="nim" name="nim" value="<?= ($action === 'edit') ? htmlspecialchars($mahasiswa_data['nim']) : '' ?>" required <?= ($action === 'edit' && !empty($mahasiswa_data['nim'])) ? '' : '' ?>>
                    </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= ($action === 'edit') ? htmlspecialchars($mahasiswa_data['nama']) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="<?= ($action === 'edit') ? htmlspecialchars($mahasiswa_data['tgl_lahir']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= ($action === 'edit') ? htmlspecialchars($mahasiswa_data['alamat']) : '' ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="agama" class="form-label">Agama</label>
                    <select class="form-select" id="agama" name="agama">
                        <option value="">Pilih Agama</option>
                        <option value="A" <?= ($action === 'edit' && $mahasiswa_data['agama'] == 'A') ? 'selected' : '' ?>>Islam</option>
                        <option value="B" <?= ($action === 'edit' && $mahasiswa_data['agama'] == 'B') ? 'selected' : '' ?>>Kristen</option>
                        <option value="C" <?= ($action === 'edit' && $mahasiswa_data['agama'] == 'C') ? 'selected' : '' ?>>Katolik</option>
                        <option value="D" <?= ($action === 'edit' && $mahasiswa_data['agama'] == 'D') ? 'selected' : '' ?>>Hindu</option>
                        <option value="E" <?= ($action === 'edit' && $mahasiswa_data['agama'] == 'E') ? 'selected' : '' ?>>Buddha</option>
                        <option value="F" <?= ($action === 'edit' && $mahasiswa_data['agama'] == 'F') ? 'selected' : '' ?>>Konghucu</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="no_hp" class="form-label">No. HP</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= ($action === 'edit') ? htmlspecialchars($mahasiswa_data['no_hp']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= ($action === 'edit') ? htmlspecialchars($mahasiswa_data['email']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="id_prodi" class="form-label">Program Studi</label>
                    <select class="form-select" id="id_prodi" name="id_prodi" required>
                        <option value="">Pilih Prodi</option>
                        <?php foreach ($prodi_list as $prodi): ?>
                            <option value="<?= $prodi['id_prodi'] ?>" <?= ($action === 'edit' && $mahasiswa_data['id_prodi'] == $prodi['id_prodi']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prodi['nama_prodi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="<?= ($action === 'add') ? 'add_mahasiswa' : 'edit_mahasiswa' ?>" class="btn btn-primary">
                    <?= ($action === 'add') ? 'Tambah' : 'Update' ?>
                </button>
                <a href="mahasiswa.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Daftar Mahasiswa
        <a href="mahasiswa.php?action=add" class="btn btn-success btn-sm">Tambah Mahasiswa</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Tgl Lahir</th>
                        <th>Alamat</th>
                        <th>Agama</th>
                        <th>No. HP</th>
                        <th>Email</th>
                        <th>Prodi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil data mahasiswa dengan join ke tabel prodi
                    $sql = "SELECT m.*, p.nama_prodi FROM mahasiswa m JOIN prodi p ON m.id_prodi = p.id_prodi ORDER BY m.nim ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $agama_text = '';
                            switch($row['agama']) {
                                case 'A': $agama_text = 'Islam'; break;
                                case 'B': $agama_text = 'Kristen'; break;
                                case 'C': $agama_text = 'Katolik'; break;
                                case 'D': $agama_text = 'Hindu'; break;
                                case 'E': $agama_text = 'Buddha'; break;
                                case 'F': $agama_text = 'Konghucu'; break;
                                default: $agama_text = 'Tidak Diketahui'; break;
                            }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nim']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['tgl_lahir']) ?></td>
                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                                <td><?= htmlspecialchars($agama_text) ?></td>
                                <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['nama_prodi']) ?></td>
                                <td>
                                    <a href="mahasiswa.php?action=edit&nim=<?= urlencode($row['nim']) ?>" class="btn btn-warning btn-sm me-1">Edit</a>
                                    <a href="mahasiswa.php?action=delete&nim=<?= urlencode($row['nim']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>Tidak ada data mahasiswa.</td></tr>";
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