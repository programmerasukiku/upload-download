<?php
include_once 'config/koneksi.php';

$id = $_GET['id'];
if (isset($_GET['id'])) {
$query = "DELETE FROM files WHERE id_user='$id'";
mysqli_query($conn, $query);}

$query = "DELETE FROM users WHERE id='$id'";
mysqli_query($conn, $query);
header("Location: index.php");
?>