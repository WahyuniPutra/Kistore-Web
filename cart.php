<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['email']) || $_SESSION['status'] !== 'login') {
  echo '<script>alert("Login terlebih dahulu.");</script>';
  echo '<script>window.location.href="account.php";</script>';
  exit();
}

$email = $_SESSION['email'];

// Tampilkan data produk dari tb_cart di halaman cart.php
$query = "SELECT * FROM tb_cart WHERE email = '$email'";
$sql = mysqli_query($conn, $query);

// Fungsi untuk menghapus produk dari keranjang
function removeProductFromCart($indexCart)
{
  global $conn;
  $indexCart = mysqli_real_escape_string($conn, $indexCart);

  // Hapus produk berdasarkan indexCart
  $deleteQuery = "DELETE FROM tb_cart WHERE indexCart = $indexCart";
  mysqli_query($conn, $deleteQuery);
}

// Periksa apakah parameter remove diatur dan panggil fungsi removeProductFromCart
if (isset($_GET['remove'])) {
  removeProductFromCart($_GET['remove']);
}

// Proses order saat tombol Beli Sekarang ditekan
if (isset($_POST['beli_sekarang'])) {
  // Query untuk memeriksa apakah tabel tb_cart kosong
  $checkCartQuery = "SELECT COUNT(*) as total FROM tb_cart WHERE email = '$email'";
  $checkCartResult = mysqli_query($conn, $checkCartQuery);
  $checkCartRow = mysqli_fetch_assoc($checkCartResult);
  $totalItemsInCart = $checkCartRow['total'];

  // Jika keranjang (tb_cart) kosong, tampilkan pesan
  if ($totalItemsInCart == 0) {
    echo '<script>alert("Anda harus membeli terlebih dahulu sebelum melakukan proses pembelian.");</script>';
  } else {
    // Insert data into tb_order_history for each item in tb_cart with the user's email
    $insertQuery = "INSERT INTO tb_order_history (id_produk, product_name, Quantity, subTotal, order_date, email)
                        SELECT id_produk, CartDisplayName, Quantity, subTotal, NOW(), '$email'
                        FROM tb_cart WHERE email = '$email'";

    mysqli_query($conn, $insertQuery);

    // Hapus semua item dari keranjang (tb_cart)
    $delete_cart_query = "DELETE FROM tb_cart WHERE email = '$email'";
    mysqli_query($conn, $delete_cart_query);

    // Tampilkan notifikasi berhasil order
    echo '<script>alert("Berhasil melakukan order! Pesanan Anda sedang diproses.");</script>';
  }
}


// Proses update quantity (same as before)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $updatedQuantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
  $indexCart = isset($_POST['indexCart']) ? $_POST['indexCart'] : 0;

  // Update database (same as before)
  $updateQuery = "UPDATE tb_cart
                   SET Quantity = $updatedQuantity,
                       subTotal = $updatedQuantity * (SELECT price FROM tb_produk WHERE id_produk = tb_cart.id_produk)
                   WHERE indexCart = $indexCart AND email = '$email'";
  $result = mysqli_query($conn, $updateQuery);

  // (Optional) display message (same as before)
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
  <style>
    #quantity_cart {
      width: 70px;
    }
  </style>
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
  </div>
  <!--</div>-->

  <!------------------------------ cart items details------------------------------>

  <div class="small-container cart-page">
    <table>
      <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Subtotal</th>
        <th>Action</th>
      </tr>

      <?php
      // Tampilkan data produk dari tb_cart
      $query = "SELECT c.*, p.price FROM tb_cart c INNER JOIN tb_produk p ON c.id_produk = p.id_produk WHERE c.email = '$email'";
      $sql = mysqli_query($conn, $query);

      // Display cart items
      while ($row = mysqli_fetch_assoc($sql)) {
        echo "<tr>";
        echo "<td>";
        echo '<div class="cart-info">';
        echo "<img src='{$row['ImageCart']}' />";
        echo '<div>';
        echo "<p>{$row['CartDisplayName']}</p>";
        echo "<small>Price: Rp. {$row['price']}</small><br />";
        echo '</div>';
        echo '</div>';
        echo '</td>';
        // Form to update quantity
        echo "<td>";
        echo "<form method='post' action='cart.php'>";
        echo "<input id='quantity_cart' type='number' name='quantity' value='{$row['Quantity']}' onchange='updateQuantity(this)' min='1' max='99'/>";
        echo "<input type='hidden' name='indexCart' value='{$row['indexCart']}' />";
        echo "</form>";
        echo "</td>";
        echo "<td>{$row['subTotal']}</td>";
        echo "<td><a href='cart.php?remove={$row['indexCart']}'>Remove</a></td>";
        echo "</tr>";
      }
      ?>

    </table>

    <div class="total-price">
      <table>
        <tr>
          <td>Total</td>
          <td>
            <?php
            // Calculate and display total price
            $total_query = "SELECT SUM(subTotal) as total FROM tb_cart WHERE email = '$email'";
            $total_result = mysqli_query($conn, $total_query);
            $total_row = mysqli_fetch_assoc($total_result);
            echo $total_row['total'];
            ?>
          </td>
        </tr>
      </table>
    </div>
    <form method="POST">
      <input type="hidden" name="beli_sekarang" value="1">
      <button type="submit" name="beli_sekarang" class="btn">Beli Sekarang</button>
    </form>

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
    // Tambahkan event listener pada saat dokumen dimuat
    document.addEventListener("DOMContentLoaded", function() {
      // Loop melalui setiap elemen input quantity
      document.querySelectorAll('input[name="quantity"]').forEach(input => {
        // Tambahkan event listener untuk event keypress
        input.addEventListener('keypress', function(event) {
          // Periksa apakah tombol yang ditekan adalah tombol "Enter"
          if (event.key === 'Enter') {
            // Panggil fungsi updateQuantity saat tombol "Enter" ditekan
            updateQuantity(this);
          }
        });
      });
    });

    const updateQuantity = (input) => {
      const quantity = input.value;
      const indexCart = input.parentNode.querySelector('input[name="indexCart"]').value;

      // Buat request AJAX untuk memperbarui kuantitas
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'cart.php');
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = () => {
        if (xhr.status === 200) {
          // Perbarui subTotal di DOM
          const subTotalElement = document.querySelector(`#subTotal${indexCart}`);
          subTotalElement.textContent = xhr.responseText;

          // Hitung ulang total price
          updateTotalPrice();
        } else {
          console.error('Terjadi kesalahan saat memperbarui kuantitas!');
        }
      };
      xhr.send(`quantity=${quantity}&indexCart=${indexCart}`);
    };

    // Jalankan fungsi saat halaman dimuat
    updateTotalPrice();
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