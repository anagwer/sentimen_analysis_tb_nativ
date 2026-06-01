<?php
session_start();
include_once('dbcon.php');

if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

$session_id = $conn->real_escape_string($_SESSION['ID']);
$user_query = $conn->query("SELECT * FROM users WHERE id_user = '$session_id'");
if (!$user_query || $user_query->num_rows === 0) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$user_row = $user_query->fetch_array();
$user_name = $user_row['username'];
?>