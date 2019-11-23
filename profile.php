<?php
session_start();
require "config/login.php";
require "config/koneksi.php";
$id_user = $_SESSION['id_user'];

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
$sizefiles = $row['SUM(ukuran)'];
$percent = ($sizefiles / $giga) * 100;

define("UPLOAD_DIR", "uploads/");
$queryselect = "SELECT * FROM users WHERE id = $id_user";
$result = mysqli_query($conn, $queryselect);
$row = mysqli_fetch_assoc($result);
$user = $row['nama'];
$username = $row['username'];
$fotolama = $row['foto'];
$tgl = $row['tgl_reg'];

//uploading
if (!empty($_FILES['myfile'])) {
    $namafile = $_FILES['myfile']['name'];
    $size = $_FILES['myfile']['size'];
    $date = date("Y-m-d H:i:s");
    $extension = pathinfo($namafile, PATHINFO_EXTENSION);
    if ($size + $sizefiles > $giga) {
        $notenough = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Storage is not enough!</strong> You should delete some files
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>';
    } else {
        // filename yang aman
        $nama = preg_replace("/[^A-Z0-9._-]/i", "_", $namafile);

        // Mencegah overwrite
        $i = 0;
        //pathinfo() =  mengembalikan nilai berupa array yang berisi informasi path dari suatu link
        $parts = pathinfo($nama);
        while (file_exists(UPLOAD_DIR . $nama)) {
            $i++;
            $nama = $parts['filename'] . "-" . $i . "." . $parts['extension'];
        }

        // uploading
        $tmpname = $_FILES['myfile']['tmp_name'];
        $upload = move_uploaded_file($tmpname, UPLOAD_DIR . $nama);
        $insert = mysqli_query($conn, "INSERT INTO files VALUES ('', '$date', '$nama', '$size', '$extension', '$id_user')");
        echo "<meta http-equiv='refresh' content='0'>";
    }
}

//Edit/ Update data user
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $foto     = $_FILES['foto-update']['name'];
    $tmpname  = $_FILES['foto-update']['tmp_name'];
    if ($_FILES['foto-update']['error'] === 4) {
        $newfoto = $fotolama;
    } else {
        $ekstensi = ['jpg', 'jpeg', 'png'];
        $ektensigambar = explode('.', $foto);
        unlink('profile' . DIRECTORY_SEPARATOR . $fotolama);
        $ektensigambar = strtolower(end($ektensigambar));
        if (!in_array($ektensigambar, $ekstensi)) {
            echo "error";
            die;
            return false;
        }
        //generate nama gambar baru
        $newfoto = uniqid();
        $newfoto .= '.';
        $newfoto .= $ektensigambar;
        //gambar siap diupload
        move_uploaded_file($tmpname, 'profile/' . $newfoto);
    }
    $queryupdate = "UPDATE users SET
                    nama = '$nama',
                    foto = '$newfoto',
                    username = '$username'
                    WHERE id = '$id_user'
                    ";
    mysqli_query($conn, $queryupdate);
    echo "<meta http-equiv='refresh' content='0'>";
}

//change color
if ($percent <= 50) {
    $color = 'success';
} elseif ($percent > 50 && $percent <= 80) {
    $color = 'warning';
} else {
    $color = 'danger';
}

// convert size
function bytesToSize($bytes, $precision = 2)
{
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;

    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';
    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';
    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';
    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';
    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

//Time ago
$usertgl = mysqli_query($conn, "SELECT tgl_upload FROM files WHERE id_user = $id_user ORDER BY tgl_upload DESC");
$rowtgl = mysqli_fetch_assoc($usertgl);
$tgl_upload = $rowtgl['tgl_upload'];

function timeago($waktu_upload)
{
    $waktu_yang_lalu = strtotime($waktu_upload);
    $waktu_sekarang  = time();
    $perbedaan_waktu = $waktu_sekarang - $waktu_yang_lalu;
    $seconds = $perbedaan_waktu;
    $minutes = round($seconds / 60);
    $hours   = round($seconds / 3600);
    $days    = round($seconds / 86400);
    $weeks   = round($seconds / 604800);
    $months  = round($seconds / 2419200);

    if ($seconds < 60) {
        return "$seconds seconds ago";
    } elseif ($minutes < 60) {
        if ($minutes == 1) {
            return "A minutes ago";
        } else {
            return "$minutes minutes ago";
        }
    } elseif ($hours <= 24) {
        if ($hours == 1) {
            return "An hour ago";
        } else {
            return "$hours hours ago";
        }
    } elseif ($days < 7) {
        if ($days == 1) {
            return "Yesterday";
        } else {
            return "$days days ago";
        }
    } elseif ($weeks < 4) {
        if ($weeks == 1) {
            return "Last week";
        } else {
            return "$weeks weeks ago";
        }
    } else {
        if ($months == 1) {
            return "Last month";
        } else {
            return "$months months ago";
        }
    }
}
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
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalfile">
                    <i class="fas fa-upload"></i>
                </button>
            </div>
            <div class="mr-2">
                <button type="button" class="btn btn-warning text-white" data-toggle="modal" data-target="#modaledit">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            <div>
                <a href="logout.php">
                    <button type="button" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </a>
            </div>
        </div>
    </nav>
    <!-- end navbar -->

    <!--Jumbotron-->
    <div class="jumbotron jumbotron-fluid">
        <div class="container text-center">
            <img src="profile/<?= $fotolama; ?>" width="25%" class="rounded-circle img-thumbnail">
            <h1 class="display-4"><?= $waktu; ?>, <?= $user; ?></h1>
            <p class="lead">Hope your day it's good.</p>
        </div>
    </div>
    <!--end Jumbotron-->

    <!-- Progress bar -->
    <div class="container">
        <?php if (!empty($_FILES["myfile"])) echo $notenough; ?>
        <div class="row">
            <div class="col">
                <div class="card mb-3" style="max-width: 540px;">
                    <div class="row no-gutters">
                        <div class="col-md-4">
                            <img src="profile/<?= $fotolama; ?>" class="card-img" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title"><?= $user; ?></h5>
                                <p class="card-text">Joined on : <?= $tgl; ?></p>
                                <p class="card-text">Last uploading : <?= timeago($tgl_upload); ?> </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <h2>Storage</h2>
                <div class="progress">
                    <div class="progress-bar bg-<?= $color; ?>" role="progressbar" style="width: <?= $percent; ?>%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p>Your storage = <?= bytesToSize($sizefiles); ?> / 500 MB</p>
            </div>
        </div>
    </div>
    <!-- end Progress bar -->

    <!-- Modal box file -->
    <div class="modal" id="modalfile" tabindex="-1" role="dialog">
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
    <!-- end Modal box file -->

    <!-- Modal box edit -->
    <div class="modal" id="modaledit" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="username">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control" value="<?= $user; ?>">
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?= $username; ?>">
                        </div>
                        <div class="form-group">
                            <label for="foto">Foto</label>
                            <input type="file" class="form-control-file" id="foto" name="foto-update" accept="image/jpg, image/jpeg, image/png" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end Modal box edit -->

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