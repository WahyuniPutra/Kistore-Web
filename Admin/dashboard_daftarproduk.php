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

if (isset($_GET["remove"])) {
    global $conn;

    // Ambil id_produk dari parameter $_GET
    $id_produk = $_GET["remove"];

    // Prepare delete statement for tb_cart
    $delete_cart_query = "DELETE FROM tb_cart WHERE id_produk = ?";
    $stmt_cart = mysqli_prepare($conn, $delete_cart_query);
    mysqli_stmt_bind_param($stmt_cart, 's', $id_produk); // Bind parameter dengan id_produk

    // Prepare delete statement for tb_order_history
    $delete_order_query = "DELETE FROM tb_order_history WHERE id_produk = ?";
    $stmt_order = mysqli_prepare($conn, $delete_order_query);
    mysqli_stmt_bind_param($stmt_order, 's', $id_produk); // Bind parameter dengan id_produk

    // Prepare delete statement for tb_produk
    $delete_produk_query = "DELETE FROM tb_produk WHERE id_produk = ?";
    $stmt_produk = mysqli_prepare($conn, $delete_produk_query);
    mysqli_stmt_bind_param($stmt_produk, 's', $id_produk); // Bind parameter dengan id_produk

    // Execute delete statements
    if (mysqli_stmt_execute($stmt_cart) && mysqli_stmt_execute($stmt_order) && mysqli_stmt_execute($stmt_produk)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

// Set the number of products to display per page
$productsPerPage = 9;

// Get the current page number from the URL parameter
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the OFFSET for the SQL query based on the current page
$offset = ($currentPage - 1) * $productsPerPage;

// Query to fetch products with LIMIT and OFFSET
$query = "SELECT * FROM tb_produk LIMIT $productsPerPage OFFSET $offset";
$result = mysqli_query($conn, $query);

// Query to get the total number of products
$totalProductsQuery = "SELECT COUNT(*) as total FROM tb_produk";
$totalProductsResult = mysqli_query($conn, $totalProductsQuery);
$totalProductsRow = mysqli_fetch_assoc($totalProductsResult);
$totalProducts = $totalProductsRow['total'];

// INI LOGIKA SEARCHNYA TAPI MASIH BELUM BISA
$totalPages = ceil($totalProducts / $productsPerPage);

$query = "SELECT * FROM tb_produk LIMIT $productsPerPage OFFSET $offset";

// Check if a search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM tb_produk WHERE productDisplayName LIKE '%$searchTerm%' LIMIT $productsPerPage OFFSET $offset";
}

$result = mysqli_query($conn, $query);
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
                <h1>Daftar Produk</h1>
            </div>

            <section class="main-course">
                <h1>Tampilan Produk</h1>
                <div class="course-box1">

                    <!-- Search products -->
                    <form method="GET" action="">
                        <input class="inputSearch" type="text" name="search" placeholder="Search by Product">
                        <button class="searchBut" type="submit">Search</button>
                    </form>

                    <!-- Display list of products -->
                    <table border="1" class="tabelproduk">
                        <tr>
                            <th id="um_id_produk"><strong>Id Produk</strong></th>
                            <th id="um_display_name"><strong>Display Name:</strong></th>
                            <th id="um_price"><strong>Price</strong></th>
                            <th id="um_edit"><strong>Edit</strong></th>
                            <th id="um_delete"><strong>Delete</strong></th>
                        </tr>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td id="um_row"><?php echo "{$row['id_produk']} "; ?></td>
                                <td id="um_row"><?php echo "{$row['productDisplayName']} "; ?></td>
                                <td id="um_row"><?php echo "{$row['price']} "; ?></td>
                                <td id="um_row"> <a href="#" class="edit-btn" onclick="editProduct('<?php echo $row['id_produk']; ?>'); return false;">Edit</a></td>
                                <td id="um_row"><a href='dashboard_daftarproduk.php?remove=<?php echo $row['id_produk']; ?>' class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>

                    <!-- Pagination links -->
                    <div class="pagination">
                        <select onchange="loadContent('daftarproduk.php?page=' + this.value + '<?php if (isset($_GET['search'])) echo "&search=" . urlencode($_GET['search']); ?>', 'main');">
                            <?php
                            for ($i = 1; $i <= $totalPages; $i++) {
                                echo "<option value='$i'";
                                if ($i == $currentPage) {
                                    echo " selected";
                                }
                                echo ">$i</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </section>
        </section>
    </div>
    <script>
        function loadContent(url, isInMain) {
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    if (isInMain) {
                        document.querySelector(".main").innerHTML = data;
                    } else {
                        document.body.innerHTML = data;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
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
    <script>
        function editProduct(productId) {
            var currentPage = "<?php echo $currentPage; ?>";
            var searchParams = new URLSearchParams(window.location.search);
            var search = searchParams.toString();
            var editUrl = 'edit.php?id=' + productId + '&page=' + currentPage + '&' + search;
            loadContent(editUrl, 'main');
        }
    </script>
</body>

</html>