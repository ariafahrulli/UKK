<?php
include 'config.php';
include 'functions.php';
redirect_if_not_logged_in(); // Pastikan user sudah login
include 'header.php';
?>
        <h2>Selamat Datang, <?= get_current_user_name() ?>!</h2>
        <p>Gunakan menu navigasi di atas untuk mengelola data mahasiswa, jurusan, dan program studi.</p>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-user-graduate fa-4x text-primary mb-3"></i>
                        <h5 class="card-title">Data Mahasiswa</h5>
                        <p class="card-text">Kelola informasi lengkap mahasiswa.</p>
                        <a href="mahasiswa.php" class="btn btn-primary">Lihat Data</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-building fa-4x text-success mb-3"></i>
                        <h5 class="card-title">Data Jurusan</h5>
                        <p class="card-text">Kelola data jurusan yang tersedia.</p>
                        <a href="jurusan.php" class="btn btn-success">Lihat Data</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-book fa-4x text-info mb-3"></i>
                        <h5 class="card-title">Data Program Studi</h5>
                        <p class="card-text">Kelola program studi dan relasinya dengan jurusan.</p>
                        <a href="prodi.php" class="btn btn-info">Lihat Data</a>
                    </div>
                </div>
            </div>
        </div>
<?php
include 'footer.php';
$conn->close();
?>