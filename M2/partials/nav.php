<?php
//Note: this is to resolve cookie issues with port numbers
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    $domain = explode(":", $domain)[0];
}
$localWorks = true; //some people have issues with localhost for the cookie params
//if you're one of those people make this false

//this is an extra condition added to "resolve" the localhost issue for the session cookie

require_once(__DIR__ . "/../lib/functions.php");

?> 
<nav>
<link rel="stylesheet" href="style.css">
    <ul>
        <?php if (is_logged_in()) : ?>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="cart.php">Cart</a></li>
        <?php endif; ?>
        <?php if (!is_logged_in()) : ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="register.php">Register</a></li>
            
        <?php endif; ?>
        <?php if (has_role('Admin')) : ?>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="edit_product.php">Edit Product</a></li>

        <?php endif; ?>
        <?php if (is_logged_in()) : ?>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>