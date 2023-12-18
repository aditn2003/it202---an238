<?php
require(__DIR__ . "/partials/nav.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid product ID";
    exit;
}

try {
    $db = getDB();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch Product Details by ID
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(":id", $_GET['id']);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found";
        exit;
    }
} catch(PDOException $e) {
    // Handle Database Connection Error
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
</head>
<body>
    <h1>Product Details</h1>

   
<div>
    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
    <p>Category: <?php echo htmlspecialchars($product['category']); ?></p>
    <p>Description: <?php echo htmlspecialchars($product['description']); ?></p>
    <p>Stock: <?php echo htmlspecialchars($product['stock']); ?></p>
    <p>Price: <?php echo htmlspecialchars($product['unit_price']); ?></p>
    <p>Visibility: <?php echo ($product['visibility'] == 1) ? 'Visible' : 'Not Visible'; ?></p>
</div>

</body>
</html>
