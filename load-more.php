<?php
include 'koneksi.php';
session_start();

// Tentukan berapa banyak produk yang akan dimuat setiap kali
$products_per_row = 4; // Jumlah produk per baris
$productsPerPage = $products_per_row * 3; // Jumlah produk per halaman (3 baris)

// Hitung offset berdasarkan halaman saat ini
$page = $_GET['page'];
$offset = ($page - 1) * $productsPerPage;

// Query untuk mengambil data produk dalam batch
$query = "SELECT * FROM tb_produk";
if(isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
    $query .= " WHERE productDisplayName LIKE '%$keyword%'"; // Filter berdasarkan kata kunci pencarian
}
$query .= " LIMIT $offset, $productsPerPage";
$sql = mysqli_query($conn, $query);

// Bangun tampilan produk tambahan dalam format HTML
$output = '';
$row_count = 0; // Hitung baris
while ($row_count * $products_per_row < $productsPerPage) {
    $output .= '<div class="row">';
    for ($i = 0; $i < $products_per_row; $i++) {
        if ($result = mysqli_fetch_assoc($sql)) {
            // Menyimpan ID produk
            $product_id = $result['id_produk']; // Sesuaikan dengan nama kolom ID produk di database
            
            $output .= '<div class="col-4">';
            $output .= '<a href="products-details.php?id_produk=' . $product_id . '">';
            $output .= '<img src="' . $result['image'] . '">';
            $output .= '</a>';
            $output .= '<a href="products-details.php?id_produk=' . $product_id . '">';
            $output .= '<h4>' . $result['productDisplayName'] . '</h4>';
            $output .= '</a>';
            $output .= '<div class="rating">';
            $output .= '<i class="fa fa-star"></i>';
            $output .= '<i class="fa fa-star"></i>';
            $output .= '<i class="fa fa-star"></i>';
            $output .= '<i class="fa fa-star-half-o"></i>';
            $output .= '<i class="fa fa-star-o"></i>';
            $output .= '</div>';
            $output .= '<p>Rp. ' . $result['price'] . '</p>';
            $output .= '</div>';
        }
    }
    $output .= '</div>'; // Tutup baris
    $row_count++; // Increment baris
}

// Mengembalikan output
echo $output;
?>
