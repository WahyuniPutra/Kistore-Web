<?php
include 'koneksi.php';
session_start();

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width-device-width, initial-scale=1.0" />
  <title>Redstore | Ecommerce website</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet" />
  <!--added a cdn link by searching font awesome4 cdn and getting this link from https://www.bootstrapcdn.com/fontawesome/ this url*/-->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>

<body>
  <div class="header">
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
              // User is logged in
              echo '<li><a href="logout.php">Logout</a></li>';
            } else {
              // User is not logged in
              echo '<li><a href="account.php">Login</a></li>';
            }
            ?>
          </ul>
        </nav>
        <a href="cart.php"><img src="images/cart.png" width="30px" height="30px" /></a>
        <img src="images/menu.png" class="menu-icon" onClick="menutoggle()" />
      </div>
      <div class="row">
        <div class="col-2">
          <h1>Give your Workout <br />A New Style!</h1>
          <p>
            Success isn't always about greatness. It's about consistency.
            Consistent<br />hard work gains success. Greatness will come.
          </p>
          <a href="products.php" class="btn">Explore Now &#8594;</a>
        </div>
        <div class="col-2">
          <img src="images/image1.png" />
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

  <!-----------------------------------js for toggle menu----------------------------------------------->
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


</body>

</html>