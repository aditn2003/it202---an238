<?php function generatePromoCode($discountPercentage, $expirationDate) {
    // Generate a unique promo code, for example, using a combination of letters and numbers
    $promoCode = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);

    // Store the promo code and associated details in the database
    // Insert into your promo code table
    $db = getDB(); // Assuming you have a function to get a database connection
    $query = "INSERT INTO promo_codes (code, discount_percentage, expiration_date) 
              VALUES (:code, :discount_percentage, :expiration_date)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':code' => $promoCode,
        ':discount_percentage' => $discountPercentage,
        ':expiration_date' => $expirationDate,
    ]);

    return $promoCode;
}

// promo_code_functions.php

function applyPromoCode($promoCode) {
    // Check if the promo code is valid and retrieve its details
    $db = getDB();
    $query = "SELECT * FROM promo_codes WHERE code = :code AND expiration_date >= NOW()";
    $stmt = $db->prepare($query);
    $stmt->execute([':code' => $promoCode]);
    $promoDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($promoDetails) {
        // Apply the discount to the total value of the cart
        $discountPercentage = $promoDetails['discount_percentage'];
        $_SESSION['promo_code'] = $promoDetails['code']; // Store applied promo code in the session

        // Retrieve cart items from the database
        $cartItems = getCartItems();
        $total = 0;

        // Update prices in the cart based on the discount percentage
        foreach ($cartItems as &$item) {
            $item['price'] *= (1 - $discountPercentage / 100);
            $total += $item['price'] * $item['quantity'];
        }

        // Store the updated cart in the session or update the database as needed
        $_SESSION['cart'] = $cartItems;

        // Optionally, store promo code details in the session
        $_SESSION['promo_code_details'] = $promoDetails;
    } else {
        // Handle invalid or expired promo code
        echo "Invalid or expired promo code.";
    }
}

