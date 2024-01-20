<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
require(__DIR__ . "/partials/nav.php");
$selectedSort = '';
try {
    // Connect to the database
    $db = getDB();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch distinct categories from the database
    $categoryStmt = $db->prepare("SELECT DISTINCT category FROM products");
    $categoryStmt->execute();
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

    // Check if a search query is provided
    // Check if a search query is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];

    // Modify your SQL query to include search conditions
    $sql = "SELECT * FROM products WHERE 
            (name LIKE :search OR 
            category LIKE :search OR 
            description LIKE :search) AND
            visibility = 1"; // Consider only visible products

    // Execute the modified query with the search parameter
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);


    } else {
        // Initialize $selectedCategory to an empty string
        $selectedCategory = '';

        // Check if a category filter is applied
        $selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;

        // Check if a price sort is applied
        $selectedSort = isset($_GET['sort']) ? $_GET['sort'] : null;

        // Construct the SQL query based on selected category, visibility, and sort
        $sql = "SELECT * FROM products WHERE visibility = 1"; // Consider only visible products
        $params = array();

        if ($selectedCategory) {
            $sql .= " AND category = :category";
            $params[':category'] = $selectedCategory;
        }

        if ($selectedSort === 'price_asc') {
            $sql .= " ORDER BY unit_price ASC";
        } elseif ($selectedSort === 'price_desc') {
            $sql .= " ORDER BY unit_price DESC";
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Output any connection errors
    echo "Connection failed: " . $e->getMessage();
    // Exit or handle the error appropriately
    exit;
}

$cartItems = [];
try {
    $db = getDB();
    $query = "SELECT * FROM cart";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>

<h1>Shop</h1>
<form method="GET" action="shop.php">
    <label for="search">Search:</label>
    <input type="text" name="search" id="search" placeholder="Enter keywords...">
    <button type="submit">Search</button>
</form>

<form method="GET">
    <label for="category">Filter by Category:</label>
    <select name="category" id="category">
        <option value="">All Categories</option>
        <?php
        // Initialize $selectedCategory to an empty string
        $selectedCategory = '';

        foreach ($categories as $category):
            // Check if $selectedCategory is set, and if it matches the current category
            $isSelected = ($selectedCategory !== '' && $selectedCategory === $category);
        ?>
            <option value="<?php echo $category; ?>" <?php echo $isSelected ? 'selected' : ''; ?>>
                <?php echo $category; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="sort">Sort by Price:</label>
    <select name="sort" id="sort">
        <option value="">No Sorting</option>
        <option value="price_asc" <?php echo ($selectedSort === 'price_asc') ? 'selected' : ''; ?>>
            Price Low to High
        </option>
        <option value="price_desc" <?php echo ($selectedSort === 'price_desc') ? 'selected' : ''; ?>>
            Price High to Low
        </option>
    </select>

    <button type="submit">Filter & Sort</button>
</form>


<div class="product-container">
    <?php foreach ($products as $product): ?>
        <div class="product">
            <h3><?php echo $product['name']; ?></h3>
            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?> Image">
            <p>Category: <?php echo $product['category']; ?></p>
            <a href="productdetail.php?id=<?php echo $product['id']; ?>">More Info</a>

            <?php
            // Check if the product is already in the cart
            $isInCart = false;
            foreach ($cartItems as $cartItem) {
                if ($cartItem['product_id'] == $product['id']) {
                    $isInCart = true;
                    break;
                }
            }

            // Display "Add to Cart" button conditionally
            if (!$isInCart) {
                echo '<a href="cart.php?action=add&product_id=' . $product['id'] . '&name=' . urlencode($product['name']) . '&price=' . $product['unit_price'] . '">Add to Cart</a>';
            } else {
                echo '<span>Already in Cart</span>';
            }
            ?>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($products)): ?>
    <p>No results available</p>
<?php endif; ?>

</body>
</html>
