<?php
session_start();
$id = $_SESSION['id_user'];
require "config/koneksi.php";
$query = mysqli_query($conn, "SELECT*FROM files WHERE id_user = $id");
$i = 0;

define("UPLOAD_DIR", "uploads/");
if (!empty($_FILES['myfile'])) {
    $id_user = $_SESSION['id_user'];
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

    <title>Files</title>
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
                <a class="nav-item nav-link" href="profile.php">Profile</a>
                <a class="nav-item nav-link active" href="files.php">Files <span class="sr-only">(current)</span></a>
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

    <!-- Table files -->
    <div class="container">
        <h1>Manage Your Files</h1>
        <table class="table table-hover mt-2">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Uploaded</th>
                    <th scope="col">Size</th>
                    <th scope="col">Type</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($query as $q) : ?>
                    <tr>
                        <th><?= ++$i; ?></th>
                        <td><?= $q['namafile'] ?></td>
                        <td><?= $q['tgl_upload'] ?></td>
                        <td><?= bytesToSize($q['ukuran']) ?></td>
                        <td><?= $q['tipe'] ?></td>
                        <td>
                            <a href="uploads/<?= $q['namafile'] ?>">
                                <button class="btn btn-success">Download</button>
                            </a>
                            <button class="btn btn-danger">Delete</button>
                        </td>
                    </tr>

                <?php endforeach; ?>
        </table>
    </div>
    <!-- end Table files -->
    <!-- Modal box -->
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
    <!-- end Modal box -->
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>