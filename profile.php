<?php
session_start();
require "config/koneksi.php";
define("UPLOAD_DIR", "uploads/");
$id_user = $_SESSION['id_user'];
$user = $_SESSION['name'];
$foto = $_SESSION['foto'];
$tgl = $_SESSION['tgl'];
if (!empty($_FILES['myfile'])) {
    $namafile = $_FILES['myfile']['name'];
    $size = $_FILES['myfile']['size'];
    $date = date("Y-m-d");
    $extension = pathinfo($namafile, PATHINFO_EXTENSION);

    // filename yang aman
    $nama = preg_replace("/[^A-Z0-9._-]/i", "_", $namafile);

    // Mencegah overwrite
    $i = 0;
    $parts = pathinfo($nama);
    while (file_exists(UPLOAD_DIR . $nama)) {
        $i++;
        $nama = $parts["filename"] . "-" . $i . "." . $parts['extension'];
    }

    // uploading
    $tmpname = $_FILES['myfile']['tmp_name'];
    $upload = move_uploaded_file($tmpname, UPLOAD_DIR . $nama);
    $insert = mysqli_query($conn, "INSERT INTO files VALUES ('', '$date', '$nama', '$size', '$extension', '$id_user')");
}

// Time
date_default_timezone_set("Asia/Jakarta");
$t = time();
// G = 24-hour format of an hour without leading zeros
$jam = date("G", $t);
if ($jam >= 0 && $jam <= 12) {
    $waktu = "Good Morning";
} elseif ($jam > 12 && $jam <= 5) {
    $waktu = "Good Afternoon";
} else {
    $waktu = "Good Evening";
}

//Total isi drive
$userfile = mysqli_query($conn, "SELECT SUM(ukuran) FROM files WHERE id_user = $id_user");
$row = mysqli_fetch_assoc($userfile);
$giga = 500000000;
$percent = ($row['SUM(ukuran)'] / $giga) * 100;
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!--Font Awesome-->
    <script src="https://kit.fontawesome.com/cc654c4eb9.js" crossorigin="anonymous"></script>

    <title>Drive</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Drive</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="index.php">Home</a>
                <a class="nav-item nav-link active" href="profile.php">Profile<span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link" href="files.php">Files</a>
            </div>
        </div>
        <div class="navbar-nav">
            <div class="mr-2">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mymodal">
                    <i class="fas fa-upload"></i>
                </button>
            </div>
            <button type="button" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </nav>
    <!-- end navbar -->

    <!--Jumbotron-->
    <div class="jumbotron jumbotron-fluid">
        <div class="container text-center">
            <img src="profile/<?= $foto; ?>" width="25%" class="rounded-circle img-thumbnail">
            <h1 class="display-4"><?= $waktu; ?>, <?= $user; ?></h1>
            <p class="lead">Hope your day it's good.</p>
        </div>
    </div>
    <!--end Jumbotron-->

    <!-- Progress bar -->
    <div class="container">
        <h2>Storage</h2>
        <div class="progress">
            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percent; ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <h4>Your storage = <?= $percent; ?> / 500 MB</h4>
    </div>
    <!-- end Progress bar -->

    <!-- Modal box -->
    <div class="modal" id="mymodal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="file" name="myfile" id="file">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end Modal box -->
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>