<?php
include '../koneksi.php';

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari form
    $id_produk = $_POST['id_produk'];
    $articleType = $_POST['articleType'];
    $usageType = $_POST['usageType'];
    $productDisplayName = $_POST['productDisplayName'];
    $price = $_POST['price'];

    // Inisialisasi variabel image
    $image = "";

    // Ambil nilai gambar
    if (!empty($_FILES['image']['name'])) {
        // Jika pengguna mengunggah file gambar
        $image = $_FILES['image']['name'];
        // Pindahkan file gambar ke folder "images"
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        // Set nilai $image sesuai dengan path lengkap file gambar
        $image = "images/" . $image;
    } elseif (!empty($_POST['image_link'])) {
        // Jika pengguna memasukkan tautan gambar, gunakan tautan tersebut
        $image_link = $_POST['image_link'];
        $image = $image_link;
    } // Tidak perlu else, karena jika tidak ada gambar yang diunggah atau dihubungkan, $image akan tetap kosong

    // Query untuk memperbarui data pada tabel tb_produk
    $query = "UPDATE tb_produk 
              SET articleType = '$articleType', 
                  usageType = '$usageType', 
                  productDisplayName = '$productDisplayName', 
                  price = '$price'";

    // Tambahkan kondisi jika $image tidak kosong
    if ($image !== "") {
        // Jika $image tidak kosong, tambahkan kolom image pada query
        $query .= ", image = '$image'";
    }

    $query .= " WHERE id_produk = '$id_produk'";

    // Jalankan query
    if (mysqli_query($conn, $query)) {
        echo '<script>alert("Data berhasil diperbarui"); window.location.href = "dashboard.php";</script>';
        exit(); // Penting untuk menghentikan eksekusi script setelah alert
    } else {
        echo '<script>alert("Data Gagal Diperbarui"); window.location.href = "dashboard.php";</script>';
        exit(); // Penting untuk menghentikan eksekusi script setelah alert
    }
}

// Jika ID Produk telah diberikan melalui GET, ambil data produk tersebut untuk ditampilkan pada form
if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];
    // Query untuk mengambil data produk berdasarkan ID
    $query_get_produk = "SELECT * FROM tb_produk WHERE id_produk = '$id_produk'";
    $result_get_produk = mysqli_query($conn, $query_get_produk);
    $row_produk = mysqli_fetch_assoc($result_get_produk);
}

?>

<div class="main-top">
    <h1>Edit Produk</h1>

</div>
<section class="main-course">
    <h1>Formulir Edit Produk</h1>
    <div class="course-box1">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <!-- Input hidden untuk id_produk -->
            <input type="hidden" name="id_produk" value="<?php echo $row_produk['id_produk']; ?>">

            <label for="articleType">Article Type:</label>
            <input class="inputAtribut" type="text" id="articleType" name="articleType" value="<?php echo $row_produk['articleType']; ?>"><br>

            <label for="usageType">Usage Type:</label>
            <input class="inputAtribut" type="text" id="usageType" name="usageType" value="<?php echo $row_produk['usageType']; ?>"><br>

            <label for="productDisplayName">Product Display Name:</label>
            <input class="inputAtribut" type="text" id="productDisplayName" name="productDisplayName" value="<?php echo $row_produk['productDisplayName']; ?>"><br>

            <!-- Input file untuk unggah gambar -->
            <label for="image">Image (Upload or Link):</label>
            <input class="inputAtribut" type="file" id="image" name="image">
            <p>Atau</p>
            <input class="inputAtribut" type="text" id="image_link" name="image_link" placeholder="Link Gambar"><br>

            <label for="price">Price:</label>
            <input class="inputAtribut" type="text" id="price" name="price" value="<?php echo $row_produk['price']; ?>"><br>

            <input type="submit" value="Update">
        </form>
    </div>
    <script src="script.js"></script>
</section>