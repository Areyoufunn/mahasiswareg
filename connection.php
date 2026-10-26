
<?php
  $dbhost = "192.168.10.80";
  $dbuser = "root";
  $dbpass = "123";
  $dbname = "my_campus";
  $connection = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
  
  if(!$connection){
    die ("Koneksi dengan database gagal: ".mysqli_connect_errno()." - ".mysqli_connect_error());
  }
?>

