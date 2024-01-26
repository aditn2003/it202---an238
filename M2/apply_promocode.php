<?php
require_once 'promo_code_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $promoCode = $_POST['promo_code'];
    applyPromoCode($promoCode);
}
?>
