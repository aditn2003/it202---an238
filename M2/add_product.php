<?php
require(__DIR__ . "/partials/nav.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDB();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve product details from the form
        $name = $_POST['name'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $stock = $_POST['stock'];
        $unit_price = $_POST['unit_price'];
        $visibility = $_POST['visibility']; // Added visibility column

        // Insert the new product into the database
        $stmt = $db->prepare("INSERT INTO products (name, description, category, stock, unit_price, visibility) VALUES (:name, :description, :category, :stock, :unit_price, :visibility)");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":unit_price", $unit_price);
        $stmt->bindParam(":visibility", $visibility); // Binding visibility column
        $stmt->execute();

        echo "Product added successfully!";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
</head>
<body>
    <h1>Add Product</h1>

    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br><br>

        <label for="description">Description:</label>
        <textarea name="description"></textarea><br><br>

        <label for="category">Category:</label>
        <input type="text" name="category"><br><br>

        <label for="stock">Stock:</label>
        <input type="number" name="stock" value="0"><br><br>

        <label for="unit_price">Unit Price:</label>
        <input type="number" name="unit_price" step="0.01" required><br><br>

        <label for="visibility">Visibility:</label> <!-- Added visibility field -->
        <input type="number" name="visibility" min="0" max="1" value="1"><br><br>

        <input type="submit" value="Add Product">
    </form>
</body>
</html>