<?php
session_start();

// 1. Check login and order session
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_SESSION['order_id'])) {
    header("location: customer_dashboard.php");
    exit;
}

include 'config.php';

$order_id = $_SESSION['order_id'];
$error = "";
$success = "";

// 2. Fetch Order Details to Calculate Total
$sql = "SELECT o.id, o.quantity, s.name, s.price 
        FROM orders o 
        JOIN services s ON o.service_id = s.id 
        WHERE o.id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Invalid Order.";
    exit;
}

$total_amount = $order['price'] * $order['quantity'];

// 3. Handle Payment Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'];
    $new_status = 'Paid'; // Default for online payments

    // Simulation Logic
    if ($payment_method == 'cod') {
        $new_status = 'Order Placed'; // COD orders aren't "Paid" yet
    } 
    
    // Simulate Card Validation (Basic)
    if ($payment_method == 'card') {
        if (empty($_POST['card_number']) || empty($_POST['cvv'])) {
            $error = "Please enter valid card details.";
        }
    }

    if (empty($error)) {
        // Update Order Status
        $update_sql = "UPDATE orders SET status = :status WHERE id = :order_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->bindParam(':status', $new_status);
        $update_stmt->bindParam(':order_id', $order_id);

        if ($update_stmt->execute()) {
            // Clear the order session so they don't pay again
            unset($_SESSION['order_id']);
            
            // Show success page (or redirect)
            echo "<script>
                alert('Payment Successful! Status: $new_status');
                window.location.href = 'customer_dashboard.php';
            </script>";
            exit;
        } else {
            $error = "Transaction failed. Please try again.";
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">
    <div class="row g-5">
        <div class="col-md-5 col-lg-4 order-md-last">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary fw-bold">Order Summary</span>
                <span class="badge bg-primary rounded-pill">1 Item</span>
            </h4>
            <ul class="list-group mb-3 shadow-sm border-0">
                <li class="list-group-item d-flex justify-content-between lh-sm border-0 border-bottom">
                    <div>
                        <h6 class="my-0 fw-bold"><?php echo htmlspecialchars($order['name']); ?></h6>
                        <small class="text-muted">Quantity: <?php echo $order['quantity']; ?></small>
                    </div>
                    <span class="text-muted">$<?php echo number_format($order['price'], 2); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-light border-0">
                    <div class="text-success">
                        <h6 class="my-0">Promo Code</h6>
                        <small>EXAMPLECODE</small>
                    </div>
                    <span class="text-success">-$0.00</span>
                </li>
                <li class="list-group-item d-flex justify-content-between border-0 fw-bold fs-5">
                    <span>Total (USD)</span>
                    <strong>$<?php echo number_format($total_amount, 2); ?></strong>
                </li>
            </ul>
        </div>

        <div class="col-md-7 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 ps-4">
                    <h4 class="mb-0 fw-bold">Payment Method</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="post" action="" id="paymentForm">
                        <div class="my-3">
                            <div class="form-check mb-3 p-3 border rounded bg-light cursor-pointer">
                                <input id="credit" name="payment_method" type="radio" class="form-check-input" value="card" checked onclick="togglePayment('card')">
                                <label class="form-check-label fw-bold w-100" for="credit">
                                    <i class="fas fa-credit-card me-2 text-primary"></i> Credit / Debit Card
                                </label>
                            </div>
                            <div class="form-check mb-3 p-3 border rounded bg-light cursor-pointer">
                                <input id="upi" name="payment_method" type="radio" class="form-check-input" value="upi" onclick="togglePayment('upi')">
                                <label class="form-check-label fw-bold w-100" for="upi">
                                    <i class="fas fa-qrcode me-2 text-success"></i> UPI / QR Code
                                </label>
                            </div>
                            <div class="form-check mb-3 p-3 border rounded bg-light cursor-pointer">
                                <input id="cod" name="payment_method" type="radio" class="form-check-input" value="cod" onclick="togglePayment('cod')">
                                <label class="form-check-label fw-bold w-100" for="cod">
                                    <i class="fas fa-money-bill-wave me-2 text-success"></i> Cash on Delivery
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div id="card-section">
                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name on card</label>
                                    <input type="text" class="form-control" placeholder="Full name as displayed on card">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Credit card number</label>
                                    <input type="text" name="card_number" class="form-control" placeholder="0000 0000 0000 0000">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Expiration</label>
                                    <input type="text" class="form-control" placeholder="MM/YY">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" name="cvv" class="form-control" placeholder="123">
                                </div>
                            </div>
                        </div>

                        <div id="upi-section" style="display: none;" class="text-center py-4">
                            <p class="mb-3 text-muted">Scan this QR code to pay instantly</p>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ExamplePayment" class="img-thumbnail mb-3">
                            <div class="w-50 mx-auto">
                                <input type="text" class="form-control text-center" placeholder="Enter Transaction ID (Simulated)">
                            </div>
                        </div>

                        <div id="cod-section" style="display: none;" class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> You can pay via Cash or UPI to the delivery agent when your clothes are delivered.
                        </div>

                        <hr class="my-4">

                        <button class="btn btn-nav-cta w-100 btn-lg" type="submit">
                            Confirm Payment <span id="pay-btn-amount">$<?php echo number_format($total_amount, 2); ?></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePayment(method) {
        // Hide all sections
        document.getElementById('card-section').style.display = 'none';
        document.getElementById('upi-section').style.display = 'none';
        document.getElementById('cod-section').style.display = 'none';

        // Show selected section
        if (method === 'card') {
            document.getElementById('card-section').style.display = 'block';
            document.getElementById('pay-btn-amount').innerText = "$<?php echo number_format($total_amount, 2); ?>";
        } else if (method === 'upi') {
            document.getElementById('upi-section').style.display = 'block';
            document.getElementById('pay-btn-amount').innerText = " via UPI";
        } else if (method === 'cod') {
            document.getElementById('cod-section').style.display = 'block';
            document.getElementById('pay-btn-amount').innerText = " on Delivery";
        }
    }
</script>

<?php include 'footer.php'; ?>