<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Include the database configuration
include 'config.php';

// Initialize variables to store form data and errors
$card_number = $expiry_date = $cvv = "";
$card_number_err = $expiry_date_err = $cvv_err = $payment_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate card number
    if (empty(trim($_POST["card_number"]))) {
        $card_number_err = "Please enter your card number.";
    } else {
        $card_number = trim($_POST["card_number"]);
        if (!preg_match('/^\d{16}$/', $card_number)) {
            $card_number_err = "Invalid card number. Please enter a 16-digit number.";
        }
    }

    // Validate expiry date
    if (empty(trim($_POST["expiry_date"]))) {
        $expiry_date_err = "Please enter your card's expiry date.";
    } else {
        $expiry_date = trim($_POST["expiry_date"]);
        if (!preg_match('/^\d{2}\/\d{2}$/', $expiry_date)) {
            $expiry_date_err = "Invalid expiry date. Please use the format MM/YY.";
        }
    }

    // Validate CVV
    if (empty(trim($_POST["cvv"]))) {
        $cvv_err = "Please enter your card's CVV.";
    } else {
        $cvv = trim($_POST["cvv"]);
        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            $cvv_err = "Invalid CVV. Please enter a 3 or 4-digit number.";
        }
    }

    // Check if all inputs are valid
    if (empty($card_number_err) && empty($expiry_date_err) && empty($cvv_err)) {
        // Simulate payment processing
        $payment_status = "success"; // Assume payment is successful

        if ($payment_status == "success") {
            // Update order status to 'paid'
            $order_id = $_SESSION['order_id'];
            $sql = "UPDATE orders SET status = 'paid' WHERE id = :order_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':order_id', $order_id);

            if ($stmt->execute()) {
                // Redirect to customer dashboard
                header("location: customer_dashboard.php");
                exit;
            } else {
                $payment_err = "Something went wrong. Please try again later.";
            }
        } else {
            $payment_err = "Payment failed. Please try again.";
        }
    }
}

// Close connection
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .payment-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .payment-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-form">
            <h2>Payment</h2>
            <?php if (!empty($payment_err)): ?>
                <div class="error"><?php echo $payment_err; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="card_number">Card Number:</label>
                    <input type="text" id="card_number" name="card_number" value="<?php echo $card_number; ?>">
                    <?php if (!empty($card_number_err)): ?>
                        <span class="error"><?php echo $card_number_err; ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="expiry_date">Expiry Date (MM/YY):</label>
                    <input type="text" id="expiry_date" name="expiry_date" value="<?php echo $expiry_date; ?>">
                    <?php if (!empty($expiry_date_err)): ?>
                        <span class="error"><?php echo $expiry_date_err; ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" value="<?php echo $cvv; ?>">
                    <?php if (!empty($cvv_err)): ?>
                        <span class="error"><?php echo $cvv_err; ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <input type="submit" value="Pay Now">
                </div>
            </form>
        </div>
    </div>
</body>
</html>