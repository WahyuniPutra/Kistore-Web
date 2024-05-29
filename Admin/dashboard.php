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

// Mengambil data total quantity dan total subtotal berdasarkan usageType dari tb_produk
$query = "SELECT p.usageType, SUM(o.Quantity) AS total_quantity, SUM(o.subtotal) AS total_subtotal 
            FROM tb_order_history o
            INNER JOIN tb_produk p ON o.id_produk = p.id_produk
            GROUP BY p.usageType";
$result = mysqli_query($conn, $query);

// Mengambil jumlah akun dari tb_user_login
$queryUserCount = "SELECT COUNT(email) AS user_count FROM tb_user_login";
$resultUserCount = mysqli_query($conn, $queryUserCount);
$userCount = mysqli_fetch_assoc($resultUserCount)['user_count'];

// Mengambil jumlah akun dari tb_user_login
$queryUserCount = "SELECT COUNT(id_produk) AS produk_count FROM tb_produk";
$resultProdukCount = mysqli_query($conn, $queryUserCount);
$produkCount = mysqli_fetch_assoc($resultProdukCount)['produk_count'];

// Inisialisasi array untuk menyimpan data
$labels = [];
$dataQuantity = [];
$dataSubtotal = [];

// Memproses hasil query
while ($row = mysqli_fetch_assoc($result)) {
  $labels[] = $row['usageType']; // Menambahkan usageType ke label
  $dataQuantity[] = $row['total_quantity']; // Menyimpan jumlah quantity
  $dataSubtotal[] = $row['total_subtotal']; // Menyimpan total subtotal
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
        <h1>Home</h1>
      </div>
      <section class="main-course">
        <h1>Analytics</h1>
        <div class="course-box">
          <canvas id="myChart" style="width: 800px;"></canvas>
        </div>
      </section>
      <div class="main-skills">
        <div class="card" id="user-count"> Jumlah Akun Terdaftar:<br> <?php echo $userCount; ?></div>
        <div class="card" id="product-count"> Jumlah Produk Terdaftar:<br> <?php echo $produkCount; ?></div>
        <div class="card"></div>
        <div class="card"></div>
      </div>
    </section>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Skrip untuk membuat grafik menggunakan Chart.js
    const ctx = document.getElementById('myChart');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Total Subtotal',
          data: <?php echo json_encode($dataSubtotal); ?>,
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }, {
          label: 'Total Quantity',
          data: <?php echo json_encode($dataQuantity); ?>,
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          borderColor: 'rgba(255, 99, 132, 1)',
          borderWidth: 1
        }]
      },
      options: {
        maintainAspectRatio: false,
        aspectRatio: 3, // Contoh rasio aspek 3:1
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
  <script>
    // Tambahkan event listener untuk setiap tautan
    document.getElementById("home-link").addEventListener("click", function(event) {
      event.preventDefault();
      window.location.href = "dashboard.php";
    });

    document.getElementById("input-link").addEventListener("click", function(event) {
      event.preventDefault();
      window.location.href = "dashboard_input.php";
    });


    document.getElementById("updel-link").addEventListener("click", function(event) {
      event.preventDefault();
      window.location.href = "dashboard_daftarproduk.php";
    });

    // Menavigasi ke halaman account list saat class "card" ditekan
    document.querySelectorAll("#user-count").forEach(card => {
      card.addEventListener("click", function() {
        window.location.href = "dashboard_user.php";
      });
    });

    document.querySelectorAll("#product-count").forEach(card => {
      card.addEventListener("click", function() {
        window.location.href = "dashboard_daftarproduk.php";
      });
    });

    document.getElementById("account-link").addEventListener("click", function(event) {
      event.preventDefault();
      window.location.href = "dashboard_user.php";
    });
  </script>
</body>

</html>