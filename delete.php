<!-- fungsi hapus data -->
<?php
include_once 'config/koneksi.php';

$id = $_GET['id'];
$query = "DELETE FROM files WHERE id='$id'";
mysqli_query($conn, $query);
header("Location: files.php");

?>
<!-- end -->