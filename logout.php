<?php
session_start();
if (isset($_POST['save']) || isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

