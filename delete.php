<!-- fungsi hapus data -->
<?php
include_once 'config/koneksi.php';

$id = $_GET['id'];
$query = "SELECT * FROM files WHERE id='$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$namafile = $row['namafile'];
unlink('uploads' . DIRECTORY_SEPARATOR . $namafile);
$querydelete = "DELETE FROM files WHERE id='$id'";
$delete = mysqli_query($conn, $querydelete);
header("Location: files.php");

?>
<!-- end -->