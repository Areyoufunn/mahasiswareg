<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  exit;
}

include("connection.php");

$error_message = "";

// 1. Ambil NIM dari URL (saat pertama kali diklik dari student_view.php)
if (isset($_GET["nim"])) {
    $nim_url = mysqli_real_escape_string($connection, $_GET["nim"]);
} else {
    // Jika tidak ada NIM di URL, kembalikan ke halaman utama
    header("Location: student_view.php");
    exit;
}

// 2. Jika form disubmit (proses UPDATE)
if (isset($_POST["submit"])) {
    $nim = htmlentities(strip_tags(trim($_POST["nim"])));
    $name = htmlentities(strip_tags(trim($_POST["name"])));
    $birth_city = htmlentities(strip_tags(trim($_POST["birth_city"])));
    $faculty = htmlentities(strip_tags(trim($_POST["faculty"])));
    $department = htmlentities(strip_tags(trim($_POST["department"])));
    $gpa = htmlentities(strip_tags(trim($_POST["gpa"])));
    $birth_date = htmlentities(strip_tags(trim($_POST["birth_date"])));
    $birth_month = htmlentities(strip_tags(trim($_POST["birth_month"])));
    $birth_year = htmlentities(strip_tags(trim($_POST["birth_year"])));

    // Validasi
    if (empty($name)) {
        $error_message .= "- Nama belum diisi <br>";
    }
    if (empty($birth_city)) {
        $error_message .= "- Tempat lahir belum diisi <br>";
    }
    if (empty($department)) {
        $error_message .= "- Jurusan belum diisi <br>";
    }

    // Format tanggal lahir untuk MySQL (YYYY-MM-DD)
    $format_birth_date = $birth_year . "-" . str_pad($birth_month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($birth_date, 2, "0", STR_PAD_LEFT);

    if ($error_message === "") {
        $name = mysqli_real_escape_string($connection, $name);
        $birth_city = mysqli_real_escape_string($connection, $birth_city);
        $faculty = mysqli_real_escape_string($connection, $faculty);
        $department = mysqli_real_escape_string($connection, $department);
        $gpa = mysqli_real_escape_string($connection, $gpa);

        // Query UPDATE
        $query = "UPDATE student SET 
                    name = '$name', 
                    birth_city = '$birth_city', 
                    birth_date = '$format_birth_date', 
                    faculty = '$faculty', 
                    department = '$department', 
                    gpa = '$gpa' 
                  WHERE nim = '$nim_url'";
                  
        $query_result = mysqli_query($connection, $query);

        if ($query_result) {
            $message = urlencode("Data mahasiswa dengan NIM $nim_url berhasil diubah.");
            header("Location: student_view.php?message=$message");
            exit;
        } else {
            die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
        }
    }
}

// 3. Ambil data lama dari database untuk ditampilkan di form
$query = "SELECT * FROM student WHERE nim = '$nim_url'";
$result = mysqli_query($connection, $query);
$data = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan di database
if (!$data) {
    die("Data mahasiswa tidak ditemukan.");
}

// Pecah tanggal lahir (YYYY-MM-DD) menjadi array
$date_parts = explode("-", $data["birth_date"]);
$db_birth_year = $date_parts[0];
$db_birth_month = (int)$date_parts[1];
$db_birth_date = (int)$date_parts[2];

// Array bulan
$arr_month = [
    "1" => "Januari", "2" => "Februari", "3" => "Maret", "4" => "April",
    "5" => "Mei", "6" => "Juni", "7" => "Juli", "8" => "Agustus",
    "9" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data Mahasiswa</title>
  <link href="assets/style.css" rel="stylesheet">
  <style>
    .container { width: 600px; margin: auto; }
    .error { background-color: #FFECEC; padding: 10px 15px; margin: 3px 3px 20px 3px; border: 1px solid red; }
  </style>
</head>
<body>
  <div class="container">
    <div id="header">
      <h1 id="logo">Data Mahasiswa</h1>
    </div>
    <hr>
    <nav>
      <ul>
        <li><a href="student_view.php">Tampil</a></li>
        <li><a href="student_add.php">Tambah</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
    
    <h2>Edit Data Mahasiswa</h2>
    
    <?php
    if ($error_message !== "") {
      echo "<div class='error'>$error_message</div>";
    }
    ?>
    
    <form action="" method="post">
      <fieldset>
        <legend>Biodata</legend>
        <p>
          <label for="nim">NIM : </label>
          <!-- NIM biasanya tidak boleh diedit (readonly), karena sebagai Primary Key -->
          <input type="text" name="nim" id="nim" value="<?php echo $data['nim']; ?>" readonly style="background-color: #eee;">
        </p>
        <p>
          <label for="name">Nama : </label>
          <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($data['name']); ?>">
        </p>
        <p>
          <label for="birth_city">Tempat Lahir : </label>
          <input type="text" name="birth_city" id="birth_city" value="<?php echo htmlspecialchars($data['birth_city']); ?>">
        </p>
        <p>
          <label for="birth_date">Tanggal Lahir : </label>
          <select name="birth_date" id="birth_date">
            <?php
            for ($i = 1; $i <= 31; $i++) {
                $selected = ($i == $db_birth_date) ? "selected" : "";
                echo "<option value='$i' $selected>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
            }
            ?>
          </select>
          <select name="birth_month">
            <?php
            foreach ($arr_month as $key => $value) {
                $selected = ($key == $db_birth_month) ? "selected" : "";
                echo "<option value='$key' $selected>$value</option>";
            }
            ?>
          </select>
          <select name="birth_year">
            <?php
            for ($i = 1990; $i <= 2005; $i++) {
                $selected = ($i == $db_birth_year) ? "selected" : "";
                echo "<option value='$i' $selected>$i</option>";
            }
            ?>
          </select>
        </p>
        <p>
          <label for="faculty">Fakultas : </label>
          <select name="faculty" id="faculty">
            <option value="FTIB" <?php if($data['faculty'] == 'FTIB') echo "selected"; ?>>FTIB</option>
            <option value="FTEIC" <?php if($data['faculty'] == 'FTEIC') echo "selected"; ?>>FTEIC</option>
          </select>
        </p>
        <p>
          <label for="department">Jurusan : </label>
          <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($data['department']); ?>">
        </p>
        <p>
          <label for="gpa">IPK : </label>
          <input type="text" name="gpa" id="gpa" value="<?php echo htmlspecialchars($data['gpa']); ?>"> 
          <small>(angka desimal dipisah dengan titik ".")</small>
        </p>
      </fieldset>
      <br>
      <p>
        <input type="submit" name="submit" value="Simpan Perubahan">
        <a href="student_view.php"><button type="button">Batal</button></a>
      </p>
    </form>
  </div>
  <?php mysqli_close($connection); ?>
</body>
</html>