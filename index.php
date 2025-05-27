<?php
require_once 'controller/ST_Controller.php';
require_once 'controller/SR_Controller.php';
require_once 'controller/PM_Controller.php';
require_once 'model/Cart.php';

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

switch ($controller) {
    case 'sales':
        $salesController = new SalesController();
        $salesController->$action();
        break;
    case 'report':
        $reportController = new ReportController();
        $reportController->$action();
        break;
    case 'product':
        $productController = new ProductController();
        $productController->$action();
        break;
    case 'cart':
        if ($action === 'add') {
            $product = Product::getById($_POST['id']);
            Cart::add($product);
            echo json_encode(['success' => true]);
        } elseif ($action === 'remove') {
            Cart::remove($_POST['index']);
            echo json_encode(['success' => true]);
        } elseif ($action === 'get') {
            echo json_encode(Cart::get());
        }
        break;
    default:
        $products = Product::getAll(); // Fetch products from the database
        include 'view/index.php';
        break;
}
?>

<script>
function addToCart(id) {
    fetch('index.php?controller=cart&action=add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    }).then(response => response.json())
      .then(data => {
          if (data.success) showContent('cart');
      });
}

function removeFromCart(index) {
    fetch('index.php?controller=cart&action=remove', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ index })
    }).then(response => response.json())
      .then(data => {
          if (data.success) showContent('cart');
      });
}

function showContent(section) {
    if (section === 'cart') {
        fetch('index.php?controller=cart&action=get')
            .then(response => response.json())
            .then(cart => {
                let content = cart.length === 0
                    ? `<h5>Your Cart is empty.</h5>`
                    : `<h5>Your Cart</h5>
                       <ul class="cart-list">` +
                       cart.map((item, idx) => `
                           <li>
                               <span>${item.name} - ₱${item.price}</span>
                               <button onclick="removeFromCart(${idx})">Remove</button>
                           </li>
                       `).join('') +
                       `</ul>
                       <div><b>Total: ₱${cart.reduce((sum, item) => sum + item.price, 0)}</b></div>`;
                document.getElementById('main-content').innerHTML = content;
            });
    }
}
</script>