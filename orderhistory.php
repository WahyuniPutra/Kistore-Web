<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['email']) || $_SESSION['status'] !== 'login') {
    echo '<script>alert("Login terlebih dahulu.");</script>';
    echo '<script>window.location.href="account.php";</script>';
    exit();
}

// Get the email of the logged-in user
$email = $_SESSION['email'];

// Tampilkan data produk dari tb_order_history di halaman orderhistory.php berdasarkan email
$query = "SELECT * FROM tb_order_history WHERE email = ?"; // Use prepared statement
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order History - Redstore</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
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

    <div class="small-container cart-page">
        <h2>Order History</h2>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Order Date</th>
            </tr>

            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['id_order']}</td>";
                echo "<td>{$row['product_name']}</td>";
                echo "<td>{$row['Quantity']}</td>";
                echo "<td>{$row['subtotal']}</td>";
                echo "<td>{$row['order_date']}</td>";
                echo "</tr>";
            }
            ?>

        </table>
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