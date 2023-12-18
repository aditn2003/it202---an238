<?php
require(__DIR__ . "/partials/nav.php");

try {
    // Connect to the database
    $db = getDB();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch distinct categories from the database
    $categoryStmt = $db->prepare("SELECT DISTINCT category FROM products");
    $categoryStmt->execute();
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

    // Check if a category filter is applied
    $selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;

    // Check if a price sort is applied
    //ucid: an239
    //date: 12/17/2023
    $selectedSort = isset($_GET['sort']) ? $_GET['sort'] : null;

    // Construct the SQL query based on selected category and sort
    $sql = "SELECT * FROM products";
    $params = array();

    if ($selectedCategory) {
        $sql .= " WHERE category = :category";
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
   

} catch(PDOException $e) {
    // Output any connection errors
    echo "Connection failed: " . $e->getMessage();
    // Exit or handle the error appropriately
    exit;
}
?>

<h1>Shop</h1>

<form method="GET">
    <label for="category">Filter by Category:</label>
    <select name="category" id="category">
        <option value="">All Categories</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category; ?>" <?php echo ($selectedCategory === $category) ? 'selected' : ''; ?>>
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

<?php foreach ($products as $product): ?>
    <div>
        <h3><?php echo $product['name']; ?></h3>
        <p>Category: <?php echo $product['category']; ?></p>
        <a href="productdetail.php?id=<?php echo $product['id']; ?>">More Info</a>
        <a href="cart.php?action=add&product_id=<?php echo $product['id']; ?>&name=<?php echo urlencode($product['name']); ?>&price=<?php echo $product['unit_price']; ?>">Add to Cart</a>
    </div>
<?php endforeach; ?>
<?php if (empty($products)): ?>
    <p>No results available</p>
<?php else: ?>
    <?php foreach ($products as $product): ?>
        <!-- Display product details -->
        <!-- Your existing product display code here -->
    <?php endforeach; ?>
<?php endif; ?>