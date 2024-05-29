<?php
include '../koneksi.php';
session_start();

if (isset($_POST['logout'])) {
  // Unset all session variables
  $_SESSION = array();

  // Destroy the session
  session_destroy();

  // Redirect to the home page (or any other desired location)
  header("Location: ../index.php");
  exit();
}

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Ambil nilai dari form
  $id_produk = $_POST['id_produk'];
  $articleType = $_POST['articleType'];
  $usageType = $_POST['usageType'];
  $productDisplayName = $_POST['productDisplayName'];

  // Ambil nilai gambar
  if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image']['name'];
    // Pindahkan file gambar ke folder "images"
    $target_dir = "../images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    // Set nilai $image sesuai dengan path lengkap file gambar
    $image = "../images/" . $image;
  } elseif (!empty($_POST['image_link'])) {
    $image_link = $_POST['image_link'];
    // Jika pengguna memasukkan tautan gambar, gunakan tautan tersebut
    $image = $image_link;
  } else {
    // Tidak ada gambar yang diunggah atau dihubungkan
    $image = ""; // atau berikan nilai default sesuai kebutuhan
  }

  $price = $_POST['price'];
  // $productDescription = $_POST['productDescription'];

  // Query untuk memeriksa keberadaan id_produk
  $check_query = "SELECT * FROM tb_produk WHERE id_produk = '$id_produk'";
  $check_result = mysqli_query($conn, $check_query);
  if (mysqli_num_rows($check_result) > 0) {
    echo '<script>alert("Id Produk Sudah Ada"); window.location.href = "dashboard.php";</script>';
    exit(); // Penting untuk menghentikan eksekusi script setelah alert
  } else {
    // Query untuk menambahkan data ke dalam tabel tb_produk
    $query = "INSERT INTO tb_produk (id_produk, articleType, usageType, productDisplayName, image, price) 
              VALUES ('$id_produk', '$articleType', '$usageType', '$productDisplayName', '$image', $price)";
    // Jalankan query
    if (mysqli_query($conn, $query)) {
      echo '<script>alert("Data berhasil ditambahkan"); window.location.href = "dashboard.php";</script>';
      exit(); // Penting untuk menghentikan eksekusi script setelah alert
    } else {
      echo '<script>alert("Data Gagal Ditambahkan"); window.location.href = "dashboard.php";</script>';
      exit(); // Penting untuk menghentikan eksekusi script setelah alert
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link rel="stylesheet" href="dashboard.css" />
</head>

<body>
  <div class="container">
    <nav>
      <ul>
        <li><a href="#" class="logo">
            <img id="logo_website" src="images/kistore.png" alt="">
            <span>Dashboard</span>
          </a></li>
        <li><a href="#" id="home-link">
            <i class="fas fa-home"></i>
            <span class="nav-item">Home</span>
          </a></li>
        <li><a href="#" id="input-link">
            <i class="fas fa-plus-circle product-icon"></i>
            <span class="nav-item">Input Produk</span>
          </a></li>
        <li><a href="#" id="updel-link">
            <i class="fas fa-tshirt product-icon"></i>
            <span class="nav-item">Daftar Produk</span>
          </a></li>
        <li><a href="#" id="account-link">
            <i class="fas fa-users user-icon"></i>
            <span class="nav-item">Daftar User</span>
          </a></li>
        <li>
          <form method="post" action="">
            <button type="submit" name="logout" class="logout-btn">
              <i class="fas fa-sign-out-alt"></i>
              <span>Logout</span>
            </button>
          </form>
        </li>
      </ul>
    </nav>

    <section class="main">
      <div class="main-top">
        <h1>Input</h1>
      </div>
      <section class="main-course">
        <h1>Formulir Input Produk</h1>
        <div class="course-box1">
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <!-- Input hidden untuk id_produk -->
            <label for="id_produk">ID Produk:</label>
            <input class="inputAtribut" type="text" id="id_produk" name="id_produk"><br>

            <label for="articleType">Article Type:</label>
            <input class="inputAtribut" type="text" id="articleType" name="articleType"><br>

            <label for="usageType">Usage Type:</label>
            <input class="inputAtribut" type="text" id="usageType" name="usageType"><br>

            <label for="productDisplayName">Product Display Name:</label>
            <input class="inputAtribut" type="text" id="productDisplayName" name="productDisplayName"><br>

            <!-- Input file untuk unggah gambar -->
            <label for="image">Image (Upload or Link):</label>
            <input class="inputAtribut" type="file" id="image" name="image">
            <p>Atau</p>
            <input class="inputAtribut" type="text" id="image_link" name="image_link" placeholder="Link Gambar"><br>

            <label for="price">Price:</label>
            <input class="inputAtribut" type="text" id="price" name="price" value="1"><br>

            <!-- <label for="productDescription">Product Description:</label>
            <textarea style="resize: none" class="inputAtribut" id="productDescription" name="productDescription"></textarea><br><br> -->

            <input type="submit" value="Submit">
          </form>
        </div>
      </section>
    </section>
  </div>
  <script>
    // Tambahkan event listener untuk setiap tautan
    document.getElementById("home-link").addEventListener("click", function(event) {
      event.preventDefault();
      window.location.href = "dashboard.php";
    });

    document.getElementById("input-link").addEventListener("click", function(event) {
      window.location.href = "dashboard_input.php";
    });

    document.getElementById("updel-link").addEventListener("click", function(event) {
      event.preventDefault();
      window.location.href = "dashboard_daftarproduk.php";
    });

    document.getElementById("account-link").addEventListener("click", function(event) {
      event.preventDefault();
      window.location.href = "dashboard_user.php";
    });
  </script>
</body>

</html>