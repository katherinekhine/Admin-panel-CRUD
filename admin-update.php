<?php
session_start();
include 'component/db.php';

$id = $_GET['edit'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_image = $_FILES['product_image']['name'] ?? '';
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'] ?? '';
    $product_image_folder = 'uploaded_img/' . $product_image;

    if (empty($product_name) || empty($product_price)) {
        $_SESSION['message'][] = 'Please fill out all fields';
    } else {
        if (!empty($product_image)) {
            // Update product with new image
            $update = "UPDATE products SET name='$product_name', price='$product_price', image='$product_image' WHERE id='$id'";
            $upload = mysqli_query($conn, $update);
            if ($upload) {
                move_uploaded_file($product_image_tmp_name, $product_image_folder);
                $_SESSION['message'][] = 'Product updated successfully';
            } else {
                $_SESSION['message'][] = 'Could not update the product';
            }
        } else {
            // Update product without changing the image
            $update = "UPDATE products SET name='$product_name', price='$product_price' WHERE id='$id'";
            $upload = mysqli_query($conn, $update);
            if ($upload) {
                $_SESSION['message'][] = 'Product updated successfully';
            } else {
                $_SESSION['message'][] = 'Could not update the product';
            }
        }
    }

    // Redirect to the same page to prevent form resubmission
    header('Location: admin-page.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Update</title>
    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS File Link -->
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
        <div class="admin-product-form-container centered">
            <?php
            if ($id) {
                $select = mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'");
                if (mysqli_num_rows($select) > 0) {
                    while ($row = mysqli_fetch_assoc($select)) {
            ?>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?edit=' . $id; ?>" method="post" enctype="multipart/form-data">
                            <h3 class="title">Update the product</h3>
                            <input type="text" class="box" name="product_name" value="<?php echo htmlspecialchars($row['name']); ?>" placeholder="Enter the product name">
                            <input type="number" min="0" class="box" name="product_price" value="<?php echo htmlspecialchars($row['price']); ?>" placeholder="Enter the product price">
                            <?php
                            if (!empty($row['image'])) {
                                echo '<img src="uploaded_img/' . htmlspecialchars($row['image']) . '" alt="Current Image" height="100">';
                            }
                            ?>
                            <input type="file" class="box" name="product_image" accept="image/png, image/jpeg, image/jpg">
                            <input type="submit" value="Update product" name="update_product" class="btn">
                            <a href="admin-page.php" class="btn">Go back!</a>
                        </form>
            <?php
                    }
                } else {
                    echo '<span class="message">Product not found.</span>';
                }
            } else {
                echo '<span class="message">Invalid product ID.</span>';
            }
            ?>
        </div>
    </div>
</body>

</html>