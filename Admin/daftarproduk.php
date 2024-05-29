<?php
include '../koneksi.php';

// Set the number of products to display per page
$productsPerPage = 9;

// Get the current page number from the URL parameter
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the OFFSET for the SQL query based on the current page
$offset = ($currentPage - 1) * $productsPerPage;

// Query to fetch products with LIMIT and OFFSET
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM tb_produk WHERE productDisplayName LIKE '%$searchTerm%' LIMIT $productsPerPage OFFSET $offset";
} else {
    $query = "SELECT * FROM tb_produk LIMIT $productsPerPage OFFSET $offset";
}

$result = mysqli_query($conn, $query);

// Query to get the total number of products
$totalProductsQuery = "SELECT COUNT(*) as total FROM tb_produk";
$totalProductsResult = mysqli_query($conn, $totalProductsQuery);
$totalProductsRow = mysqli_fetch_assoc($totalProductsResult);
$totalProducts = $totalProductsRow['total'];

// Calculate total pages
$totalPages = ceil($totalProducts / $productsPerPage);
?>

<div class="main-top">
    <h1>Daftar Produk</h1>
</div>

<section class="main-course">
    <h1>Tampilan Produk</h1>

    <!-- Form for searching -->
    <div class="course-box1">
        <form method="GET" action="">
            <input class="inputSearch" type="text" name="search" placeholder="Search by Product" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>">
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
            <select onchange="loadContent('daftarproduk.php?page=' + this.value + '<?php if (isset($_GET['search'])) echo "&search=" . urlencode($_GET['search']); ?>', 'main-course');">
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