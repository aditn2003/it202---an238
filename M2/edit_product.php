<?php
require_once(__DIR__ . "/partials/nav.php");

// Initialize $products variable
$products = [];

// Set initial update status message
$updateStatus = "";

try {
    $db = getDB();
    $query = "SELECT * FROM products";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle any errors here...
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Check if the form is submitted for updating
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["product_id"])) {
    $productId = $_POST["product_id"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $category = $_POST["category"];
    $stock = $_POST["stock"];
    $unitPrice = $_POST["unit_price"];
    $visibility = $_POST["visibility"];
    $imageURL = $_POST["image_url"] ?? 'default_image_url.jpg'; // Get image URL from the form

    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE products SET name = :name, description = :description, category = :category, stock = :stock, unit_price = :unit_price, image_url = :image_url WHERE id = :id");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":unit_price", $unitPrice);
        $stmt->bindParam(":image_url", $imageURL); // Add image_url to the bindParams
        $stmt->bindParam(":id", $productId);
        $stmt->execute();

        // Set the update status message
        $updateStatus = "Product updated successfully!";
    } catch (PDOException $e) {
        // Output any potential errors for debugging purposes
        echo "Error updating product: " . $e->getMessage();
        exit;
    }

    // Separate visibility update logic
    $isVisible = ($visibility == 1) ? true : false;
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE products SET visibility = :visibility WHERE id = :id");
        $stmt->bindParam(":visibility", $isVisible, PDO::PARAM_BOOL);
        $stmt->bindParam(":id", $productId);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error updating visibility: " . $e->getMessage();
        exit;
    }

    // After successfully updating the product, reset the update status message
    $updateStatus = "";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product List</title>
</head>
<body>
    <h1>Product List</h1>

    <?php if (!empty($products)) : ?>
        <ul>
            <?php foreach ($products as $product) : ?>
                <li>
                    <?php echo $product['name']; ?> | 
                    <a href="#" onclick="showEditForm(<?php echo $product['id']; ?>)">Edit</a>
                    <div id="editForm<?php echo $product['id']; ?>" style="display: none;">
                        <form method="POST">
                            <label for="name<?php echo $product['id']; ?>">Name:</label>
                            <input type="text" name="name" id="name<?php echo $product['id']; ?>" value="<?php echo $product['name']; ?>" required><br><br>
                            
                            <label for="description<?php echo $product['id']; ?>">Description:</label>
                            <textarea name="description" id="description<?php echo $product['id']; ?>"><?php echo $product['description']; ?></textarea><br><br>
                            
                            <label for="category<?php echo $product['id']; ?>">Category:</label>
                            <input type="text" name="category" id="category<?php echo $product['id']; ?>" value="<?php echo $product['category']; ?>"><br><br>
                            
                            <label for="stock<?php echo $product['id']; ?>">Stock:</label>
                            <input type="number" name="stock" id="stock<?php echo $product['id']; ?>" value="<?php echo $product['stock']; ?>"><br><br>
                            
                            <label for="unit_price<?php echo $product['id']; ?>">Unit Price:</label>
                            <input type="number" name="unit_price" id="unit_price<?php echo $product['id']; ?>" step="0.01" value="<?php echo $product['unit_price']; ?>" required><br><br>
                            
                            <label for="visibility<?php echo $product['id']; ?>">Visibility:</label>
                            <select name="visibility" id="visibility<?php echo $product['id']; ?>" required>
                                <option value="1" <?php echo ($product['visibility'] == 1) ? 'selected' : ''; ?>>True</option>
                                <option value="0" <?php echo ($product['visibility'] == 0) ? 'selected' : ''; ?>>False</option>
                            </select><br><br>

                            <label for="image_url<?php echo $product['id']; ?>">Image URL:</label>
                            <input type="text" name="image_url" id="image_url<?php echo $product['id']; ?>" value="<?php echo $product['image_url']; ?>"><br><br>

                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="submit" value="Update">
                        </form>
                    </div>
                    <p><?php echo $updateStatus; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No products found.</p>
    <?php endif; ?>

    <script>
        function showEditForm(productId) {
            var editForm = document.getElementById('editForm' + productId);
            if (editForm.style.display === 'none') {
                editForm.style.display = 'block';
            } else {
                editForm.style.display = 'none';
            }
        }
    </script>
</body>
</html>