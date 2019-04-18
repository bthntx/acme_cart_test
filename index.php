<?php
require 'Cart.php';

//init catalog from file
try {
    $json = file_get_contents('catalog.json');
    $catalog = json_decode($json, true)["catalog"];

    /* Important! Should be sorted by moreThan ASC */
    $json = file_get_contents('shipping.json');
    $shippingRates = json_decode($json, true)["rates"];

    $json = file_get_contents('offers.json');
    $offersDiscounts = json_decode($json, true)["offers"];

    $cart = new Cart($catalog, $shippingRates, $offersDiscounts);


    $cart->addItem("B01");
    $cart->addItem("G01");
    $cart->printCart();
    echo '------------------------'."\n"."\n";
    $cart->clearCart();
    $cart->addItem("R01");
    $cart->addItem("R01");
    $cart->printCart();
    echo '------------------------'."\n"."\n";

    $cart->clearCart();
    $cart->addItem("R01");
    $cart->addItem("G01");
    $cart->printCart();
    echo '------------------------'."\n"."\n";
    $cart->clearCart();

    $cart->addItem("R01");
    $cart->addItem("R01");
    $cart->addItem("R01");
    $cart->addItem("B01");
    $cart->addItem("B01");
    $cart->printCart();
    echo '------------------------'."\n"."\n";


} catch (Exception $e) {
    print_r($e);
}




