<?php
session_start();
include_once 'config/koneksi.php';

// cek tombol register jalan/tidak
if (isset($_POST['submit'])) {
	$nama = $_POST['nama'];
	$foto 	  = $_FILES['foto']['name'];
	$sizefile = $_FILES['foto']['size'];
	$error    = $_FILES['foto']['error'];
	$tmpname  = $_FILES['foto']['tmp_name'];
	//checking gambar
	if ($error === 4) {
		echo "<script>
                alert ('Isikan gambar terlebih dahulu')
                </script>";
		return false;
	}

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

	// membersihkan slash, give text lowercase pada username
	$username = strtolower(stripcslashes($_POST['username']));
	// give tanda"" agar password safety
	$password = mysqli_real_escape_string($conn, $_POST['password']);
	$confpass = mysqli_real_escape_string($conn, $_POST['confpass']);
	$tgl    = date("Y-m-d");
	$result = mysqli_query($conn, "SELECT*FROM users WHERE username='$username'");


	// cek apakah form telah diisi semua
	if ($username and $password and $confpass) {
		// cek apakah username sudah ada/belum
		if ($cek = mysqli_num_rows($result)) {
			echo "<script> alert('Username sudah terdaftar, Ulangi!');
				</script>";
			return false;
		}

		// konfirmasi password
		if ($password == $confpass) {
			$password = password_hash($password, PASSWORD_DEFAULT);
			$query = mysqli_query($conn, "INSERT INTO users VALUES ('','$nama','$newfoto','$username', '$password', '$tgl', '')");
			header("Location: login.php?registrasi-sukses");
		} else
			header("Location: register.php?konfirmasipassword-failed");
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
	<div class="jumbotron jumbotron-fluid">
		<div class="container">
			<h3 align="center">SIGN-UP</h3>
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">

					<form action="" method="post" enctype="multipart/form-data">
						<div class="form-group">
							<label for="username">Nama</label>
							<input type="text" name="nama" class="form-control">
						</div>
						<div class="form-group">
							<label for="username">Username</label>
							<input type="text" name="username" class="form-control">
						</div>
						<div class="form-group">
							<label for="foto">Foto</label>
							<input type="file" name="foto" accept="image/*" class="form-control">
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" name="password" class="form-control">
						</div>
						<div class="form-group">
							<label for="confpass">Konfirmasi Password</label>
							<input type="password" name="confpass" class="form-control">
						</div>
						<button type="submit" class="btn btn-primary" name="submit">Submit</button>
					</form>
				</div>
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