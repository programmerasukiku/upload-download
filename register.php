<?php
session_start();
include_once 'config/koneksi.php';

// cek tombol register jalan/tidak
if (isset($_POST['submit'])) {
	$nama = $_POST['nama'];
	// membersihkan slash, give text lowercase pada username
	$username = strtolower(stripcslashes($_POST['username']));
	$foto 	  = $_FILES['foto']['name'];
	$sizefile = $_FILES['foto']['size'];
	$error    = $_FILES['foto']['error'];
	$tmpname  = $_FILES['foto']['tmp_name'];
	$_SESSION['tempnama'] = $nama;
	$_SESSION['tempusername'] = $username;

	//checking ekstensi file
	$ekstensi = ['jpg', 'jpeg', 'png'];
	$ektensigambar = explode('.', $foto);
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

	// give tanda"" agar password safety
	$password = mysqli_real_escape_string($conn, $_POST['password']);
	$confpass = mysqli_real_escape_string($conn, $_POST['confpass']);
	$tgl    = date("Y-m-d");
	$result = mysqli_query($conn, "SELECT*FROM users WHERE username='$username'");

	// cek apakah form telah diisi semua
	if ($username and $password and $confpass) {
		// cek apakah username sudah ada/belum
		if ($cek = mysqli_num_rows($result)) {
			header("Location: register.php?error=u");
			exit;
		}

		// konfirmasi password
		if ($password == $confpass) {
			$password = password_hash($password, PASSWORD_DEFAULT);
			$query = mysqli_query($conn, "INSERT INTO users VALUES ('','$nama','$newfoto','$username', '$password', '$tgl', '')");
			header("Location: login.php?registrasi-sukses");
		} else
			header("Location: register.php?error=p");
		exit;
	}

	// jika form tidak terisi semua
	else {
		echo "<script> alert('Isi data dengan lengkap');
				</script>";
	}
}

?>

<!DOCTYPE html>
<html>

<head>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<!--Font Awesome-->
	<script src="https://kit.fontawesome.com/cc654c4eb9.js" crossorigin="anonymous"></script>

	<!-- Requirewordd meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Register</title>
</head>

<body>
	<?php
	if (isset($_GET['error'])) {
		$e = $_GET['error'];
		if ($e == "u") {
			echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
				<strong>Username unavailable!</strong> Choose another username.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				</div>';
		} else if ($e == "p") {
			echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
				<strong>Password mismatch!</strong> You should input same password.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				</div>';
		}
	}
	?>
	<!-- Navbar -->
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="index.php">Drive</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
			<div class="navbar-nav">
				<!-- <a class="nav-item nav-link" href="#">Pricing</a> -->
				<a href="register.php">
					<button type="button" class="btn btn-outline-primary mr-2 active">Sign Up</button>
				</a>
				<a href="login.php">
					<button type="button" class="btn btn-outline-primary">Sign In</button>
				</a>
			</div>
		</div>
	</nav>
	<!-- end Navbar -->
	<div class="container">
		<div class="row  justify-content-center align-items-center">
			<div class="col-6 mt-4">
				<h3>Register</h3>
				<form action="" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="username">Nama</label>
						<input type="text" name="nama" id="nama" class="form-control" value="<?php if (isset($_SESSION['tempnama'])) echo $_SESSION['tempnama']; ?>" required>
					</div>
					<div class="form-group">
						<label for="username">Username</label>
						<input type="text" name="username" id="username" class="form-control" value="<?php if (isset($_SESSION['tempusername'])) echo $_SESSION['tempusername']; ?>" required>
					</div>
					<div class="form-group">
						<label for="foto">Foto</label>
						<input type="file" class="form-control-file" id="foto" name="foto" accept="image/jpg, image/jpeg, image/png" required>
					</div>
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" name="password" id="password" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="confpass">Konfirmasi Password</label>
						<input type="password" name="confpass" id="confpass" class="form-control" required>
					</div>
					<button type="submit" class="btn btn-primary" name="submit">Submit</button>
				</form>
			</div>
		</div>
	</div>
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
	</script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
	</script>
</body>

</html>