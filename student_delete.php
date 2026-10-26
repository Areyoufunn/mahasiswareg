<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  exit;
}

include("connection.php");

$nim = $_GET["nim"];
$nim = mysqli_real_escape_string($connection, $nim);

$query = "DELETE FROM student WHERE nim = '$nim'";
$result = mysqli_query($connection, $query);

if($result) {
    $message = urlencode("Data mahasiswa dengan NIM $nim berhasil dihapus.");
    header("Location: student_view.php?message=$message");
} else {
    die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
}
mysqli_close($connection);
?>