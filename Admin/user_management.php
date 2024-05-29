<?php
include '../koneksi.php';

// Set the number of products to display per page
$userPerPage = 9;

// Get the current page number from the URL parameter
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the OFFSET for the SQL query based on the current page
$offset = ($currentPage - 1) * $userPerPage;

// Query to get the total number of User
$totalUserQuery = "SELECT COUNT(*) as total FROM tb_user_login";
$totalUserResult = mysqli_query($conn, $totalUserQuery);
$totalUserRow = mysqli_fetch_assoc($totalUserResult);
$totalUser = $totalUserRow['total'];

// Retrieve all user accounts
$totalPages = ceil($totalUser / $userPerPage);
$query = "SELECT * FROM tb_user_login LIMIT $userPerPage OFFSET $offset";
$result = mysqli_query($conn, $query);

// Check if a search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM tb_user_login WHERE username LIKE '%$searchTerm%' LIMIT $userPerPage OFFSET $offset";
}

$result = mysqli_query($conn, $query);
?>
<div class="main-top">
    <h1>User Management</h1>
</div>
<section class="main-course">
    <h1>User List</h1>
    <div class="course-box1">

        <!-- Search user -->
        <form method="GET" action="">
            <input class="inputSearch" type="text" name="search" placeholder="Search by Username">
            <button class="searchBut" type="submit">Search</button>
        </form>

        <!-- Display list of User -->
        <table border="1" class="tabeluser">
            <tr>
                <th id="um_username">Username</th>
                <th id="um_email">Email</th>
                <th id="um_delete">Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td id="um_username"><?php echo htmlspecialchars($row['username']); ?></td>
                    <td id="um_email"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td id="um_delete">
                        <form method="get" action="dashboard_user.php">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                            <button id="um_del_but" type="submit" name="delete_user" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Pagination links -->
        <div class="pagination">
            <select onchange="loadContent('user_management.php?page=' + this.value + '<?php if (isset($_GET['search'])) echo "&search=" . urlencode($_GET['search']); ?>', 'main');">
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