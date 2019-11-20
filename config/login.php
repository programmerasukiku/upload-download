<?php
if (!isset($_SESSION['login'])) {
    header("Location: login.php?status=0");
    die;
}
