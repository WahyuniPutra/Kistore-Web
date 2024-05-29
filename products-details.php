<?php
include 'koneksi.php';
session_start();

$query1 = "SELECT * FROM tb_produk;";
$sql1 = mysqli_query($conn, $query1);

if (isset($_GET['id_produk'])) {
  $product_id = $_GET['id_produk'];

  $query = "SELECT * FROM tb_produk WHERE id_produk = $product_id";
  $sql = mysqli_query($conn, $query);

  if (mysqli_num_rows($sql) > 0) {
    $result = mysqli_fetch_assoc($sql);
  } else {
    header("Location: products.php");
    exit;
  }
} else {
  header("Location: products.php");
  exit;
}

if (isset($_POST['add_to_cart'])) {
  if (isset($_SESSION['email']) && $_SESSION['status'] === 'login') {
    $quantity = $_POST['quantity'];
    $product_display_name = mysqli_real_escape_string($conn, $result['productDisplayName']);
    $imageCart = mysqli_real_escape_string($conn, $result['image']);
    $product_price = $result['price'];
    $subtotal = $quantity * $product_price;
    $user_email = $_SESSION['email'];

    $insert_query = "INSERT INTO tb_cart (id_produk, CartDisplayName, ImageCart, Quantity, subTotal, email) 
                        VALUES ('{$result['id_produk']}', '$product_display_name', '$imageCart', $quantity, $subtotal, '$user_email')";
    mysqli_query($conn, $insert_query);

    // Redirect ke halaman detail produk setelah berhasil menambahkan ke keranjang
    header("Location: products-details.php?id_produk={$result['id_produk']}");
    exit();
  } else {
    echo '<script>alert("Login required to add to cart.");</script>';
    echo '<script>window.location.href="account.php";</script>';
    exit();
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width-device-width, initial-scale=1.0" />
  <title><?php echo $result['productDisplayName']; ?></title>
  <link rel="stylesheet" href="style.css" />
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
  <style>
    .product-description {
      word-wrap: break-word;
      /* Atur word-wrap agar teks bisa dipecah ke baris baru saat mencapai batas kontainer */
    }

    #ai_Button {
      display: inline-block;
      background: #ff523b;
      color: #fff;
      padding: 8px 30px;
      margin: 15px 0px;
      border-radius: 30px;
      transition: background 0.5s;
      border: none;
    }

    #ai_Button:hover {
      background: #808080;
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
          // Check if the user is logged in
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
  </div>

  <!------------------------------ Single product details------------------------------>
  <div class="small-container single-product">
    <div class="row">
      <div class="col-2">
        <img src="<?php echo $result['image']; ?>" width="100%" id="productImg" />
      </div>
      <div class="col-2">
        <form method="POST">
          <p>Home / <?php echo $result['articleType']; ?> </p>
          <h1><?php echo $result['productDisplayName']; ?></h1>
          <h4>Rp. <?php echo $result['price']; ?></h4>
          <input type="number" value="1" min="1" max="99" name="quantity" />
          <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
        </form>
        <h3>Description Product <i class="fa fa-indent"></i></h3>
        <br />
        <div id="response"></div>
        <p class="product-description">
          <button id="ai_Button" onclick='generateResponse();'>Generate</button>
          <input style="display:none" type='text' id='text' value="Deskripsi Produk <?php echo $result['productDisplayName']; ?> sebanyak 100 kata">
      </div>
      </p>
    </div>
  </div>
  </div>

  <!----------------------------------Title------------------------------------->
  <div class="small-container">
    <div class="row row-2">
      <h2>Related Products</h2>
      <a href="products.php">
        <p>View More</p>
      </a>
    </div>
  </div>

  <!---------------------------------- Relate products------------------------------------->
  <div class="small-container">
    <?php
    $products_per_row = 4;
    $total_products = mysqli_num_rows($sql1);
    $total_rows = 1;
    for ($row = 0; $row < $total_rows; $row++) {
    ?>
      <div class="row">
        <?php
        for ($i = 0; $i < $products_per_row; $i++) {
          if ($result = mysqli_fetch_assoc($sql1)) {
            // Menyimpan ID produk
            $product_id = $result['id_produk']; // Sesuaikan dengan nama kolom ID produk di database
        ?>
            <div class="col-4">
              <a href="products-details.php?id_produk=<?php echo $product_id; ?>"> <!-- Menambahkan ID produk ke URL -->
                <img src="<?php echo $result['image']; ?>">
              </a>

              <a href="products-details.php?id_produk=<?php echo $product_id; ?>">
                <h4> <!-- Menambahkan ID produk ke URL -->
                  <?php echo $result['productDisplayName']; ?>
                </h4>
              </a>
              <div class="rating">
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star-half-o"></i>
                <i class="fa fa-star-o"></i>
              </div>
              <p>Rp. <?php echo $result['price']; ?></p>
            </div>
        <?php
          }
        }
        ?>
      </div>
    <?php
    }
    ?>
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

  <script src="script.js"></script>
  <!-----------------------------------js for toggle menu----------------------------------------------->
  <script>
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
</body>

</html>