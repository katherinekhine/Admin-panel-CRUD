<?php
session_start();
include 'component/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_FILES['product_image']['name'] ?? '';
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'] ?? '';
    $product_image_folder = 'uploaded_img/' . $product_image;

    if (empty($product_name) || empty($product_price) || empty($product_image)) {
        $_SESSION['message'][] = 'please fill out all fields';
    } else {
        $insert = "INSERT INTO products(name, price, image) VALUES('$product_name', '$product_price', '$product_image')";
        $upload = mysqli_query($conn, $insert);
        if ($upload) {
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
            $_SESSION['message'][] = 'new product added successfully';
        } else {
            $_SESSION['message'][] = 'could not add the product';
        }
    }

    // Redirect to the same page to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = "DELETE FROM products WHERE id = $id";
    $result = mysqli_query($conn, $delete);
    if ($result) {
        $_SESSION['message'][] = 'Product deleted successfully';
        header('location:admin-page.php');
        exit();
    } else {
        $_SESSION['message'][] = 'Error deleting record: ' . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin page</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php
    if (isset($_SESSION['message'])) {
        foreach ($_SESSION['message'] as $msg) {
            echo '<span class="message">' . htmlspecialchars($msg) . '</span>';
        }
        // Clear the messages after displaying
        unset($_SESSION['message']);
    }
    ?>

    <div class="container">

        <div class="admin-product-form-container">

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <h3>add a new product</h3>
                <input type="text" placeholder="enter product name" name="product_name" class="box">
                <input type="number" placeholder="enter product price" name="product_price" class="box">
                <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box">
                <input type="submit" class="btn" name="add_product" value="add product">
            </form>

        </div>

        <?php
        $select = mysqli_query($conn, "SELECT * FROM products");
        ?>

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>product image</th>
                        <th>product name</th>
                        <th>product price</th>
                        <th>action</th>
                    </tr>
                </thead>
                <?php while ($row = mysqli_fetch_assoc($select)) { ?>
                    <tr>
                        <td><img src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" height="100" alt=""></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>$<?php echo htmlspecialchars($row['price']); ?>/-</td>
                        <td>
                            <a href="admin-update.php?edit=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-edit"></i> edit </a>
                            <a href="admin-page.php?delete=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-trash"></i> delete </a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</body>

</html>