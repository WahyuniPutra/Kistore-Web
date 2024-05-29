<?php
include 'koneksi.php';
session_start();
// Mulai sesi di awal file

if (isset($_POST["register"])) {
  // Ambil data dari formulir
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = $_POST["password"]; // Password yang dimasukkan oleh pengguna

  // Periksa apakah email sudah terdaftar
  $checkEmailQuery = "SELECT * FROM tb_user_login WHERE email=?";
  $checkEmailStmt = mysqli_prepare($conn, $checkEmailQuery);
  mysqli_stmt_bind_param($checkEmailStmt, "s", $email);
  mysqli_stmt_execute($checkEmailStmt);
  $checkEmailResult = mysqli_stmt_get_result($checkEmailStmt);

  if (mysqli_num_rows($checkEmailResult) > 0) {
    // Email sudah terdaftar, tampilkan pesan kesalahan
    echo '<script>alert("Akun dengan email tersebut sudah terdaftar!");</script>';
  } else {
    // Hash password menggunakan password_hash
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);



    // Simpan data ke database menggunakan prepared statement
    $insertQuery = "INSERT INTO tb_user_login (username, email, PASSWORD) VALUES (?, ?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($insertStmt, "sss", $username, $email, $hashedPassword);
    mysqli_stmt_execute($insertStmt);

    // Redirect atau tampilkan pesan sukses
    header("Location: account.php"); // Ganti account.php dengan halaman login yang sesuai
    exit();
  }
}

if (isset($_POST['login'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  $query = "SELECT * FROM tb_user_login WHERE email = '$email'";
  $result = mysqli_query($conn, $query);

  if ($email === 'admin@gmail.com' && $password == 'admin') {
    // Redirect admin to the dashboard
    $_SESSION['status'] = 'login';
    $_SESSION['email'] = $email;
    header("Location: Admin/dashboard.php");
    exit();
  } else {
    if ($result) {
      if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
          // Login successful, set session, redirect, etc.
          $_SESSION['email'] = $email;
          $_SESSION['status'] = 'login';
          header("Location: index.php?login=success");
          exit();
        } else {
          echo '<script>alert("Login failed. Incorrect password.");</script>';
        }
      } else {
        echo '<script>alert("Login failed. Email not found.");</script>';
      }
    } else {
      echo '<script>alert("Error in the database query.");</script>';
      // You might want to log the mysqli_error() for debugging purposes
    }
  }
}

?>



<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width-device-width, initial-scale=1.0" />
  <title>All Products - Redstore</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet" />
  <!--added a cdn link by searching font awesome4 cdn and getting this link from https://www.bootstrapcdn.com/fontawesome/ this url*/-->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>

<body>
  <!--<div class ="header">-->
  <div class="container">
    <div class="navbar">
      <div class="logo">
        <a href="index.php"><img src="images/kistore.png" width="125px" /></a>
      </div>
      <nav>
        <ul id="MenuItems">
          <li><a href="index.php">Home</a></li>
          <li><a href="products.php">Products</a></li>
          <li><a href="orderhistory.php">Order History</a></li>
          <li><a href="account.php">Login</a></li>
        </ul>
      </nav>
      <a href="cart.php"><img src="images/cart.png" width="30px" height="30px" /></a>
      <img src="images/menu.png" class="menu-icon" onClick="menutoggle()" />
    </div>
  </div>
  <!--</div>-->

  <!------------------------------ account-page details------------------------------>

  <div class="account-page">
    <div class="container">
      <div class="row">
        <div class="col-2">
          <img src="images/image1.png" width="100%" />
        </div>
        <div class="col-2">
          <div class="form-container">
            <div class="form-btn">
              <span onclick="login()">Login</span>
              <span onclick="register()">Register</span>
              <hr id="Indicator" />
            </div>
            <form id="LoginForm" method="post">
              <input type="email" placeholder="Email" name="email" />
              <input type="password" placeholder="Password" name="password" />
              <button type="submit" class="btn" name="login">Login</button>

            </form>

            <form id="RegForm" method="post">
              <input type="text" placeholder="Username" name="username" />
              <input type="email" placeholder="Email" name="email" />
              <input type="password" placeholder="Password" name="password" />
              <button type="submit" class="btn" name="register">Register</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!----------------------------------footer------------------------------------->
  <div class="footer">
    <div class="container">
      <div class="row">
        <div class="footer-col-2">
          <img src="images/kistore.png" />
          <p>
            Our Purpose Is To Sustainably Make the Pleasure and Benefits of
            Sports Accessible to the Many.
          </p>
        </div>

        <div class="footer-col-4">
          <h3>Kelompok 5</h3>
          <ul>
            <li>Atong Nazarius</li>
            <li>Ferry Saputra</li>
            <li>Rifky Mustaqim H.</li>
            <li>Ryan Delon P.</li>
            <li>Wahyuni Putra</li>
          </ul>
        </div>
      </div>

      <hr />
      <p class="copyright">2024 - Kelompok 5</p>
    </div>
  </div>

  <!-----------------------------------js for toggle menu-------------------------------------->
  <script>
    var menuItems = document.getElementById("MenuItems");

    MenuItems.style.maxHeight = "0px";

    function menutoggle() {
      if (MenuItems.style.maxHeight == "0px") {
        MenuItems.style.maxHeight = "200px";
      } else {
        MenuItems.style.maxHeight = "0px";
      }
    }
  </script>

  <!-----------------------------------js for toggle form-------------------------------------->
  <script>
    var LoginForm = document.getElementById("LoginForm");
    var RegForm = document.getElementById("RegForm");
    var Indicator = document.getElementById("Indicator");

    function register() {
      RegForm.style.transform = "translateX(0px)";
      LoginForm.style.transform = "translateX(0px)";
      Indicator.style.transform = "translateX(100px)";
    }

    function login() {
      RegForm.style.transform = "translateX(300px)";
      LoginForm.style.transform = "translateX(300px)";
      Indicator.style.transform = "translateX(0px)";
    }
  </script>


</body>

</html>