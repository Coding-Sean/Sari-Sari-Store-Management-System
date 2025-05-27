<?php
session_start();

class Cart {
    public static function add($product) {
        $_SESSION['cart'][] = $product;
    }

    public static function remove($index) {
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex the array
        }
    }

    public static function get() {
        return $_SESSION['cart'] ?? [];
    }

    public static function clear() {
        unset($_SESSION['cart']);
    }
}
?>