<?php
include 'koneksi.php';
session_start();
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>All Products - Redstore</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
  <style>
    #load-more-btn {
      background: #ff523b;
      color: #fff;
      padding: 8px 40px;
      margin: 40px auto;
      /* Mengatur margin auto akan menempatkannya di tengah */
      border-radius: 30px;
      display: block;
      /* Mengubahnya menjadi blok untuk menempatkannya di tengah */
      border: none;
      /* Menghapus border */
      cursor: pointer;
      outline: none;
      /* Menghapus efek focus */
      height: 50px;
      font-size: 15px;
    }

    #load-more-btn:hover {
      background-color: #ff7755;
      /* Ubah warna saat dihover */
    }

    #search-container {
      position: absolute;
      top: 15%;
    }

    #search-input {
      width: 300px;
      padding: 10px;
      border: 3px solid #ccc;
      border-radius: 5px;
      outline: none;
      /* Add outline */
      border-color: #ff523b;
      /* Change border color on focus */
    }
  </style>
</head>

<body>
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
          <?php
          if (isset($_SESSION['status']) && $_SESSION['status'] === 'login') {
            echo '<li><a href="logout.php">Logout</a></li>';
          } else {
            echo '<li><a href="account.php">Login</a></li>';
          }
          ?>
        </ul>
      </nav>
      <a href="cart.php"><img src="images/cart.png" width="30px" height="30px" /></a>
      <img src="images/menu.png" class="menu-icon" onClick="menutoggle()" />
    </div>
  </div>

  <div class="small-container">
    <div id="search-container">
      <input type="text" id="search-input" placeholder="Search products">
    </div>
    <div class="row row-2">
      <h2>All Products</h2>
    </div>
    <div id="product-container" class="row">
      <!-- Di sini akan ditambahkan produk melalui AJAX -->
    </div>
  </div>

  <button id="load-more-btn">Tampilkan Lebih Banyak</button>

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


  <script>
    // Toggle menu
    var MenuItems = document.getElementById("MenuItems");
    MenuItems.style.maxHeight = "0px";

    function menutoggle() {
      if (MenuItems.style.maxHeight == "0px") {
        MenuItems.style.maxHeight = "200px";
      } else {
        MenuItems.style.maxHeight = "0px";
      }
    }
  </script>

  <script>
    // Load initial products
    var currentPage = 1;

    function loadInitialProducts() {
      var keyword = document.getElementById('search-input').value.trim();
      var url = 'load-more.php?page=' + currentPage;
      if (keyword !== '') {
        url += '&keyword=' + keyword;
      }
      var xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.onload = function() {
        if (this.status == 200) {
          document.getElementById('product-container').innerHTML = this.responseText;
          currentPage++;
        }
      }
      xhr.send();
    }
    window.onload = loadInitialProducts;
  </script>

  <script>
    document.getElementById('search-input').addEventListener('keypress', function(event) {
      // Check if the key pressed is Enter (key code 13)
      if (event.keyCode === 13) {
        var keyword = this.value.trim();
        if (keyword !== '') {
          var url = 'load-more.php?page=1&keyword=' + keyword;
          var xhr = new XMLHttpRequest();
          xhr.open('GET', url, true);
          xhr.onload = function() {
            if (this.status == 200) {
              document.getElementById('product-container').innerHTML = this.responseText;
              currentPage = 2; // Reset the currentPage to 2 to allow "Load More" button to work
            }
          };
          xhr.send();
        } else {
          // If keyword is empty, reload the page to show initial products
          location.reload();
        }
      }
    });
  </script>


  <script>
    // Load more products
    document.getElementById('load-more-btn').addEventListener('click', function() {
      var keyword = document.getElementById('search-input').value.trim();
      var url = 'load-more.php?page=' + currentPage;
      if (keyword !== '') {
        url += '&keyword=' + keyword;
      }
      var xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.onload = function() {
        if (this.status == 200) {
          document.getElementById('product-container').innerHTML += this.responseText; // Menggunakan += agar menambahkan produk baru tanpa menghapus yang sudah ada
          currentPage++;
        }
      }
      xhr.send();
    });
  </script>
</body>

</html>