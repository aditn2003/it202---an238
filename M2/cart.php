
<?php
require_once(__DIR__ . "/partials/nav.php");
require_once 'get_promocode.php';
require_once 'apply_promocode.php';
// Check if product is being added to the cart
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];
    $name = $_GET['name'];
    $price = $_GET['price'];

    // Ensure that product_id, name, and price are not empty
    if (!empty($productId) && !empty($name) && !empty($price)) {
        try {
            $db = getDB();
            $query = "INSERT INTO cart (product_id, name, price, quantity) VALUES (:product_id, :name, :price, 1)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":product_id", $productId);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":price", $price);
            $stmt->execute();

            echo "Product added to cart successfully!";
        } catch (PDOException $e) {
            echo "Error adding product to cart: " . $e->getMessage();
        }
    } else {
        echo "Invalid product details.";
    }
}

function getCartItems() {
    // Check if the cart is set in the session
    if (isset($_SESSION['cart'])) {
        return $_SESSION['cart'];
    } else {
        // If the cart is not set, return an empty array
        return [];
    }
}

// Handle removing a single item from the cart
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    try {
        $db = getDB();
        $query = "DELETE FROM cart WHERE product_id = :product_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":product_id", $productId);
        $stmt->execute();

        echo "Product removed from cart successfully!";
    } catch (PDOException $e) {
        echo "Error removing product from cart: " . $e->getMessage();
    }
}

// Handle clearing the entire cart
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    try {
        $db = getDB();
        $query = "DELETE FROM cart";
        $stmt = $db->prepare($query);
        $stmt->execute();

        echo "Cart cleared successfully!";
    } catch (PDOException $e) {
        echo "Error clearing cart: " . $e->getMessage();
    }
}

// Handle updating the quantity of a specific item in the cart
if (isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];
    $newQuantity = isset($_GET['quantity']) ? max(0, $_GET['quantity']) : 1;

    try {
        $db = getDB();
        $query = "UPDATE cart SET quantity = :quantity WHERE product_id = :product_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":quantity", $newQuantity);
        $stmt->bindParam(":product_id", $productId);
        $stmt->execute();

        echo "Quantity updated successfully!";
    } catch (PDOException $e) {
        echo "Error updating quantity: " . $e->getMessage();
    }
}

// Retrieve cart items from the database
$cartItems = [];
try {
    $db = getDB();
    $query = "SELECT * FROM cart";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle any errors here...
    echo "Connection failed: " . $e->getMessage();
    exit;
}
if (isset($_POST['apply_promo_code'])) {
    $promoCode = $_POST['promo_code']; // Adjust as needed based on your form input name
    applyPromoCode($promoCode);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
</head>
<body>
    <h1>Shopping Cart</h1>

    <table border="1">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($cartItems as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['price']; ?></td>
                <td>
                    <form method="GET" action="cart.php">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                        <input type="submit" value="Update">
                    </form>
                </td>
                <td><?php echo number_format($subtotal, 2); ?></td>
                <td><a href="cart.php?action=remove&product_id=<?php echo $item['product_id']; ?>">Remove</a></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="4"><strong>Total</strong></td>
                <td><strong><?php echo number_format($total, 2); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <form method="GET">
        <input type="hidden" name="action" value="clear">
        <input type="submit" value="Clear Cart">
    </form>
    <form method="POST" action="apply_promocode.php">
    <label for="promo_code">Promo Code:</label>
    <input type="text" name="promo_code" id="promo_code" placeholder="Enter promo code">
    <button type="submit">Apply Promo Code</button>
</form>
</body>
</html>
