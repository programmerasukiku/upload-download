<?php
session_start();
include_once 'config/koneksi.php';

// cek apakah tombol login sudah jalan/belum
if (isset($_POST['login'])) {

	// membersihkan slash, give text lowercase pada username
	$username = $_POST['username'];
	$password = $_POST['password'];
	$result = mysqli_query($conn, "SELECT*FROM users WHERE username='$username'");

	// cek ada/tidak username dalam db
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$id = $row['id'];

		if (password_verify($password, $row['password'])) {
			$_SESSION['id_user'] = $id;
			$_SESSION['login'] = true;
			header("Location: profile.php");
		}
	}
	$error = true;
}
?>

<!DOCTYPE html>
<html>

<head>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<!--Font Awesome-->
	<script src="https://kit.fontawesome.com/cc654c4eb9.js" crossorigin="anonymous"></script>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Login</title>
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
				<!-- <a class="nav-item nav-link" href="#">Pricing</a> -->
				<button type="button" class="btn btn-primary mr-2">Sign Up</button>
				<button type="button" class="btn btn-outline-primary">Sign In</button>
			</div>
		</div>
	</nav>
	<!-- end Navbar -->

	<div class="container">
		<h3 class="text-center">LOGIN</h3>
		<div class="row">
			<div class="col-md-4"></div>
			<div class="col-md-4">

				<!-- jika username/password tidak sesuai db -->
				<?php if (isset($error)) : ?>
					<p style="color: red;">username/password anda salah</p>
				<?php endif; ?>

				<form action="" method="post">
					<div class="form-group">
						<label for="username">Username</label>
						<input type="text" name="username" class="form-control">
					</div>

					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" name="password" class="form-control">
					</div>
					<input type="submit" name="login" class="btn btn-dark" value="Login">
					<a href="register.php" class="btn btn-primary float-right">Sign Up</a>
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