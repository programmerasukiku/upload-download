<?php
session_start();
include_once 'config/koneksi.php';
$result=mysqli_query($conn, "SELECT*FROM users WHERE NOT level='admin'");
$row=mysqli_fetch_assoc($result);

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


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
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

    <!-- jumbotron -->
    <div class="jumbotron jumbotron-fluid">
  <div class="container" align="center">
    <img src="https://image.winudf.com/v2/image/Y29tLmFkbWlueC5mYW50YXNodGVjaF9pY29uXzE1MzQwNjg3ODVfMDc5/icon.png?w=170&fakeurl=1">
    <h1 class="display-5">WELCOME TO <span style="color: blue;"><?=$_SESSION['admin']?></span></h1>
    <p class="lead">It's time only you who's made the another changes!<br>Let's Start...</p>
  </div>
</div>


<!-- card -->
<div class="container-fluid">
    <div class="row">
        <?php
            if (!empty($result)):?>
            <?php foreach ($result as $row):?>
        <div class="col-md-4 mt-3">
            <div class="card">
                <div class="card-horizontal">
                    <div class="img-square-wrapper">
                        <img class="" src="profile/<?= $row['foto']?>" width="100%" alt="Card image cap">
                    </div>
                    <div class="card-body">
                        <h4 class="card-title"><?=$row['nama']?></h4>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        <a href="deleteuser.php?id=<?=$row['id']?>" onclick="return confirm('Are you sure');" class="btn btn-danger">Delete</a>
                        <button type="button" class="btn btn-warning text-white" data-toggle="modal" data-target="#modaledit"><i class="fas fa-edit"></i></button>
                         <a href="file.php?id=<?=$row['id']?>" class="btn btn-primary">Details</a>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Last updated 3 mins ago</small>
                </div>
            </div>
        </div>
        <?php endforeach?>
            <?php endif;?>
    </div>
</div>

                
    
<!-- end card -->

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
</body>
</html>
