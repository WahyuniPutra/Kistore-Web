<?php
$host = 'localhost';
$user = 'dbwebsitetoko';
$pass = '12345678';
$db = 'dbwebsitetoko';

$conn = mysqli_connect($host, $user, $pass, $db);

if ($conn) {
  // echo "koneksi berhasil";
}

mysqli_select_db($conn, $db);
