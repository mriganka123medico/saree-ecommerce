<?php
session_start();

// Add to cart logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
    $product = [
        'name' => $_POST['product_name'],
        'price' => $_POST['product_price'],
        'quantity' => 1
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['name'] == $product['name']) {
            $_SESSION['cart'][$key]['quantity'] += 1;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = $product;
    }
}

// Remove from cart
if (isset($_GET['remove'])) {
    $remove_name = $_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['name'] == $remove_name) {
            unset($_SESSION['cart'][$key]);
        }
    }
    // Reindex array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Your Cart</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Saree</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
            ?>
            <tr>
<tr>
    <td colspan="5" class="text-end">
        <button id="rzp-button1" class="btn btn-primary">Pay with Razorpay</button>
    </td>
</tr>

                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>₹<?= number_format($item['price']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>₹<?= number_format($subtotal) ?></td>
                <td><a href="cart.php?remove=<?= urlencode($item['name']) ?>" class="btn btn-danger btn-sm">Remove</a></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                <td colspan="2"><strong>₹<?= number_format($total) ?></strong></td>
            </tr>
        </tbody>
    </table>
    <a href="index.html" class="btn btn-secondary">Continue Shopping</a>
    <a href="checkout.php" class="btn btn-success">Checkout</a>
    <?php else: ?>
        <p>Your cart is empty.</p>
        <a href="index.html" class="btn btn-primary">Shop Now</a>
    <?php endif; ?>
</div>

</body>
</html>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "YOUR_RAZORPAY_KEY_ID", // Replace with your Razorpay key ID
        "amount": <?= $total * 100 ?>, // Razorpay works in paise
        "currency": "INR",
        "name": "Your Saree Store",
        "description": "Payment for your order",
        "image": "https://yourdomain.com/logo.png",
        "handler": function (response){
            alert("Payment successful! Razorpay Payment ID: " + response.razorpay_payment_id);
            // Redirect to success page or save to database
            window.location.href = "payment_success.php?payment_id=" + response.razorpay_payment_id;
        },
        "prefill": {
            "name": "",
            "email": "",
            "contact": ""
        },
        "theme": {
            "color": "#F37254"
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('rzp-button1').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
</script>



